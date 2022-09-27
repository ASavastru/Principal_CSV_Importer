<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class HomeController extends AbstractController
{
    public function __construct(protected EntityManagerInterface $db)
    {
    }

    #[Route('/home', name: 'app_home')]
    public function home(): Response
    {
        if ($this->getUser()) {
            return $this->render('home/home.html.twig', [
                'controller_name' => 'HomeController',
            ]);
        }
        return $this->redirectToRoute('app_register');
    }

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_home');
    }
}
