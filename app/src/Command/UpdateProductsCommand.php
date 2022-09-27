<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class UpdateProductsCommand extends Command
{
    protected static $defaultName = 'app:update-products';

    public function __construct($projectDir)
    {
        $this->projectDir = $projectDir;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Update Product Records')
            ->setHelp('This command reads the selected .csv files')
            ->addArgument('markup', InputArgument::OPTIONAL, 'Percentage Markup', 20)
            ->addArgument('process_date', InputArgument::OPTIONAL, 'Date of the process', date_create()->format('d-m-Y'));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $processDate = $input->getArgument('process_date');

        $supplierProducts = $this->getCsvRowsAsArrays($processDate);

        // Loop over records
        foreach ($supplierProducts as $supplierProduct) {



        }

            // Update if matching records found in DB

            // Create new records if matching records are not found in DB
    }

    public function getCsvRowsAsArrays($processDate)
    {
        $inputFile = $this->projectDir . '/CSV Files/Products/' . $processDate . '.csv';

        $decoder = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

        return $decoder->decode(file_get_contents($inputFile), 'csv');
    }
}