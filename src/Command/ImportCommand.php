<?php

namespace App\Command;

use App\Entity\Departement;
use App\Entity\OldRegion;
use App\Entity\Region;
use App\Entity\StatValue;
use App\Entity\Type;
use Doctrine\ORM\EntityManagerInterface;
use SplFileObject;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:import-csv';
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('path', InputArgument::REQUIRED, 'The path of the csv file.')
            ->setHelp('This command helps you importing stats from an csv file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Path to csv file: '.$input->getArgument('path'));

        $file = new SplFileObject($input->getArgument('path'));

        if($file->getExtension()!='csv')
        {
            return Command::FAILURE;
            //throw new \Exception('The file must be a csv extension');
        }

        $file->setFlags(SplFileObject::READ_CSV);


        //Clearing the database tables
        /*$entities = [Departement::class, Region::class, StatValue::class, Type::class];

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
        }*/

        $types = [];
        $departements = [];
        $stats[] = [];

        //Navigating through the file
        foreach ($file as $key=>$row){

            //the first row stores the headers
            if($key==0)
            {
                $i = 3;
                $keys = explode(";", $row[0]);

                $defaultHeadersValues = ['Région', 'Groupement', 'Code'];
                $percentNeeded = [80, 85,75];

                //We check if the 3 first rows match with the default headers values, if not, we throw an error message
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
                }

                while ($i < count($keys))
                {
                    //Asking if the column needs a suffixe parameter, if yes, asking its name and adding it to the new Type Object
                    $helper = $this->getHelper('question');
                    $question = new ConfirmationQuestion('Does the column ' . $keys[$i] . ' needs a suffixe parameter?(yes/no)', true);
                    $type = new Type();
                    $type->setLibelle($keys[$i]);
                    if ($helper->ask($input, $output, $question)) {
                        $question = new Question('Please enter the name of the suffixe needed');
                        $suffixe = $helper->ask($input, $output, $question);
                        $type->setSuffixe($suffixe);
                    }
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

       //$this->em->flush();

        // return this if there was no problem running the command
        // (it's equivalent to returning int(0))
        return Command::SUCCESS;
    }
}