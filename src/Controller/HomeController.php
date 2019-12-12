<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\RegionsRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(AdRepository $repo, RegionsRepository $repoRegion)
    {

        return $this->render('home/index.html.twig', [
            'ads' => $repo->findLast(),
            'regions' => $repoRegion->findAll()
        ]);
    }
}
