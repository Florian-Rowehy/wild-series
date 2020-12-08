<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use App\Entity\Season;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EpisodeFixtures extends BaseFixtures implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        parent::load($manager);
        $regex = '/Season_/';
        $refEpisode = 0;
        foreach ($this->referenceRepository->getReferences() as $ref => $key) {
            if (!preg_match($regex, $ref))
                continue;
            $randNbEpisode = rand(3, 12);
            $this->createMany(Episode::class, $randNbEpisode, function(Episode $episode, $i) use ($ref)
            {
                $episode
                    ->setTitle($this->faker->sentence())
                    ->setNumber($i+1)
                    ->setSynopsis($this->faker->text())
                    ->setSeason($this->getReference($ref))
                ;
            }, $refEpisode);
            $refEpisode++;
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }
}
