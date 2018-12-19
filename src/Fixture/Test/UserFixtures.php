<?php

namespace App\Fixture\Test;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
         $user = new User();
         $user->setUsername("testuser");
         $user->setPassword($this->passwordEncoder->encodePassword($user,'test'));
         $roles[] = 'ROLE_ADMIN';
         $user->setRoles($roles);
         $manager->persist($user);

        $manager->flush();
    }
}
