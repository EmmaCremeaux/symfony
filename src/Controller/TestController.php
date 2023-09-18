<?php

namespace App\Controller;

use DateTime;
use Exception;
use App\Entity\Project;
use App\Entity\SchoolYear; 
use App\Entity\Student;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/test')]

class TestController extends AbstractController
{
    #[Route('/tag', name: 'app_test_tag')]
    public function tag(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $repositoryTag = $em->getRepository(Tag::class);
        $repositoryStudent = $em->getRepository(Student::class);


        // Création d'un nouvel objet
        $foo = new Tag();
        $foo->setName('Foo');
        $foo->setDescription('Foo Bar Baz');
        $em->persist($foo);

        try {
            $em->flush();
        } catch(Exception $e) {
            //générer l'erreur
            dump($e->getMessage());
        }

        // Récupération de l'objet dont l'id est 1
        $tag = $repositoryTag->find(1);
        
        // Récupération de l'objet dont l'id est 14
        $tag14 = $repositoryTag->find(14);

        // Si l'objet existe
        if ($tag14){
            // Suppression de l'objet
            $em->remove($tag14);
            $em->flush();
        }
        
        // Récupération de l'objet dont l'id est 4
        $tag4 = $repositoryTag->find(4);

        // Modification d'un objet (ici celui dont l'id est 4)
        $tag4->setName('Python');
        $tag4->setDescription(null);
        // pas la peine d'utiliser persist() si l'objet proviens de la BDD
        $em->flush();

        // Récuperation du student dont l'id est 1
        $student1 = $repositoryStudent->find(1);
        // Association du tag 4 au student 1
        $tag4->addStudent($student1);
        $em->flush();

        // Récupération d'un tag dont le nom est CSS
        $cssTag = $repositoryTag->findOneBy([
            // Critères de recherche
            'name' => 'CSS'
        ]);

        // Récupération de tout les tags dont la description est NULL
        $nullDescriptionTags = $repositoryTag->findBy([
            // Critères de recherche
            'description' => NULL,
        ], [
            // Critères de tri
            'name' => 'ASC'
        ]);
        // OU
        // $nullDescriptionTags = $repositoryTag->findByNullDescription();  ==> Préférence de Daishi

        // Récupération de tout les Tags avec description
        $notNullDescriptionTags = $repositoryTag->findByNotNullDescription();

        // Récupération de la liste complète des objets
        $tags = $repositoryTag->findAll();

        // Récupération des tags qui contiennent certain mot-clés
        $keywordTags1 = $repositoryTag->findByKeyword('html');
        $keywordTags2 = $repositoryTag->findByKeyword('exercitationem');
        // $keywordTags3 = $repositoryTag->findByKeyword('foo');

        // Récupération de tags a partir d'une schoolYear
        $repositorySchoolYear = $em->getRepository(schoolYear::class);
        $schoolYear1 = $repositorySchoolYear->find(1);
        $schoolYearTags = $repositoryTag->findBySchoolYear($schoolYear1);

    
        $title = 'Test des tags';

        return $this->render('test/tag.html.twig', [
            'title' => $title,
            'tags' => $tags,
            'tag' => $tag,
            'cssTag' => $cssTag,
            'nullDescriptionTags' => $nullDescriptionTags,
            'notNullDescriptionTags' => $notNullDescriptionTags,
            'keywordTags1' => $keywordTags1,
            'keywordTags2' => $keywordTags2,
            // 'keywordTags3' => $keywordTags3,
            'schoolYearTags' => $schoolYearTags,
        ]);
    }

    #[Route('/school-year', name: 'app_test_schoolyear')]
    public function schoolYear(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $repository = $em->getRepository(schoolYear::class);
        $repositoryStudent = $em->getRepository(Student::class);

        // Création d'un nouvel objet
        $bar = new schoolYear();
        $bar->setName('Bar');
        $bar->setDescription('Foo Bar Baz');
        $startDate = new DateTime('2022-06-01');
        $bar->setStartDate($startDate);
        $endDate = new DateTime('2022-09-01');
        $bar->setEndDate($endDate);
        $em->persist($bar);

        try {
            $em->flush();
        } catch(Exception $e) {
            //générer l'erreur
            dump($e->getMessage());
        }

        // Récupération de l'objet dont l'id est 1
        $schoolYear = $repository->find(1);
        
        // Récupération de l'objet dont l'id est 14
        $schoolYear24 = $repository->find(24);

        // Si l'objet existe
        if ($schoolYear24){
            // Suppression de l'objet
            $em->remove($schoolYear24);
            $em->flush();
        }
        
        // Récupération de l'objet dont l'id est 4
        $schoolYear4 = $repository->find(4);

        // Récuperation du student dont l'id est 1
        $student = $repositoryStudent->find(1);
        // Association de la school year 4 au student 1
        $student->setSchoolYear($schoolYear4);
        $em->flush();

        // Modification d'un objet (ici celui dont l'id est 4)
        $schoolYear4->setName('année 13');
        $schoolYear4->setDescription(NULL);
        // pas la peine d'utiliser persist() si l'objet proviens de la BDD
        $em->flush();

        // Récupération de la liste complète des objets
        $schoolYears = $repository->findAll();

        $title = 'Test des schoolYears';
        return $this->render('test/school-year.html.twig', [
            'title' => $title,
            'schoolYears' => $schoolYears,
            'schoolYear' => $schoolYear,
        ]);
    }

    #[Route('/project', name: 'app_test_project')]
    public function project(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $repositoryProject = $em->getRepository(project::class);
        $repositoryTag = $em->getRepository(tag::class);

        // Création d'un nouvel objet
        $pop = new Project();
        $pop->setName('Pop11');
        $pop->setDescription('Project Pop 11');
        $pop->setClientName('Emma');
        $startDate = new DateTime('2022-06-01');
        $pop->setStartDate($startDate);
        $checkpointDate = new DateTime('2022-09-01');
        $pop->setCheckpointDate($checkpointDate);
        $deliveryDate = new DateTime('2022-12-01');
        $pop->setDeliveryDate($deliveryDate);

        $em->persist($pop);

        // try {
        //     $em->flush();
        // } catch(Exception $e) {
        //     //générer l'erreur
        //     dump($e->getMessage());
        // }

        // Récupération de l'objet dont l'id est 1
        $project = $repositoryProject->find(1);
        
        // Récupération de l'objet dont l'id est 14
        $project4 = $repositoryProject->find(4);

        // Si l'objet existe
        if ($project4){
            // Suppression de l'objet
            $em->remove($project4);
            $em->flush();
        }
        
        // Récupération de l'objet dont l'id est 4
        $project5 = $repositoryProject->find(5);

        // Récuperation du student dont l'id est 1
        $tag1 = $repositoryTag->find(1);
        // Association de la school year 4 au student 1
        $tag1->addProject($project5);
        $em->flush();

        // Modification d'un objet (ici celui dont l'id est 4)
        $project5->setName('Pop 2');
        $project5->setDescription(NULL);
        // pas la peine d'utiliser persist() si l'objet proviens de la BDD
        $em->flush();

        // Récupération de la liste complète des objets
        $projects = $repositoryProject->findAll();

        $title = 'Test des projects';
        return $this->render('test/project.html.twig', [
            'title' => $title,
            'projects' => $projects,
            'project' => $project,
        ]);
    }

}

