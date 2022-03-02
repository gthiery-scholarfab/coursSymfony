<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $p1 = new Produit();
        $p1->setNom("Produit1");
        $p1->setDescription("Description du produit P1");
        $p1->setPrix(53.65);
        $p1->setStock(4);
        $p1->setImage("fsjdlfjsjflks");
        $p1->setDateCreation(new \DateTime());
        $manager->persist($p1);
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
