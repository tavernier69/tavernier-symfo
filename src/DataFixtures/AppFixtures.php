<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
public function __construct(UserPasswordEncoderInterface $encoder){
    $this->encoder = $encoder;
}

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('FR-fr');

        // Nous gérons les utilisateurs
        $users = [];
        $genres = ['male', 'female'];
        for($i = 1; $i <= 10; $i++){
            $user = new User();

            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $picture_id = $faker->numberBetween(1, 99). '.jpg';

            if($genre == 'male'){
                $picture = $picture. 'men/'. $picture_id;
            } else  {
                $picture = $picture. 'women/'. $picture_id;
            }

            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstname($genre))
                 ->setLastName($faker->lastname)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence())
                 ->setText('<p>' . join('<p></p>', $faker->paragraphs(1)) . '</p>')
                 ->setHash($hash)
                 ->setPicture($picture);

            $manager->persist($user);
            $users[] = $user;
        }

        // Nous gérons les annonces
        for($i = 0; $i <= 12; $i++){
            $ad = new Ad();

            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(1000, 350);
            $introduction = $faker->paragraph(2);
            $content = '<p>' . join('<p></p>', $faker->paragraphs(5)) . '</p>';
            $user = $users[mt_rand(0, count($users) - 1)];

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setAuthor($user);

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
