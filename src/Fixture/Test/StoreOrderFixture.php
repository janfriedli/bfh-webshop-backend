<?php

namespace App\Fixture\Test;

use App\Entity\StoreOrder;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;

class StoreOrderFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $products = new ArrayCollection();
        for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $product->setTitle('product'.$i);
            $product->setDescription('description' .$i);
            $product->setImgUrl('https://img.url/test' .$i. '.jpg');
            $product->setPrice($i);
            $product->setQuantity($i);
            $manager->persist($product);
            $products->add($product);
        }

        /**
         * first order
         */
        $order = new StoreOrder();
        $order->setCountry('CH0');
        $order->setFullname('testName0');
        $order->setPaid(false);
        $order->setStreet('testStreet0');
        $order->setZip('testZip0');
        $order->addProduct($products->get(0));
        $order->addProduct($products->get(1));
        $order->addProduct($products->get(2));
        $manager->persist($order);

        /**
         * second order
         */
        $order = new StoreOrder();
        $order->setCountry('CH1');
        $order->setFullname('testName1');
        $order->setPaid(false);
        $order->setStreet('testStreet1');
        $order->setZip('testZip1');
        $order->setProducts($products);
        $manager->persist($order);

        $manager->flush();
    }
}