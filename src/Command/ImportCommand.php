<?php

namespace App\Command;

use App\Entity\StateUnite;
use Doctrine\ORM\EntityManagerInterface;
use SplFileObject;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:import-csv';
    private $finder;
    private $em;

    public function __construct(Finder $finder, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->finder = new Finder();
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
        // outputs multiple lines to the console (adding "\n" at the end of each line)

        $output->writeln('Path to csv file: '.$input->getArgument('path'));

        //$this->finder->files()->in('src\Data');
        //$this->finder->path($input->getArgument('path'))->name('*.csv');

        $file = new SplFileObject($input->getArgument('path'));
        $file->setFlags(SplFileObject::READ_CSV);

        $keys = [];
        $region = '';

        $stats = $this->em->getRepository(StateUnite::class)->findAll();

        //Clearing the table StateUnite
        $cmd = $this->em->getClassMetadata(StateUnite::class);
        $connection = $this->em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
            $connection->executeUpdate($q);
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        }
        catch (\Exception $e) {
            $connection->rollback();
        }

        //Navigating through the file
        foreach ($file as $key=>$row) {
            //The first row contents the fields name
            if($key==0)
            {
                $keys = explode(";", $row[0]);
            }
            else if($key>=1 && !is_null($row[0]))
            {
                    //Separating the row in order to filter it
                    $row = explode(";", $row[0]);

                    //If the row has no region, he heritates the last used region
                    $row[0] = (empty($row[0]) ? $region : $region=$row[0]);

                    //Filling the row array as values with the keys array as keys
                    $row = array_combine(array_map(function($el) use ($keys) {
                    return $keys[$el];
                }, array_keys($row)), array_values($row));

                    //Creating a new StateUnite Object
                    $stat = new StateUnite();
                    $stat
                        ->setRegion($row['Région'])
                        ->setGroupement($row['Groupement'])
                        ->setCode($row['Code'])
                        ->setSatisfaction($row['satisfaction usagers'])
                        ->setSatisfactionVictime($row['satisfaction victimes'])
                        ->setBrigadeNumerique($row['brigade numérique'])
                        ->setDelaiBrigadeNumerique($row['délai brigade numérique']);

                    $this->em->persist($stat);
            }
        }
        var_dump($stats);
        $this->em->flush();

        // the value returned by someMethod() can be an iterator (https://secure.php.net/iterator)
        // that generates and returns the messages with the 'yield' PHP keyword


        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable

        // return this if there was no problem running the command
        // (it's equivalent to returning int(0))
        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;
    }
}