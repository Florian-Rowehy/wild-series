<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends BaseFixtures
{
    public function load(ObjectManager $manager)
    {
        parent::load($manager);
        $this->createMany(User::class, 15, function (User $user, int $i){
            $user
                ->setEmail($this->faker->email)
                ->setRoles(['ROLE_CONTRIBUTOR'])
                ->setPassword($this->passwordEncoder->encodePassword(
                    $user,
                    'contributorpassword'
                ));
            ;
        }, 'contributor');

        $this->createMany(User::class, 4, function (User $user, int $i){
            $user
                ->setEmail($this->faker->safeEmail)
                ->setRoles(['ROLE_ADMIN'])
                ->setPassword($this->passwordEncoder->encodePassword(
                    $user,
                    'adminpassword'
                ))
            ;
        }, 'admin');
        $manager->flush();
    }
}