<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');


        for ($i = 0; $i < 25; $i++) {
            $product = new Product();
            $product->setName($faker->unique->word())
                ->setDescription($faker->paragraph())
                ->setPrice($faker->randomFloat(1, 200, 1000));
            $manager->persist($product);
        }

        $manager->flush();
    }
}
