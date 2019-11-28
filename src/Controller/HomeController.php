<?php

namespace App\Controller;

use App\Repository\AdRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(AdRepository $repo)
    {
        return $this->render('home/index.html.twig', [
            'ads' => $repo->findLast(),
        ]);
    }
}
