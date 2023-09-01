<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Project;
use App\Entity\SchoolYear;
use App\Entity\Student;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestFixtures extends Fixture implements FixtureGroupInterface
{
    private $faker; // Permet de générer des string aléatoire (email, user, etc)
    private $hasher; // Permet de hasher les MDP
    private $manager; // Permet de stocker des données dans une BDD

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->faker = FakerFactory::create('fr_FR');
        $this->hasher = $hasher;
    }

    public static function getGroups(): array
    {
        return ['test'];
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->loadSchoolYears();
        $this->loadTags();
        $this->loadProjects();
        $this->loadStudents();
    }


    public function loadTags() : void
    {
        // données statiques
        $datas = [
            [
                'name' => 'HTML',
                'description' => null,
            ],
            [
                'name' => 'CSS',
                'description' => null,
            ],
            [
                'name' => 'JS',
                'description' => null,
            ],
        ];

        foreach($datas as $data) {
            $tag = new Tag();
            $tag->setName($data['name']);
            $tag->setDescription($data['description']);

            $this->manager->persist($tag);
        }
        $this->manager->flush();

        // données dynamiques
        for($i = 0; $i < 10; $i++) {
            $tag = new Tag();
            $words = random_int(1, 3);
            $tag->setName($this->faker->unique()->sentence($words));

            $words = random_int(8, 13);
            $tag->setDescription($this->faker->optional($weight = 0.6)->sentence($words));

            $this->manager->persist($tag);
        }
        $this->manager->flush();
    }


    public function loadSchoolYears() : void
    {
        // données statiques
        $datas = [
            [
                'name' => 'Alan Turing',
                'description' => null,
                'startDate' => new DateTime('2022-01-01'),
                'endDate' => new DateTime('2022-12-31'),
            ],
            [
                'name' => 'John Von Neumann',
                'description' => null,
                'startDate' => new DateTime('2022-06-01'),
                'endDate' => new DateTime('2023-05-31'),
            ],
            [
                'name' => 'Brendan Eich',
                'description' => null,
                'startDate' => null,
                'endDate' => null,
            ],
        ];

        foreach($datas as $data) {
            $schoolYear = new SchoolYear();
            $schoolYear->setName($data['name']);
            $schoolYear->setDescription($data['description']);
            $schoolYear->setStartDate($data['startDate']);
            $schoolYear->setEndDate($data['endDate']);

            $this->manager->persist($schoolYear);
        }
        $this->manager->flush();

        // données dynamiques
        for($i = 0; $i < 20; $i++) {
            $schoolYear = new SchoolYear();

            $words = random_int(1, 3);
            $schoolYear->setName($this->faker->unique()->sentence($words));

            $words = random_int(8, 13);
            $schoolYear->setDescription($this->faker->optional($weight = 0.6)->sentence($words));


            $startDate = $this->faker->dateTimeBetween('-2 year', '-1 years');
            $schoolYear->setStartDate($startDate);

            $endDate = $this->faker->dateTimeBetween('-1 year', '-6 months');
            $schoolYear->setEndDate($endDate);

            $this->manager->persist($schoolYear);
        }
        $this->manager->flush();

    }


    public function loadProjects() : void
    {
        $tagRepository = $this->manager->getRepository(Tag::class);
        $tags = $tagRepository->findAll();

        // récupération d'un tag à partir de son ID
        $htmlTag = $tagRepository->find(1);
        $cssTag = $tagRepository->find(2);
        $jsTag = $tagRepository->find(3); // ou $tagJS = $tags[2] pour récupérer le 3eme élément de la liste
        // données statiques
        $datas = [
            [
                'name' => 'Site vitrine',
                'description' => null,
                'clientName' => "Tony",
                'startDate' => new DateTime('2022-01-01'),
                'checkpointDate' => new DateTime('2022-12-31'),
                'deliveryDate' => new DateTime('2023-01-31'),
                'tags' => [$htmlTag, $cssTag],
            ],
            [
                'name' => 'Wordpress',
                'description' => null,
                'clientName' => "Justine",
                'startDate' => new DateTime('2022-06-01'),
                'checkpointDate' => new DateTime('2023-05-31'),
                'deliveryDate' => new DateTime('2023-07-31'),
                'tags' => [$jsTag, $cssTag],
            ],
            [
                'name' => 'API Rest',
                'description' => null,
                'clientName' => "Alex",
                'startDate' => new DateTime('2022-06-01'),
                'checkpointDate' => new DateTime('2022-07-01'),
                'deliveryDate' => new DateTime('2022-08-01'),
                'tags' => [$jsTag],
            ],
        ];

        foreach($datas as $data) {
            

            $project = new Project();
            $project->setName($data['name']);
            $project->setDescription($data['description']);
            $project->setClientName($data['clientName']);
            $project->setStartDate($data['startDate']);
            $project->setCheckpointDate($data['checkpointDate']);
            $project->setDeliveryDate($data['deliveryDate']);

            foreach($data['tags'] as $tag) {
                $project->addTag($tag);
            }

            $this->manager->persist($project);
        }
        $this->manager->flush();

        // données dynamiques
        for($i = 0; $i < 30; $i++) {
            $project = new Project();

            $words = random_int(1, 3);
            $project->setName($this->faker->sentence($words));

            $words = random_int(8, 13);
            $project->setDescription($this->faker->optional(0.7)->sentence($words));

            $project->setClientName($this->faker->name());

            $startDate = $this->faker->dateTimeBetween('-3 year', '-2 years');
            $project->setStartDate($startDate);

            $checkpointDate = $this->faker->dateTimeBetween('-2 year', '-1 years');
            $project->setCheckpointDate($checkpointDate);

            $deliveryDate = $this->faker->dateTimeBetween('-1 year', '-6 months');
            $project->setDeliveryDate($deliveryDate);

            // on choisit le nombre de tag au hasard entre 1 et 4
            $tagsCount = random_int(1, 4);
            // on choisit des tags au hasard depuis la liste complete
            $shortList = $this->faker->randomElements($tags, $tagsCount);

            // on pass en revue chaque tag de la short liste
            foreach ($shortList as $tag) {
                // on associe un/des tag avec le projet
                $project->addTag($tag); 
             }
            

            $this->manager->persist($project);
        }
        $this->manager->flush();
    }


    public function loadStudents() : void 
    {
        $repoStudent = $this->manager->getRepository(Student::class);
        $students = $repoStudent->findAll();

        $repoSchoolYear = $this->manager->getRepository(SchoolYear::class);
        $schoolYears = $repoSchoolYear->findAll();

        $repoProject = $this->manager->getRepository(Project::class);
        $projects = $repoProject->findAll();

        $siteVitrine = $repoProject->find(1);
        $wordpress = $repoProject->find(2);
        $apiREST = $repoProject->find(3);

        $repoTag = $this->manager->getRepository(Tag::class);
        $tags = $repoTag->findAll();

        $html = $repoTag->find(1);
        $css = $repoTag->find(2);
        $js = $repoTag->find(3);

        // données statiques
        $datas = [
            [
                'email' => 'foo@example.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
                'firstName' => 'Foo',
                'lastName' => 'Example',
                'schoolYear' => $schoolYears[0],
                'projects' => [$siteVitrine],
                'tags' => [$html, $css]
            ],
            [
                'email' => 'bar@example.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
                'firstName' => 'Bar',
                'lastName' => 'Example',
                'schoolYear' => $schoolYears[1],
                'projects' => [$wordpress],
                'tags' => [$html, $js]
            ],
            [
                'email' => 'baz@example.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
                'firstName' => 'Baz',
                'lastName' => 'Example',
                'schoolYear' => $schoolYears[2],
                'projects' => [$apiREST],
                'tags' => [$js, $css]
            ],
        ];

        foreach ($datas as $data) {

            $user = new User();
            $user->setEmail($data['email']);
            $password = $this->hasher->hashPassword($user, $data['password']);
            $user->setPassword($password);
            $user->setRoles($data['roles']);
            
            $this->manager->persist($user);

            $student = new Student();
            $student->setFirstName($data['firstName']);
            $student->setLastName($data['lastName']);
            $student->setSchoolYear($data['schoolYear']);
            $student->setUser($user);

            // récupération du premier projet de la liste du student
            $project = $data['projects'][0];
            $student->addProject($project);

            foreach($data['tags'] as $tag) {
                $student->addTag($tag);
            }

            $this->manager->persist($student);

        }

        $this->manager->flush(); 

        for ($i = 0; $i < 50; $i++) 
        {
            $user = new User();
            $user->setEmail($this->faker->unique()->safeEmail());
            $password = $this->hasher->hashPassword($user, '123');
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);

            $this->manager->persist($user);
        
            $student = new Student();
            $student->setFirstName($this->faker->firstName());
            $student->setLastName($this->faker->lastName());

            $schoolYear = $this->faker->randomElement($schoolYears);
            $student->setSchoolYear($schoolYear);

            $project = $this->faker->randomElement($projects);
            $student->addProject($project);

            $tagsCount = random_int(1, 4);
            $shortList = $this->faker->randomElements($tags, $tagsCount);
            foreach ($shortList as $tag) {
                $student->addTag($tag);
            }

            $student->setUser($user);
            $this->manager->persist($student);

        }
        $this->manager->flush();
    }
}