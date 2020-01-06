<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\AdRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TagController extends AbstractController
{
    /**
     * @Route("/admin/tag", name="home_tag")
     */
    public function index(TagRepository $repo)
    {
        return $this->render('admin/tag/show.html.twig', [
            'tags' => $repo->findAll()
        ]);
    }

    /**
     * @Route("/admin/tag/add", name="admin_tag_add")
     */
    public function add(EntityManagerInterface $manager, Request $request)
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handlerequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($tag);
            $manager->flush();

            $this->addFlash(
                'success',
                "Une nouveau tag a bien été créé"
            );
            return $this->redirectToRoute('home_tag');
        }

        return $this->render('admin/tag/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer un tag
     *
     * @param Tag $tag
     * @param EntityManagerInterface $manager
     * 
     * @Route("/admin/tag/{id}/delete", name="admin_tag_delete")
     * 
     * @return void
     */
    public function delete(Tag $tag, EntityManagerInterface $manager)
    {
        $manager->remove($tag);
        $manager->flush();

        $this->addFlash(
            'success',
            'Le tag "<strong>{$tag->getName()}</strong>" a bien été supprimée'
        );

        return $this->redirectToRoute("home_tag");
    }

    /**
     * @Route("/tag/{name}", name="show_tag")
     */
    public function showByRegion(Tag $tag, TagRepository $repoTag, AdRepository $ad)
    {
        return $this->render('region/index.html.twig', [
            'tag' => $tag,
            'regions' => $repoTag->findAll(),
            'ads' => $ad->findBy([
                'tags' => $tag->getId()
            ])
        ]);
    }
}
