<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr-FR');

        //----------------------CUSTOMERS--------------------
        // customer with ROLE_USER
        $customer = new Customer();
        $customer->setUsername('customer')
            ->setPassword(password_hash('password', PASSWORD_BCRYPT))
            ->setEmail('customer@customer.fr')
            ->setCreatedAt(new DateTime('now'))
            ->setRoles(['ROLE_USER']);
        $manager->persist($customer);

        // customer with ROLE_ADMIN
        $adminCustomer = new Customer();
        $adminCustomer->setUsername('admin')
            ->setPassword(password_hash('password', PASSWORD_BCRYPT))
            ->setEmail('admin@admin.fr')
            ->setCreatedAt(new DateTime('now'))
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($adminCustomer);

        //----------------------USERS--------------------
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail($faker->email)
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName)
                ->setCreatedAt($faker->dateTimeThisYear())
                ->setCustomer($customer);
            $manager->persist($user);
            $customer->addUser($user);
        }

        $manager->flush();
    }
}
