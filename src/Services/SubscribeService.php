<?php

namespace App\Services;


use App\Entity\User;
use App\Helpers\TimeHelper;
use App\Repository\ArticleRepository;
use App\Repository\SubscribeRepository;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * SubscribeService constructor.
     * @param SubscribeRepository $subscribeRepository
     */
    public function __construct(
        UserRepository $userRepository,
        SubscribeRepository $subscribeRepository,
        ArticleRepository $articleRepository,
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
        $this->articleRepository = $articleRepository;
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
     * @return \App\Entity\Subscribe[]
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
            'subscribe_issued_till' => Carbon::create($user->getSubscribeIssuedTill())->format('d.m.Y')
        );
    }

    /**
     * @about Выодит стоку, сколько осталось до истечения срока подписки
     * @param User $user
     * @return string
     */
    public function getUserSubscribeExpiresAfterString(User $user): string
    {
        $dateDiff = Carbon::create($user->getSubscribeIssuedTill())->diff(Carbon::now());
        $dateDiffStrring = TimeHelper::getDateIntervalString($dateDiff);
        $result = 'Подписка истекает через  ' . $dateDiffStrring;

        return $result;
    }
}