<?php

namespace App\Form;

use App\Entity\Thread;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ThreadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre *',
                'attr' => ['minlength' => 3, 'maxlength' => 255]
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu *',
                'attr' => ['rows' => 6, 'minlength' => 10]
            ])
            ->add('tags', TextType::class, [
                'label' => 'Tags (séparés par des virgules)',
                'required' => false,
                'attr' => ['maxlength' => 500]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Thread::class,
        ]);
    }
}