<?php
/**
 * Created by PhpStorm.
 * User: viktor
 * Date: 06.06.22
 * Time: 23:38
 */

namespace App\Services;


use App\Entity\User;
use App\Repository\UserRepository;

class UserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;


    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param $apiToken
     * @return User
     */
    public function getUserByApiToken($apiToken): ?User
    {
        return $this->userRepository->getUserByApiToken($apiToken);
    }
}