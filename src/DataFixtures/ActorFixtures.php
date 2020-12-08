<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ActorFixtures extends BaseFixtures implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        parent::load($manager);

        $this->createMany(Actor::class, 100, function(Actor $actor)
        {
            $faker = $this->faker;
            $actor->setName($faker->name());
            $randNbProgram = rand(0, 10);
            for ($i=0; $i<$randNbProgram; $i++) {
                $actor->addProgram($this->getRandomReference('program'));
            }
        });
        $manager->flush();
    }

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }
}
