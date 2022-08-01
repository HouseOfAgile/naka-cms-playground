<?php

namespace HouseOfAgile\NakaCMSBundle\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use HouseOfAgile\NakaCMSBundle\Entity\AdminUser;
use HouseOfAgile\NakaCMSBundle\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends BaseFixture
{
    
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->passwordHasher = $passwordHasher;
    }
    
    
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(10, 'main_users', function ($i) {
            $user = new User();
            $user->setEmail(sprintf('spacebar%d@example.com', $i));
            $user->setFirstName($this->faker->firstName);
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                'engage'
            );
            $user->setPassword($hashedPassword);
            return $user;
        });
        $this->createMany(3, 'admin_users', function($i) {
            $user = new AdminUser();
            $user->setEmail(sprintf('admin%d@thespacebar.com', $i));
            $user->setFirstName($this->faker->firstName);
            $user->setRoles(['ROLE_ADMIN']);
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                'engage'
            );
            $user->setPassword($hashedPassword);
            return $user;
        });
        $manager->flush();
    }
}
