<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoriesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categoryNames = ['Travail', 'Personnel', 'Ménage', 'Santé', 'À faire'];
        foreach ($categoryNames as $index => $name){
            $category = new Categories();
            $category->setName($name);
            $this->addReference('cat-'.$index, $category);
            $manager->persist($category);
        }
        $manager->flush();

        
    }
}
