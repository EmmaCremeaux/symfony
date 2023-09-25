<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\SchoolYear;
use App\Entity\Student;
use App\Entity\Tag;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('schoolYear', EntityType::class, [
                'class' => SchoolYear::class,
                'choice_label' => function (SchoolYear $schoolYear) {
                    $startDate = $schoolYear->getStartDate() ? $startDate= '' : $startDate->format('Y') ;
                    return "{$schoolYear->getName()}";
                },
                'multiple' => false,
                'expanded' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC')
                        ->addOrderBy('s.startDate', 'ASC');
                },
            ])

            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => function (Tag $tag) {
                    return "{$tag->getName()} (id {$tag->getId()})";
                },
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
            ])
            
            ->add('projects', EntityType::class, [
                'class' => Project::class,
                'choice_label' => function (Project $project) {
                    return "{$project->getName()} (id {$project->getId()})";
                },
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC')
                        ->addOrderBy('p.startDate', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}