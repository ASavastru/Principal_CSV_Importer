<?php

namespace App\Command;

use App\Entity\SuppliedProducts;
use App\Repository\SuppliedProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class UpdateProductsCommand extends Command
{
    protected static $defaultName = 'app:update-products';

    public function __construct($projectDir, EntityManagerInterface $entityManager)
    {
        $this->projectDir = $projectDir;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Update Product Records')
            ->setHelp('This command reads the selected .csv files')
            ->addArgument('process_date', InputArgument::OPTIONAL, 'Date of the process', date_create()->format('d-m-Y'));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $processDate = $input->getArgument('process_date');

        $supplierProducts = $this->getCsvRowsAsArrays($processDate);

        $suppliedProductRepo = $this->entityManager->getRepository(SuppliedProducts::class);

        foreach ($supplierProducts as $supplierProduct) {
            if ($existingSuppliedProduct = $suppliedProductRepo->findOneBy(['name' => $supplierProduct['name']])) {
                $this->updateSuppliedProduct($existingSuppliedProduct, $supplierProduct);
                continue;
            }
            $this->createNewSuppliedProduct($supplierProduct);
        }
        $io = new SymfonyStyle($input, $output);

        $io->success('It worked!');

        return Command::SUCCESS;
    }

    public function getCsvRowsAsArrays($processDate)
    {
        $inputFile = $this->projectDir . '/CSV Files/Products/' . $processDate . '.csv';

        $decoder = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

        return $decoder->decode(file_get_contents($inputFile), 'csv');
    }

    public function updateSuppliedProduct($existingSuppliedProduct, $supplierProduct)
    {
        $existingSuppliedProduct->setSalePrice($supplierProduct['salePrice']);
        $existingSuppliedProduct->setPurchasePrice($supplierProduct['purchasePrice']);

        $this->entityManager->flush();
        $this->entityManager->persist($existingSuppliedProduct);
    }

    public function createNewSuppliedProduct($supplierProduct)
    {
        $newSuppliedProduct = new SuppliedProducts();
        $newSuppliedProduct->setName($supplierProduct['name']);
        $newSuppliedProduct->setPurchasePrice($supplierProduct['purchasePrice']);
        $newSuppliedProduct->setSalePrice($supplierProduct['salePrice']);
        $newSuppliedProduct->setQuantity($supplierProduct['quantity']);

        $this->entityManager->flush();
        $this->entityManager->persist($newSuppliedProduct);
    }
}