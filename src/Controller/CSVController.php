<?php

namespace App\Controller;

use App\Entity\CSVFile;
use App\Entity\Departement;
use App\Entity\OldRegion;
use App\Entity\Region;
use App\Entity\StatValue;
use App\Entity\Type;
use Doctrine\ORM\EntityManagerInterface;
use SplFileObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CSVController extends AbstractController
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/importPDF", name="import_csv")
     * @param Request $request
     * @return Response
     */
    public function importCSV(Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('file', FileType::class,[
                    'multiple' => false,
                    'required' => true,
                    'label' => 'Fichier CSV : ',
                    'attr' => ['class' => 'form-control']
                ]
            )
            ->add('Envoyer', SubmitType::class, [
                'attr' => ['class' => 'save form-control']])
            ->getForm();
        $form->handleRequest($request);

        $File = new CSVFile();


        if($form->isSubmitted() && $form->isValid())
        {
            $File->setFile($form->get('file')->getData());
            $file = $form->get('file')->getData();
            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) .'.' .$file->getClientOriginalExtension();
            $file->move(
                $this->getParameter('CSV_directory'),
                $filename);

            if(!empty($file))
            {
                $fileCSV = new SplFileObject($this->getParameter('CSV_directory') . $filename);
                var_dump($fileCSV);
                $fileCSV->setFlags(SplFileObject::READ_CSV);

                $types = [];
                $departements = [];
                $stats[] = [];

                //Clearing the database tables
                $entities = [Departement::class, Region::class, StatValue::class, Type::class];

                $connection = $this->em->getConnection();
                $databasePlatform = $connection->getDatabasePlatform();
                if ($databasePlatform->supportsForeignKeyConstraints()) {
                    $connection->query('SET FOREIGN_KEY_CHECKS=0');
                }
                foreach ($entities as $entity) {
                    $query = $databasePlatform->getTruncateTableSQL(
                        $this->em->getClassMetadata($entity)->getTableName());
                    $connection->executeUpdate($query);
                }
                if ($databasePlatform->supportsForeignKeyConstraints()) {
                    $connection->query('SET FOREIGN_KEY_CHECKS=1');
                }

                //Navigating through the file
                foreach ($fileCSV as $key=>$row){

                    //the first row stores the headers
                    if($key==0)
                    {
                        $i = 3;
                        $keys = explode(";", $row[0]);

                        $defaultHeadersValues = ['Région', 'Groupement', 'Code'];
                        $percentNeeded = [80, 85,75];

                        /*//We check if the 3 first rows match with the default headers values, if not, we throw an error message
                        for($j = 0; $j<$i;$j++)
                        {
                            var_dump($percentNeeded[$j]);
                            similar_text($defaultHeadersValues[$j], $keys[$j], $percent);
                            var_dump($percent);
                            if($percent < $percentNeeded[$j])
                            {
                                $output->writeln('ERROR: The column ' . $keys[$j] . ' doesnt match with the default value ' . $defaultHeadersValues[$j]);
                                $output->writeln('Command Failure');
                                return Command::FAILURE;
                            }
                        }*/

                        while ($i < count($keys))
                        {
                            //Asking if the column needs a suffixe parameter, if yes, asking its name and adding it to the new Type Object
                            //$helper = $this->getHelper('question');
                            //$question = new ConfirmationQuestion('Does the column ' . $keys[$i] . ' needs a suffixe parameter?(yes/no)', true);
                            $type = new Type();
                            $type->setLibelle($keys[$i]);
                            /*if ($helper->ask($input, $output, $question)) {
                                $question = new Question('Please enter the name of the suffixe needed');
                                $suffixe = $helper->ask($input, $output, $question);
                                $type->setSuffixe($suffixe);
                            }*/
                            $types[] = $type;
                            $i++;

                            $this->em->persist($type);
                        }
                    }
                    else if($key>=1 && !is_null($row[0])){
                        $row = explode(";", $row[0]);

                        //If the row has a region, we create a new one
                        if(!empty($row[0])){
                            $RegionEntity = new Region();
                            $RegionEntity->setName($row[0]);
                            $currentRegion = $RegionEntity;

                            $this->em->persist($RegionEntity);
                        }

                        $departement = new Departement();

                        //If the row 'Région' is empty, it means its the same as the past departement, so we give it the same region
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

                        //Navigating through the departements stats
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
                $this->em->flush();
                return $this->redirectToRoute('view_CSV');
            }
        }
        return $this->render('csv/form.html.twig', [
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
     * @return Response
     */
    public function export_CSV(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('Name', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Nom du fichier à importer',
            ])
            ->add('Envoyer', SubmitType::class, [
                'attr' => ['class' => 'save form-control']])
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //Creating the file
            $filename = $this->getParameter('CSV_directory') . $form->get('Name')->getData() . '.csv';

            $newFile = new SplFileObject($filename, 'w');

            $header = ['Région', 'Groupement', 'Code'];

            $types = $this->getDoctrine()->getRepository(Type::class)->findAll();
            foreach ($types as $type)
            {
                $header[] = $type->getLibelle();
            }

            $newFile->fputcsv($header, ';');

            $regions = $this->getDoctrine()->getRepository(Region::class)->findAll();

            foreach ($regions as $region)
            {
                $currentRegion = $region->getName();
                $departements = $region->getDepartements();

                for($i = 0; $i < count($departements); $i++)
                {
                    $values = ($i == 0 ? [$region->getName()] : ['']);
                    $data = $departements[$i]->toCSV();
                    foreach ($data as $item) {
                        $values[] = $item;
                    }
                    $newFile->fputcsv($values, ';');

                }
            }
        }

        return $this->render('csv/export_csv.html.twig', [
            'form' => $form->createView()
        ]);
        die;
    }
}
