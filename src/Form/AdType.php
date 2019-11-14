<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration('Titre', 'Entrer un titre pour l\'article'))
            ->add('slug', TextType::class, $this->getConfiguration('Url', 'Adresse web (Automatique)', ['required' => false]))
            ->add('introduction', TextType::class, $this->getConfiguration('Introduction', 'Entrer une introduction'))
            ->add('content', TextareaType::class, $this->getConfiguration('Contenu', 'Entrer du contenu pour l\'article'))
            ->add('coverImage', UrlType::class, $this->getConfiguration('Image', 'URL de l\'image principale'))
            ->add('images', CollectionType::class, [
                'entry_type' => ImageType::class,
                'allow_add' => true,
                'allow_delete' => true
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
