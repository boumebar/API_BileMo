<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Customer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CustomerFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 25; $i++) {
            $customer = new Customer();
            $customer->setName($faker->name())
                ->setEmail($faker->email())
                ->setPassword($this->hasher->hashPassword($customer, 'password'));
            $manager->persist($customer);
        }

        $manager->flush();
    }
}
