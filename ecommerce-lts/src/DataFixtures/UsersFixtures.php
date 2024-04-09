<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class UsersFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordEncoder){}
    public function load(ObjectManager $manager): void
    {
        $admin = new Users();
        $admin->setEmail('admin@test.fr');
        $admin->setFirstname('Trotro');
        $admin->setLastname('anime');
        $admin->setAddress('habite ou');
        $admin->setCity('Jesspas');
        $admin->setZipcode('99999');
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
            $user->setAddress($faker->streetAddress);
            $user->setCity($faker->city);
            $user->setZipcode($faker->postcode);//ça marche pas tjrs probleme d'espace mais dans mes tests aucun probleme
            //$user->setZipcode(str_replace(' ','',$faker->postcode));
            $user->setPassword(
                $this->passwordEncoder->hashPassword($user, 'secret')
            );
            $user->setRoles(['ROLE_ADMIN']);
            $manager->persist($user);
        }
        $manager->flush();
    }
}
