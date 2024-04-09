<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Products;
use Faker;
class ProductsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($prod = 1; $prod <= 10 ; $prod++){
            $product = new Products();
            $product->setName($faker->text(15));
            $product->setDescription($faker->text());
            $product->setPrice($faker->numberBetween(900,150000));
            $product->setStock($faker->numberBetween(0,10));
            //on cherche la ref de la catégorie
            $category = $this->getReference('cat-'.rand(1,8));
            $product->setCategories($category);
            //donner une ref au produit
            $this->addReference('prod-'.$prod,$product);
            $manager->persist($product);
            
        }
        $manager->flush();
    }
}
