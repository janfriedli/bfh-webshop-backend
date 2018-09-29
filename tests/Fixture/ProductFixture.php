<?php

namespace App\Test\Fixture;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ProductFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $product->setTitle('product '.$i);
            $product->setDescription('description'.$i);
            $manager->persist($product);
        }

        $manager->flush();
    }
}