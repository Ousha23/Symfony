<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Priority;
use App\Entity\Tasks;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TasksFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', options :[
                'label' => 'Titre',
            ])
            ->add('description')
            ->add('due_date', null, [
                'widget' => 'single_text',
                'label' => 'Date d\'échéance',
            ])
            ->add('status', ChoiceType::class, [ 
                'label' => 'Statut',
                'choices' => [
                    'À faire' => 'À faire',
                    'En cours' => 'En cours',
                    'Fait' => 'Fait',
                ],
                'placeholder' => 'Sélectionner un statut', 
            ])
            ->add('category', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'placeholder' => 'Sélectionner une catégorie',
            ])
            ->add('priority', EntityType::class, [
                'class' => Priority::class,
                'choice_label' => 'name',
                'label' => 'Priorité',
                'placeholder' => 'Sélectionner une priorité',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tasks::class,
        ]);
    }
}
