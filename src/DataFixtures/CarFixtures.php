<?php

namespace App\DataFixtures;

use App\Entity\Car;
use App\Enum\CarTypeEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CarFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $cars = [
            ['Peugeot', '208', CarTypeEnum::Citadine, 5, 'Rouge'],
            ['Renault', 'Clio', CarTypeEnum::Citadine, 5, 'Bleu'],
            ['Citroën', 'C3', CarTypeEnum::Citadine, 5, 'Gris'],
            ['Toyota', 'Corolla', CarTypeEnum::Berline, 5, 'Noir'],
            ['Ford', 'Focus', CarTypeEnum::Berline, 5, 'Blanc'],
            ['Volkswagen', 'Golf', CarTypeEnum::Berline, 5, 'Gris'],
            ['BMW', 'Serie 3', CarTypeEnum::Berline, 5, 'Noir'],
            ['Audi', 'A4', CarTypeEnum::Berline, 5, 'Bleu'],
            ['Mercedes', 'Classe A', CarTypeEnum::Citadine, 5, 'Argent'],
            ['Hyundai', 'i20', CarTypeEnum::Citadine, 5, 'Blanc'],
        ];

        foreach ($cars as [$brand, $model, $type, $passengers, $color]) {
            $car = new Car();
            $car->setBrand($brand);
            $car->setModel($model);
            $car->setType($type);
            $car->setPassengers($passengers);
            $car->setColor($color);

            $manager->persist($car);
        }

        for ($i = 1; $i <= 3; $i++) {
            $car = new Car();
            $car->setBrand('Iveco');
            $car->setModel('Daily ' . $i);
            $car->setType(CarTypeEnum::Utilitaire);
            $car->setPassengers(2);
            $car->setColor('Blanc');
            $car->setPtra(3500 + ($i * 500));

            $manager->persist($car);
        }

        $manager->flush();
    }
}
