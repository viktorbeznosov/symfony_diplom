<?php

namespace App\Services;


use App\Repository\SubscribeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class SubscribeService
{
    /**
     * @var SubscribeRepository
     */
    private $subscribeRepository;
    /**
     * @var Security
     */
    private $security;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * SubscribeService constructor.
     * @param SubscribeRepository $subscribeRepository
     */
    public function __construct(
        UserRepository $userRepository,
        SubscribeRepository $subscribeRepository,
        EntityManagerInterface $entityManager,
        Security $security
    )
    {
        $this->subscribeRepository = $subscribeRepository;
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    public function getAllSubscribes()
    {
        $allSubsctibes = $this->subscribeRepository->findAll();

        foreach ($allSubsctibes as $key => &$subscribe) {
            $subscribe->features_array = (!empty($subscribe->getFeatures())) ? json_decode($subscribe->getFeatures()) : array();
            if ($this->security->getUser()->getSubscribe()->getId() == $subscribe->getId()) {
                $subscribe->current = true;
                if (isset($allSubsctibes[$key + 1])) {
                    $allSubsctibes[$key + 1]->can_issue = true;
                }
            }
        }

        return $allSubsctibes;
    }

    public function subscribeIssue($userId, $subscribeCode)
    {
        $user = $this->userRepository->find($userId);
        $subscribe = $this->subscribeRepository->findOneBy(['code' => $subscribeCode]);

        $user->setSubscribe($subscribe);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->getAllSubscribes();
    }
}