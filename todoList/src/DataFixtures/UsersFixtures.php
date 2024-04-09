<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Users;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordEncoder){}
    public function load(ObjectManager $manager): void
    {
        $admin = new Users();
        $admin->setEmail('admin@test.fr');
        $admin->setFirstname('Trotro');
        $admin->setLastname('anime');
        $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin, 'admin')
        );
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $faker = Faker\Factory::create('fr_FR');

        for($usr = 1; $usr <= 5; $usr++){
            $user = new Users();
            $user->setEmail($faker->email);
            $user->setFirstname($faker->FirstName);
            $user->setLastname($faker->lastName);
            $user->setPassword(
                $this->passwordEncoder->hashPassword($user, 'secret')
            );
            $user->setRoles(['ROLE_USER']);
            $this->addReference('user-'.$usr, $user);
            $manager->persist($user);
        }
        $manager->flush();
    }
}