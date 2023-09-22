<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\Student;
use App\Entity\Tag;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('clientName')
            ->add('startDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('checkpointDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('deliveryDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false
            ])
            ->add('students', EntityType::class, [
                'class' => Student::class,
                'choice_label' => function(Student $student) {
                    return "{$student->getFirstName()} {$student->getLastName()} (id {$student->getId()})";
                },
                // multiple permet d'avoir plusieurs choix
                'multiple' => true,
                // expanded permet d'avoir une liste déroulante avec une case à cocher
                'expanded' => true,
                'query_builder'=> function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.firstName', 'ASC')
                        ->addOrderBy('s.lastName', 'ASC');
                },
                // WARNING:
                // a ne rajouter que pour les associations qui sont le coté possédant
                // autrement dit, nécéssaire si dans l'entité project , la propriété student possede l'attribue mappedBy.
                // dans ce cas student est possédant et project est le coté possédé.
                'by_reference' => false,
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'choice_label' => function(Tag $tag) {
                    return "{$tag->getName()} (id {$tag->getId()})";
                },
                'multiple' => true,
                'expanded' => true,
                'query_builder'=> function (EntityRepository $er) {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC')
                        ->addOrderBy('t.id', 'ASC');
                },
                // INFO:
                // pas necessaire d'ajouter l'attribut by_reference car l'association n'est pas le coté possédant
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
