<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('FR-fr');

        

        for($i = 0; $i <= 12; $i++){
            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('<p></p>', $faker->paragraphs(5)) . '</p>';
            $ad = new Ad();
            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content);

        for($j = 0; $j <= mt_rand(2, 5); $j++){
            $image = new Image();
            $image->seturl($faker->imageurl())
                  ->setCaption($faker->sentence())
                  ->setAd($ad);
                  $manager->persist($image);
        }

            $manager->persist($ad);
        }
        
        $manager->flush();
    }
}
