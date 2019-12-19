<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\RoleType;
use App\Repository\UserRepository;
use App\Repository\RegionsRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/user", name="admin_users_index")
     */
    public function index(UserRepository $repo, RoleRepository $repoRoles )
    {
        $roles_user_admin = [];
        foreach($repo->findAll() as $user){
            $role = $repo->findRoleByUser($user->getId());
            foreach($role as $ro){
                $roles_user_admin[] = $ro;
            }
        }
        return $this->render('admin/user/index.html.twig', [
            'users' => $repo->findAll(),
            'role' => $roles_user_admin
        ]);
    }

    /**
     * @param User $user
     * @param EntityManagerInterface $manager
     * 
     * @return void
     * @Route("/admin/user/{id}/role", name="admin_users_role_index")
     */
    public function role(Request $request,RoleRepository $repoRoles, UserRepository $repo, RegionsRepository $repoRegion, User $user, EntityManagerInterface $manager)
    {
        $adminRole = $repoRoles->findOneByTitle('ROLE_ADMIN');
        $manager->persist($adminRole);
        $user->addUserRole($adminRole);
        $manager->persist($user);
        $manager->flush();

        $roles_user_admin = [];
        foreach($repo->findAll() as $user){
            $role = $repo->findRoleByUser($user->getId());
            foreach($role as $ro){
                $roles_user_admin[] = $ro;
            }
        }
        $this->addFlash(
            'success',
            "L'utilisateur <strong>{$user->getFullName()}</strong> est devenu un admin"
        );
        return $this->render('admin/user/index.html.twig', [
            'users' => $repo->findAll(),
            'role' => $roles_user_admin
        ]);
    }

    /**
     * @param User $user
     * @param EntityManagerInterface $manager
     * 
     * @return void
     * @Route("/admin/user/remove/{id}/role", name="admin_users_remove_role_index")
     */
    public function removeRole(Request $request,RoleRepository $repoRoles, UserRepository $repo, RegionsRepository $repoRegion, User $user, EntityManagerInterface $manager)
    {
        $adminRole = $repoRoles->findOneByTitle('ROLE_ADMIN');
        
        $manager->persist($adminRole);
        $user->removeUserRole($adminRole);
        $manager->persist($user);
        $manager->flush();

        $roles_user_admin = [];
        foreach($repo->findAll() as $user){
            $role = $repo->findRoleByUser($user->getId());
            foreach($role as $ro){
                $roles_user_admin[] = $ro;
            }
        }
        $this->addFlash(
            'success',
            "L'utilisateur <strong>{$user->getFullName()}</strong> n'est plus un admin"
        );
        return $this->render('admin/user/index.html.twig', [
            'users' => $repo->findAll(),
            'role' => $roles_user_admin
        ]);
    }

    /**
     * Permet d'effacer un utilisateur
     *
     * @param User $user
     * @param EntityManagerInterface $manager
     * 
     * @return void
     * @Route("/admin/users/{id}/delete", name="admin_users_delete")
     */
    public function delete_user(User $user, EntityManagerInterface $manager)
    {

        $manager->remove($user);
        $manager->flush();
        $this->addFlash(
            'success',
            "L'utilisateur <strong>{$user->getFullName()}</strong> a bien été supprimée"
        );

        return $this->redirectToRoute('admin_users_index');
    }
}
