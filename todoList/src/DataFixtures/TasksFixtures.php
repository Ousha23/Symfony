<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Tasks;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TasksFixtures extends Fixture implements DependentFixtureInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function load(ObjectManager $manager): void
    {
        // Sélectionner un nombre arbitraire de tâches à créer
        $faker = Factory::create();
        for($i = 1; $i <= 15; $i++){
                $task = new Tasks();
                $task->setTitle($faker->sentence(4));; // Ajoutez un titre unique à chaque tâche
                $user = $this->getReference('user-'.rand(1,5));
                $task->setUser($user);
                $task->setDescription($faker->text());
                $priority = $this->getReference('prio-'.rand(0,2));
                $task->setPriority($priority);
                //on cherche la ref de la catégorie
                $category = $this->getReference('cat-'.rand(0,4));
                $task->setCategory($category);
                $manager->persist($task);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoriesFixtures::class, // Dépendance de la classe CategoriesFixtures
            UsersFixtures::class,      // Dépendance de la classe UsersFixtures
            PriorityFixtures::class,   // Dépendance de la classe PriorityFixtures
        ];
    }
}