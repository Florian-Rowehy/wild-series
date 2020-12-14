<?php

namespace App\DataFixtures;

use App\Service\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

abstract class BaseFixtures extends Fixture
{
    private $manager;
    protected $faker;
    private $referencesIndex  = [];
    protected $slugify;

    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
        $this->faker = \Faker\Factory::create('en_US');
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    protected function createMany(string $className, int $n, callable  $factory, ?int $ref=null )
    {
        for ($i=0; $i<$n; $i++) {
            $entity = new $className();
            $factory($entity, $i);

            $this->manager->persist($entity);

            if ($ref) {
                $this->addReference($className.'_'. $ref . "_".$i, $entity);
            } else {
                $this->addReference($className."_".$i, $entity);
            }
        }
    }

    protected function getRandomReference(string $className) {
        if (!isset($this->referencesIndex[$className])) {
            $this->referencesIndex[$className] = [];
            foreach ($this->referenceRepository->getReferences() as $key => $ref) {
                if (strpos($key, $className.'_') === 0) {
                    $this->referencesIndex[$className][] = $key;
                }
            }
        }
        if (empty($this->referencesIndex[$className])) {
            throw new \Exception(sprintf('Cannot find any references for class "%s"', $className));
        }
        $randomReferenceKey = $this->faker->randomElement($this->referencesIndex[$className]);
        return $this->getReference($randomReferenceKey);
    }
}