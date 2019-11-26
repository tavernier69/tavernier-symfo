<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Comment;
use App\Repository\AdRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads", name="admin_ads_index")
     */
    public function index(AdRepository $repo)
    {
        return $this->render('admin/ad/index.html.twig', [
            'ads' => $repo->findAll(),
        ]);
    }

    /**
     * Permet de modifier une annonce
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     */
    public function edit (Ad $ad, Request $request, ObjectManager $manager){

        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée"
            );
        }

        return $this->render('admin/ad/edit.html.twig',[
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }


    /**
     * Permet d'effacer une annonce
     *
     * @param Ad $ad
     * @param ObjectManager $manager
     * 
     * @return void
    * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     */
    public function delete(Ad $ad, ObjectManager $manager){

        $manager->remove($ad);
        $manager->flush();
        $this->addFlash(
            'success',
            "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée"
        );

        return $this->redirectToRoute('admin_ads_index');
    }

    /**
     * @Route("/admin/comments", name="admin_comments_index")
     */
    public function comment(CommentRepository $repo)
    {
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $repo->findAll(),
        ]);
    }

    /**
     * Permet d'effacer un commentaire
     *
     * @param Ad $ad
     * @param ObjectManager $manager
     * 
     * @return void
    * @Route("/admin/comments/{id}/delete", name="admin_comments_delete")
     */
    public function delete_comment(Comment $comment, ObjectManager $manager){

        $manager->remove($comment);
        $manager->flush();
        $this->addFlash(
            'success',
            "Le commentaire de <strong>{$comment->getAuthor()->getFullName()}</strong> pour l'annonce <strong>{$comment->getAd()->getTitle()}</strong> a bien été supprimée"
        );

        return $this->redirectToRoute('admin_comments_index');
    }

    /**
     * Permet d'afficher et de gerer le formulaire de connexion
     * @Route("/admin/login", name="admin_account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        return $this->render('admin/account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * Permet la déconnexion de l'utilisateur admin
     * 
     * @Route("/admin/logout", name="admin_account_logout")
     */
    public function logout(){

    }
}
