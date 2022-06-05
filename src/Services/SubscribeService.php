<?php

namespace App\Services;

use App\Entity\Subscribe;
use App\Entity\User;
use App\Helpers\TimeHelper;
use App\Repository\SubscribeRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
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
     * @var ContainerBagInterface
     */
    private $params;

    /**
     * SubscribeService constructor.
     * @param UserRepository $userRepository
     * @param SubscribeRepository $subscribeRepository
     * @param EntityManagerInterface $entityManager
     * @param Security $security
     * @param ContainerBagInterface $params
     */
    public function __construct(
        UserRepository $userRepository,
        SubscribeRepository $subscribeRepository,
        EntityManagerInterface $entityManager,
        Security $security,
        ContainerBagInterface $params
    )
    {
        $this->subscribeRepository = $subscribeRepository;
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->params = $params;
    }

    /**
     * @return \App\Entity\Subscribe[]
     */
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

    /**
     * @about Оформление подписки
     * @param $userId
     * @param $subscribeCode
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function subscribeIssue($userId, $subscribeCode)
    {
        $user = $this->userRepository->find($userId);
        $subscribe = $this->subscribeRepository->findOneBy(['code' => $subscribeCode]);
        $subscribeIssuedTill = ($subscribeCode != 'free') ? Carbon::now()->modify("+ {$this->params->get('subscribe.period')} days") : null;

        $user->setSubscribe($subscribe);
        $user->setSubscribeIssuedTill($subscribeIssuedTill);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return array(
            'subscribes' => $this->getAllSubscribes(),
            'subscribe_code' => strtoupper($user->getSubscribe()->getCode()),
            'subscribe_issued_till' => Carbon::create($user->getSubscribeIssuedTill())->format('d.m.Y'),
            'subscribe_expires_till_string' => $this->getUserSubscribeExpiresAfterString($user),
            'next_subscribe_code' => $this->getNextSubscribe($user) ? $this->getNextSubscribe($user)->getCode() : null
        );
    }

    /**
     * @about проверяет, истекла ли подписка
     * @param User $user
     * @return bool
     */
    public function userSubscribeIsExpired(User $user): bool
    {
        return Carbon::create($user->getSubscribeIssuedTill()) < Carbon::now();
    }

    /**
     * @about Выодит стоку, сколько осталось до истечения срока подписки
     * @param User $user
     * @return string
     */
    public function getUserSubscribeExpiresAfterString(User $user): ?string
    {
        if (empty($user->getSubscribeIssuedTill())) {
            return null;
        }
        if ($this->userSubscribeIsExpired($user)) {
            return 'Подписка истекла';
        }
        $dateDiff = Carbon::create($user->getSubscribeIssuedTill())->diff(Carbon::now());
        $dateDiffStrring = TimeHelper::getDateIntervalString($dateDiff);
        $result = 'Подписка истекает через ' . $dateDiffStrring;

        return $result;
    }

    /**
     * @about Возвращает следуюущую подписку
     * @param User $user
     * @return Subscribe|null
     */
    public function getNextSubscribe(User $user): ?Subscribe
    {
        return $this->subscribeRepository->getNextSubscribe($user->getSubscribe());
    }
}