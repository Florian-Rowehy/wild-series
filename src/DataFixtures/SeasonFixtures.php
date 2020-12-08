<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SeasonFixtures extends BaseFixtures implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        parent::load($manager);
        $regex = '/^program_(\d+)/';
        $refSeason = 0;
        foreach ($this->referenceRepository->getReferences() as $ref => $key) {
            if (!preg_match($regex, $ref))
                continue;
            $randNbSeason = rand(3, 12);
            $releaseYear = 2021 - $randNbSeason - rand(0,10);
            $this->createMany(Season::class, $randNbSeason, function(Season $season, $i) use ($ref, &$releaseYear)
            {
                $season
                    ->setProgram($this->getReference($ref))
                    ->setNumber($i+1)
                    ->setYear($releaseYear)
                    ->setDescription($this->faker->paragraph(2))
                ;
                $releaseYear++;
            }, $refSeason);
            $refSeason++;
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }
}
