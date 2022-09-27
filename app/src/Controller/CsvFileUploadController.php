<?php

namespace App\Controller;

use App\Kernel;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CsvFileUploadController extends AbstractController
{
    public function __construct(protected EntityManagerInterface $db)
    {
    }

    #[Route('/upload_file', name: 'app_upload')]
    public function upload(\Symfony\Component\HttpKernel\KernelInterface $kernel): Response
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'app:update-products'
        ));

        $output = new BufferedOutput();

        $application->run($input, $output);

        // return the output
        $content = $output->fetch();

        // Send the output of the console command as response
        return new Response($content);
    }
}