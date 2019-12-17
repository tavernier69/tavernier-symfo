<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AccountType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfiguration('Prénom', 'Entrer votre prénom'))
            ->add('lastName', TextType::class, $this->getConfiguration('Nom', 'Entrer votre nom de famille'))
            ->add('email', EmailType::class, $this->getConfiguration('Email', 'Exemple: exemple@exemple.com'))
            ->add('picture', FileType::class, array('data_class' => null, 'required' => false, 'label' => 'Introduction'))
            ->add('introduction', TextType::class)
            ->add('text', TextareaType::class, array('label' => 'Description'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
