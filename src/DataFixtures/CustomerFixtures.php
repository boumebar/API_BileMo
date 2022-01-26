<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Customer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CustomerFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 25; $i++) {
            $customer = new Customer();
            $customer->setName($faker->name())
                ->setEmail($faker->email());
            $manager->persist($customer);
        }

        $manager->flush();
    }
}
