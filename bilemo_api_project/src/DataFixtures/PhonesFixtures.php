<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Phone;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class PhonesFixtures extends Fixture
{
    private $names = ['iPhone', 'Samsung', 'Sony', 'Xiaomi', 'Motorola', 'Huawei', 'Lenovo', 'Alcatel', 'Asus'];


    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr-FR');
        for ($i = 1; $i <= 120; $i++) {
            $phone = new Phone();
            $phone->setName($this->names[rand(0, 8)] . ' ' . $faker->word());
            $phone->setColor($faker->safeColorName);
            $phone->setPrice(rand(200, 1000));
            $phone->setDescription($faker->sentence(3));
            $phone->setReleasedAt($faker->dateTimeThisYear());
            $manager->persist($phone);
        }

        $manager->flush();
    }
}
