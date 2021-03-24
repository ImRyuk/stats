<?php

namespace App\Controller;

use App\Entity\CSVFile;
use App\Entity\Departement;
use App\Entity\OldRegion;
use App\Entity\Region;
use App\Entity\StatValue;
use App\Entity\Type;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use SplFileObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;

class CSVController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/importCSV", name="import_csv")
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function importCSV(Request $request): Response
    {
        //Generating the form
        $form = $this->createFormBuilder()
            ->add('file', FileType::class,[
                    'multiple' => false,
                    'required' => true,
                    'label' => 'Fichier CSV : ',
                    'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new File([
                        'mimeTypes' => [ // We want to let upload only txt, csv or Excel files
                            'text/x-comma-separated-values',
                            'text/comma-separated-values',
                            'text/x-csv',
                            'text/csv',
                            'text/plain',
                            'application/octet-stream',
                            'application/vnd.ms-excel',
                            'application/x-csv',
                            'application/csv',
                            'application/excel',
                            'application/vnd.msexcel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                        ],
                        'mimeTypesMessage' => "Merci d'importer un fichier CSV",
                    ])
                ],
            ])
            ->add('Envoyer', SubmitType::class, [
                'attr' => ['class' => 'save form-control']])
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //We get the sent file and create
            $file = $form->get('file')->getData();
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) .'.' .$file->getClientOriginalExtension();

            //We create the file in order to stock it
            $file->move(
                $this->getParameter('CSV_directory'),
                $filename);

            if(!empty($file))
            {
                $fileCSV = new SplFileObject($this->getParameter('CSV_directory') . $filename);
                $fileCSV->setFlags(SplFileObject::READ_CSV);

                $types = [];
                $departements = [];
                $stats[] = [];

                //Reading the file
                foreach ($fileCSV as $key=>$row){

                    //the first row stores the header' titles we need
                    if($key==0)
                    {
                        $i = 3;
                        $keys = explode(";", $row[0]);

                        $defaultHeadersValues = ['Région', 'Groupement', 'Code'];
                        $percentNeeded = [80, 85,75];

                        //We check if the 3 first rows match with the default headers values, if not, we throw an error message
                        for($j = 0; $j<$i;$j++)
                        {
                            similar_text($defaultHeadersValues[$j], $keys[$j], $percent);
                            if($percent < $percentNeeded[$j])
                            {
                                //We then delete the previous created file because it doesnt suit the header configuration
                                $filesystem = new Filesystem();
                                $filesystem->remove($this->getParameter('CSV_directory').
                                    $filename);

                                //We render an error message
                                $this->addFlash('error', 'Les entêtes du fichier ne correspondent pas avec le modèle de base!');
                                return $this->redirect($this->generateUrl('import_csv'));
                            }
                        }

                        //After the third column, the columns' values are provided by the file so we create new Type
                        while ($i < count($keys))
                        {
                            $type = new Type();
                            $type->setLibelle($keys[$i]);
                            $types[] = $type;
                            $i++;
                            $this->em->persist($type);
                        }
                    }
                    else if($key>=1 && !is_null($row[0])){
                        $row = explode(";", $row[0]);

                        //If the row has a region, we create a new Region Entity
                        if(!empty($row[0])){
                            $RegionEntity = new Region();
                            $RegionEntity->setName($row[0]);
                            $currentRegion = $RegionEntity;

                            $this->em->persist($RegionEntity);
                        }

                        //We create a new Departement Entity for each departement met
                        $departement = new Departement();

                        //If the row 'Région' is empty, it means it has the same region as the past departement, so we pass it the same region
                        if(empty($row[1]))
                        {
                            $departement->setName($currentRegion->getName());
                        } else {
                            $departement->setName($row[1]);
                        }
                        $departement
                            ->setCode($row[2])
                            ->setRegion($currentRegion);
                        $departements[] = $departement;

                        $i=3;
                        $j=0;

                        //Navigating through the departements' stats
                        while($i < count($row))
                        {
                            $statValue = new StatValue();
                            $statValue
                                ->setValue($row[$i])
                                ->setDepartement($departement)
                                ->setType($types[$j]);
                            $stats[] = $statValue;
                            $i++;
                            $j++;

                            $this->em->persist($statValue);
                        }

                    }
                }

                //Affecting the old region to each departement in order to render their ecusson later
                foreach ($departements as $departement)
                {
                    $oldRegion = $this->em->getRepository(OldRegion::class)->findOneBy(['Code' => $departement->getCode()]);
                    if(!is_null($oldRegion))
                    {
                        $departement->setOldRegion($oldRegion->getEcusson());
                    }
                    $this->em->persist($departement);
                }

                //Clearing the database tables if the file is validated in order to replace the former database with new values
                $entities = [Departement::class, Region::class, StatValue::class, Type::class];

                $connection = $this->em->getConnection();
                $databasePlatform = $connection->getDatabasePlatform();
                if ($databasePlatform->supportsForeignKeyConstraints()) {
                    $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
                }
                foreach ($entities as $entity) {
                    $query = $databasePlatform->getTruncateTableSQL(
                        $this->em->getClassMetadata($entity)->getTableName());
                    $connection->executeStatement($query);
                }
                if ($databasePlatform->supportsForeignKeyConstraints()) {
                    $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
                }

                $this->em->flush();

                return $this->redirectToRoute('view_csv');
            }
        }
        return $this->render('csv/import.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/viewCSV", name="view_csv")
     * @return Response
     */
    public function view_CSV()
    {
        $types = $this->getDoctrine()->getRepository(Type::class)->findAll();
        $regions = $this->getDoctrine()->getRepository(Region::class)->findAll();
        $departements = $this->getDoctrine()->getRepository(Departement::class)->findAll();
        return $this->render('csv/view_csv.html.twig', [
            'regions' => $regions,
            'departements' => $departements,
            'types' => $types
        ]);
    }

    /**
     * @Route("/exportCSV", name="export_csv")
     * @param Request $request
     * @return BinaryFileResponse|Response
     */
    public function export_CSV(Request $request)
    {
            //Creating the file
            $file = 'Stats-gendarmeries-' . date("d-m-Y") . '.csv';
            $filename = $this->getParameter('CSV_directory') . $file;
            $newFile = new SplFileObject($filename, 'w');

            //Creating the CSV's header with predefined titles and adding the stats titles to it
            $header = ['Région', 'Groupement', 'Code'];
            $types = $this->getDoctrine()->getRepository(Type::class)->findAll();
            foreach ($types as $type)
            {
                $header[] = $type->getLibelle();
            }

            //Putting the header into the new CSV File
            $newFile->fputcsv($header, ';');

            //Getting all regions to start constructing the CSV File
            $regions = $this->getDoctrine()->getRepository(Region::class)->findAll();

            foreach ($regions as $region)
            {
                $departements = $region->getDepartements();

                for($i = 0; $i < count($departements); $i++)
                {
                    //If first loop inside a departement, we add region's name inside the CSV's column "Région"
                    $values = ($i == 0 ? [$region->getName()] : ['']);

                    //Adding all departements's values inside the CSV file(stats values, Code, Name)
                    $data = $departements[$i]->toCSV();
                    foreach ($data as $item) {
                        $values[] = $item;
                    }
                    $newFile->fputcsv($values, ';');

                }
            }
            // This should return the file to the browser as response
            $response = new BinaryFileResponse($this->getParameter('CSV_directory').$file);

            // To generate a file download, you need the mimetype of the file
            $mimeTypeGuesser = new FileinfoMimeTypeGuesser();

            // Set the mimetype with the guesser or manually
            if($mimeTypeGuesser->isGuesserSupported()){
                // Guess the mimetype of the file according to the extension of the file
                $response->headers->set('Content-Type', $mimeTypeGuesser->guessMimeType($this->getParameter('CSV_directory').$file));
            }else{
                // Set the mimetype of the file manually, in this case for a text file is text/plain
                $response->headers->set('Content-Type', 'csv/plain');
            }

            // Set content disposition inline of the file
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $file
            );

            return $response;
    }
}
