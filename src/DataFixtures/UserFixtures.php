<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Repository\CustomerRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixtures extends Fixture
{

    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');

        $customers = $this->customerRepository->findAll();;

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setLastname($faker->lastname())
                ->setFirstname($faker->firstname())
                ->setEmail($faker->email())
                ->setCustomer($faker->randomElement($customers));
            $manager->persist($user);
        }

        $manager->flush();
    }
}
