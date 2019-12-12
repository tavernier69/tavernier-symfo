<?php

namespace App\Controller;

use App\Entity\Regions;
use App\Form\RegionType;
use App\Repository\AdRepository;
use Doctrine\ORM\Cache\Region;
use App\Repository\RegionsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegionController extends AbstractController
{
    /**
     * @Route("/admin/region", name="home_region")
     */
    public function index(RegionsRepository $repo)
    {
        return $this->render('admin/region/show.html.twig', [
            'regions' => $repo->findAll()
        ]);
    }

    /**
     * @Route("/admin/region/add", name="admin_region_add")
     */
    public function add(RegionsRepository $repo, EntityManagerInterface $manager, Request $request)
    {
        $region = new Regions();
        $form = $this->createForm(RegionType::class, $region);
        $form->handlerequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($region);
            $manager->flush();

            $this->addFlash(
                'success',
                "Une nouvelle région a bien été créé"
            );
            return $this->redirectToRoute('home_region');
        }

        return $this->render('admin/region/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une région
     *
     * @param Regions $region
     * @param EntityManagerInterface $manager
     * 
     * @Route("/admin/region/{id}/delete", name="admin_region_delete")
     * 
     * @return void
     */
    public function delete(Regions $region, EntityManagerInterface $manager)
    {
        $manager->remove($region);
        $manager->flush();

        $this->addFlash(
            'success',
            "La région <strong>{$region->getName()}</strong> a bien été supprimée"
        );

        return $this->redirectToRoute("home_region");
    }

    /**
     * @Route("/region/{name}", name="show_region")
     */
    public function showByRegion(Regions $region, RegionsRepository $repoRegion, AdRepository $ad)
    {
        return $this->render('region/index.html.twig', [
            'region' => $region,
            'regions' => $repoRegion->findAll(),
            'ads' => $ad->findBy([
                'regions' => $region->getId()
            ])
        ]);
    }
}
