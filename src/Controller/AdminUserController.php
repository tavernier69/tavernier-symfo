<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/user", name="admin_users_index")
     */
    public function index(UserRepository $repo)
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $repo->findAll(),
        ]);
    }

    /**
     * Permet d'effacer un utilisateur
     *
     * @param User $user
     * @param ObjectManager $manager
     * 
     * @return void
    * @Route("/admin/users/{id}/delete", name="admin_users_delete")
     */
    public function delete_user(User $user, ObjectManager $manager){

        $manager->remove($user);
        $manager->flush();
        $this->addFlash(
            'success',
            "L'utilisateur <strong>{$user->getFullName()}</strong> a bien été supprimée"
        );

        return $this->redirectToRoute('admin_users_index');
    }
}