<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr-FR');

        $customer = new Customer();
        $customer->setName('phone-discount.fr')
            ->setEmail('admin@phone-discount.fr');
        $manager->persist($customer);

        for ($i = 0; $i < 20; $i++) {

            $user = new User();
            $user->setEmail($faker->email)
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName)
                ->setCreatedAt($faker->dateTimeThisYear())
                ->setCustomer($customer);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
