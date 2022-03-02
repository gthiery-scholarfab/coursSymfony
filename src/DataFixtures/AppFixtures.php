<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use App\Entity\Categorie;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $c1 = new Categorie();
        $c1->setNom("Categorie1");
        $manager->persist($c1);
        $c2 = new Categorie();
        $c2->setNom("Categorie2");
        $manager->persist($c2);
        $p1 = new Produit();
        $p1->setNom("Produit1");
        $p1->setDescription("Description du produit P1");
        $p1->setPrix(53.65);
        $p1->setStock(4);
        $p1->setImage("fsjdlfjsjflks");
        $p1->setDateCreation(new \DateTime());
        $p1->setCategorie($c1);
        $manager->persist($p1);
        //Création du deuxième produit
        $p2 = new Produit();
        $p2->setNom("Produit2");
        $p2->setDescription("Description du produit P2");
        $p2->setPrix(53.65);
        $p2->setStock(4);
        $p2->setImage("fsjdlfjsjflks");
        $p2->setDateCreation(new \DateTime());
        $p2->setCategorie($c2);
        $manager->persist($p2);
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
