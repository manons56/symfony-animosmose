<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use App\Entity\Pictures;
use App\Entity\Products;
use App\Entity\Variants;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $category = new Categories();
        $category->setName("Aliment chien");

        $image = new Pictures();
        $image->setUrl("img/products/croquette_truite_canard.png")
            ->setAlt("Paquet de croquette");



        $product = new Products();
        $product->setName("Primordial Adulte Truite et Canard")
            ->setDescription("La gamme Primordial respecte l’alimentation naturelle et ancestrale de nos chiens, en apportant un haut pourcentage de viande et poisson frais, fruits et légumes. Ces croquettes aident à notre chien d’âge adulte (à partir d’un an) à avoir une meilleure digestion, et une vie plus saine. Une alimentation Premium sans céréales et avec la combinaison de la meilleure viande de truite et de canard. Les croquettes Primordial Adult Truite et Canard sont un aliment complet avec une recette sans céréales, holistique et avec des ingrédients 100% naturels. Le 70% sont des protéines animales et l’autre 30% sont un mélange des meilleures fruits et légumes. Délicieux et très sain pour le chien.")
            ->setComposition("truite fraîche (35%), la viande de canard séché (25%), les pois, les pommes de terre, graisse de poulet (10%), les haricots, les graines de lin (2,5%), pulpe de betterave séchée, levure, algues en poudre ( 0,3%), oligofructose (0,2%), le produit de levure (0,2%), Mojave yucca (0,03%), poudre de racine de pissenlit (Taraxacum officinale W.) (0,02%), séché grenade (Punica granatum) (0,02%), l’ananas séché (ananas sativus L.) (0,02%), séché hanche de fruits (Rosa Canina LR, pendulina L.) (0,002), la glucosamine, le sulfate de chondroïtine, extrait de romarin.")
            ->setAnalyticsComponents("3a672a Vitamine A 21000 UI, vitamine D3 1400 UI 3a700 vitamine E 180 mg, E4 cuivrique pentahydrate de sulfate 59 mg, E1 siderite 62 mg, E5 manganeux oxyde 77 mg, E6 sulfate de zinc monohydraté 186 mg d’iodure de potassium E2 4,85 mg, E8 Selena soude 0,35 mg.")
            ->setNutritionnalAdditive("Humidité 8%, protéine brute de 30%, matières grasses brutes et 19%, cendres brutes 8,4%, 2,4% de fibres brutes.")
            ->setIsNew(true)
            ->setIsBestseller(false)
            ->setCategoryId($category)
            ->setCapacity("Choisir une option")
            ->setImgId($image);



        $variant1 = new Variants();
        $variant1->setPrice(22)
            ->setLabel("2kg")
            ->setProductId($product)
            ->setIsDefault(false);

        $variant2 = new Variants();
        $variant2->setPrice(69)
          ->setLabel("12kg")
          ->setProductId($product)
          ->setIsDefault(true);


        $manager->persist($category);
        $manager->persist($image);
        $manager->persist($product);
        $manager->persist($variant1);
        $manager->persist($variant2);

        $manager->flush();
    }
}
