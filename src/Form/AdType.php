<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\Tag;
use App\Entity\Regions;
use App\Form\ImageType;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration('Titre', 'Entrer un titre pour l\'article'))
            ->add('slug', HiddenType::class, $this->getConfiguration('Url', 'Adresse web (Automatique)', ['required' => false]))
            ->add('introduction', TextType::class, $this->getConfiguration('Introduction', 'Entrer une introduction'))
            ->add('description', TextareaType::class, $this->getConfiguration('Contenu', 'Entrer du contenu pour l\'article'))
            ->add('coverImage', FileType::class, array('data_class' => null, 'required' => false, 'label' => 'Image de couverture'))
            ->add('regions', EntityType::class, [
                'class' => Regions::class,
                'choice_label' => 'name'
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => 'name',
                'label' => 'Catégorie'
            ])
            ->add('images', CollectionType::class, [
                'entry_type'        => ImageType::class,
                'prototype'			=> true,
                'allow_add'			=> true,
                'allow_delete'		=> true,
                'by_reference' 		=> false,
                'required'			=> false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
