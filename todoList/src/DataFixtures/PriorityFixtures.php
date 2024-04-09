<?php

namespace App\DataFixtures;

use App\Entity\Priority;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PriorityFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $priorityNames = ['Priorité Haute', 'Priorité Moyenne', 'Priorité faible'];
        foreach ($priorityNames as $index => $name){
            $priority = new Priority();
            $priority->setName($name);
            $this->addReference('prio-'.$index, $priority);
            $manager->persist($priority);
        }
        $manager->flush();
    }
}
