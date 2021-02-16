<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:import-csv';

    public function __construct()
    {
        // best practices recommend to call the parent constructor first and
        // then set your own properties. That wouldn't work in this case
        // because configure() needs the properties set in this constructor

        parent::__construct();
    }

    protected function configure()
    {
        // ...
        $this
            ->setDefinition('Importing CSV file')
            ->setHelp('This command helps you importing stats from an csv file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // ... put here the code to create the user

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