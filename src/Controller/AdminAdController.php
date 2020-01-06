<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Comment;
use App\Service\MailService;
use App\Repository\AdRepository;
use App\Repository\CommentRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\File;
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
    public function edit(Ad $ad, Request $request, EntityManagerInterface $manager, MailService $mailService, AdRepository $repo)
    {
        $info_user = $repo->findUserByIdArticle($ad->getId());
        foreach($info_user as $user){
            $firstname = $user['u_firstName'];
            $lastname = $user['u_lastName'];
            $email = $user['u_email'];
        }
        $form = $this->createForm(AdType::class, $ad);
        $name_file = $ad->getCoverImage();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form['coverImage']->getData() != null) {

                $file = $form['coverImage']->getData();
                $name_file = $file->getClientOriginalName();
                $file->move($this->getParameter('article_cover_image_directory'). "/", $name_file);
            } else {
                new File($this->getParameter('article_cover_image_directory'). "/" . $name_file);
            }
            $ad->setCoverImage($name_file);
            foreach ($form['images']->getData() as $image) {
                new File($this->getParameter('article_cover_image_directory'). "/" . 'bali9.jpg');
                
                $name_pict = $image->getCaption().".jpg";
                $image->setUrl($image->getCaption());
                $image->setAd($ad);
                $manager->persist($image);
            }
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée"
            );
            if(!empty($email) && !empty($firstname) && !empty($lastname)){
                $mailService->mail_article($email, $firstname, $lastname, $ad->getTitle(), 'modifié');
            }
        }
        
        return $this->render('admin/ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }


    /**
     * Permet d'effacer une annonce
     *
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     * 
     * @return void
     * @Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     */
    public function delete(Ad $ad, EntityManagerInterface $manager, MailService $mailService, AdRepository $repo)
    {
        $info_user = $repo->findUserByIdArticle($ad->getId());
        foreach($info_user as $user){
            $firstname = $user['u_firstName'];
            $lastname = $user['u_lastName'];
            $email = $user['u_email'];
        }
        $manager->remove($ad);
        $manager->flush();
        $this->addFlash(
            'success',
            "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée"
        );
        $mailService->mail_article($email, $firstname, $lastname, $ad->getTitle(), 'supprimé');
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
     * @param EntityManagerInterface $manager
     * 
     * @return void
     * @Route("/admin/comments/{id}/delete", name="admin_comments_delete")
     */
    public function delete_comment(Comment $comment, EntityManagerInterface $manager)
    {

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
    public function logout()
    { }
}
