<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends BaseFixtures
{
    public function load(ObjectManager $manager)
    {
        parent::load($manager);
        $user = new User();
        $user
            ->setUsername('user')
            ->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'userpassword'
            ))
            ->setEmail('user@gmail.com');
        $manager->persist($user);

        $contributor = new User();
        $contributor
            ->setUsername('contributor')
            ->setPassword($this->passwordEncoder->encodePassword(
                $contributor,
                'contributorpassword'
            ))
            ->setEmail('contributor@gmail.com')
            ->setRoles(['ROLE_CONTRIBUTOR']);
        $manager->persist($contributor);

        $admin = new User();
        $admin
            ->setUsername('admin')
            ->setPassword($this->passwordEncoder->encodePassword(
                $admin,
                'adminpassword'
            ))
            ->setEmail('admin@gmail.com')
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $this->createMany(User::class, 15, function (User $user, int $i){
            $user
                ->setUsername($this->faker->userName)
                ->setEmail($this->faker->email)
                ->setRoles(['ROLE_CONTRIBUTOR'])
                ->setPassword($this->passwordEncoder->encodePassword(
                    $user,
                    'userpassword'
                ));
            ;
        }, 'contributor');

        $this->createMany(User::class, 4, function (User $user, int $i){
            $user
                ->setUsername($this->faker->userName)
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