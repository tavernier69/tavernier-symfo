<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\AdRepository;
use App\Repository\RegionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{

    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo, SessionInterface $session, RegionsRepository $repoRegion)
    {
        $ads = $repo->findAll();
        return $this->render('ad/index.html.twig', [
            'ads' => $ads,
            'regions' => $repoRegion->findAll()
        ]);
    }

    /**
     * Permet de créer une annonce
     * 
     * @Route("/ads/creer", name="ads_create")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager, RegionsRepository $repoRegion)
    {

        $ad = new Ad();

        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['coverImage']->getData();
            $name_file = $file->getClientOriginalName();
            $file->move($this->getParameter('article_image_directory') . '/' . $form['title']->getData() . '/coverImage', $name_file);
            $ad->setCoverImage($name_file);

            foreach ($ad->getImages() as $image) {
                $name_pict = $image->getCaption();
                $file->move($this->getParameter('article_image_directory') . '/' . $form['title']->getData(), $name_pict);

                $image->setAd($ad);
                $manager->persist($image);
            }

            $ad->setAuthor($this->getUser());
            //$manager = $this->getDoctrine()->getmanager();
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'article <strong> {$ad->getTitle()} </strong> a bien été enregistré"
            );

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render('ad/create.html.twig', [
            'form' => $form->createView(),
            'regions' => $repoRegion->findAll()
        ]);
    }


    /**
     * Permet d'éditer une annonce
     * 
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Cette annonce ne vous appartient pas, vous ne pouvez pas la modifier")
     *
     * @return Response
     */
    public function edit(Ad $ad, EntityManagerInterface $manager, Request $request, RegionsRepository $repoRegion)
    {

        $name_file = $ad->getCoverImage();
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);
        dump($form['title']->getData());
        if ($form->isSubmitted() && $form->isValid()) {

            if ($form['coverImage']->getData() !== null || $this->getParameter('article_image_directory') . '/' . $form['title']->getData() . '/coverImage' !== null) {
                $file = $form['coverImage']->getData();
                $name_file = $file->getClientOriginalName();
                $file->move($this->getParameter('article_image_directory') . '/' . $form['title']->getData() . '/coverImage', $name_file);
            } else {
                new File($this->getParameter('article_image_directory') . '/' . $form['title']->getData() . '/coverImage' . "/" . $name_file);
            }
            $ad->setCoverImage($name_file);
            foreach ($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }
            //$manager = $this->getDoctrine()->getmanager();
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications de l'article  <strong> {$ad->getTitle()} </strong> a bien été enregistré"
            );

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }
        return $this->render('ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad,
            'nameFile' => $name_file,
            'regions' => $repoRegion->findAll()
        ]);
    }

    /**
     * 
     * Permet d'afficher une seule annonce
     * @Route("/ads/{slug}", name="ads_show")
     * @param Ad $ad
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function show($slug, Ad $ad, Request $request, EntityManagerInterface $manager, RegionsRepository $repoRegion)
    {

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAd($ad)
                ->setAuthor($this->getUser());

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre commentaire a bien été posté"
            );
        }

        // $ad = $repo->findOneBySlug($slug);
        return $this->render('ad/show.html.twig', [
            'ad'    => $ad,
            'form'  => $form->createView(),
            'regions' => $repoRegion->findAll()
        ]);
    }

    /**
     * Permet de supprimer l'annonce
     *
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     * 
     * @Route("/ads/{slug}/delete", name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message="Vous n'avez pas le droit d'acceder à cette ressource")
     * 
     * @return void
     */
    public function delete(Ad $ad, EntityManagerInterface $manager)
    {
        $manager->remove($ad);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée"
        );

        return $this->redirectToRoute("ads_index");
    }
}
