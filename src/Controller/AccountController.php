<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use App\Repository\RegionsRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher et de gerer le formulaire de connexion
     * @Route("/login", name="account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils, RegionsRepository $repoRegion)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username,
            'regions' => $repoRegion->findAll()
        ]);
    }


    /**
     * Permet de se déconnectioner
     * @Route("/logout", name="account_logout")
     *
     * @return void
     */
    public function logout()
    { }


    /**
     * permet d'afficher le formulaire d'inscription
     * @Route("/register", name="account_register")
     *
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {

        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handlerequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getHash());
            $file = $form['picture']->getData();
            $name_file = $file->getClientOriginalName();
            $file->move($this->getParameter('picture_directory'), $name_file);
            $user->setPicture($name_file);

            $user->setHash($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre compte a bien été créé"
            );

            return $this->redirectToRoute('account_login');
        }

        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * permet d'afficher et de traiter le formulaire de modification du profil
     * @Route("/account/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function profile(Request $request, EntityManagerInterface $manager, RegionsRepository $repoRegion)
    {

        $user = $this->getUser();
        $name_file = $user->getPicture();

        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);
        $directory = $this->getParameter('path_directory');
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form['picture']->getData() != null) {
                $file = $form['picture']->getData();
                $name_file = $file->getClientOriginalName();
                $file->move($this->getParameter('picture_directory'), $name_file);
                $user->setPicture($name_file);
            } else {
                new File($this->getParameter('picture_directory') . "/" . $name_file);
                $user->setPicture($name_file);
            }

            $manager->persist($user);
            $manager->flush();



            $this->addFlash(
                'success',
                "Les données du profil ont été enresitrées avec succes"
            );
        }

        return $this->render('account/profile.html.twig', [
            'path_pict' => $directory,
            'form' => $form->createView(),
            'nameFile' => $name_file,
            'regions' => $repoRegion->findAll()
        ]);
    }

    /**
     * permet de modifier le mot de passe
     * @Route("/account/password-update", name="account_password")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager, RegionsRepository $repoRegion)
    {

        $passwordUpdate = new PasswordUpdate();

        $user = $this->getUser();
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getHash())) {
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel"));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setHash($hash);
                $manager->persist($user);
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié"
                );
                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'regions' => $repoRegion->findAll()
        ]);
    }


    /**
     * permet d'afficher le profil de l'utilisateur connecté
     *
     * @Route("/account", name="account_index")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function myAccount(RegionsRepository $repoRegion)
    {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser(),
            'regions' => $repoRegion->findAll()
        ]);
    }
}
