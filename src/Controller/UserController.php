<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RegionsRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{slug}", name="user_show")
     */
    public function index(User $user, RegionsRepository $repoRegion)
    {
        
        return $this->render('user/index.html.twig', [
            'user' => $user,
            'regions' => $repoRegion->findAll()
        ]);
    }
}
