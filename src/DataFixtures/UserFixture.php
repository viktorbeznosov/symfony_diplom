<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $admin = new User();

        $admin
            ->setEmail('admin@mail.ru')
            ->setFirstName('Admin')
            ->setPassword($this->passwordEncoder->encodePassword($admin, '123456'))
            ->setIsActive(true)
            ->setApiToken(bin2hex(random_bytes(15)))
            ->setSubscribeType(1)
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $user = new User();

        $user
            ->setEmail('user@mail.ru')
            ->setFirstName('User')
            ->setPassword($this->passwordEncoder->encodePassword($user, '123456'))
            ->setIsActive(true)
            ->setApiToken(bin2hex(random_bytes(15)))
            ->setSubscribeType(1)
            ->setRoles(['ROLE_USER']);

        $manager->persist($user);

        $manager->flush();
    }
}
