<?php

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('caption', TextType::class, [
                'attr' => [
                    'placeholder' => 'Donner un titre pour l\'image'
                ]
            ])
            ->add('url', FileType::class, array(
                'data_class' => null,
                 'required' => false,
                  'label' => 'Image de carousel',
                  'empty_data' => '',
                  'constraints' => array(
                    new File(),
                ),
                  ),
                   [
                'attr' => [
                    'placeholder' => 'Donner une adresse pour l\'image'
                ]
            ]);
            

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
