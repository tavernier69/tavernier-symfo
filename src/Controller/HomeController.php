<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="test")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/hello/{name}", name="hello")
     */
    public function hello ($name = "anonyme"){
        return $this->render('home/hello.html.twig', [
            'name' => $name
        ]);
    }
}
