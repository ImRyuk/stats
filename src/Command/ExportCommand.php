<?php

namespace App\Command;

use App\Entity\Stat;
use Doctrine\ORM\EntityManagerInterface;
use SplFileObject;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:export-csv';
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
            ->setHelp('This command helps you exporting stats to an csv file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)

        $output->writeln('Path to csv file: '.$input->getArgument('path'));

        $file = new SplFileObject($input->getArgument('path'), 'w');

        $stats = $this->em->getRepository(Stat::class)->findAll();

        $header = ['Région', 'Groupement', 'Code', 'satisfaction usagers', 'satisfaction victimes', 'brigade numérique', 'délai brigade numérique', ''];
        $file->fputcsv($header, ';');
        foreach ($stats as $stat) {
            $file->fputcsv($stat->toArray(), ';');
        }

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