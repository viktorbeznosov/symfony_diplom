<?php

namespace App\Controller;

use App\Services\SubscribeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class SubscribeController extends AbstractController
{
    /**
     * @param Request $request
     * @Route("/subscribe/issue", name="app_subscribe_issue")
     */
    public function issue(Request $request, SubscribeService $subscribeService, Security $security)
    {
        return $this->json($subscribeService->userSubscribeIssue($security->getUser(), $request->request->get('subscribe')));
    }

    /**
     * @param Request $request
     * @param SubscribeService $subscribeService
     * @param Security $security
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @Route("/subscribe/prolongate", name="app_subscribe_prolongate")
     */
    public function prolongate(Request $request, SubscribeService $subscribeService, Security $security)
    {
        return $this->json($subscribeService->userSubscribeProlongate($security->getUser()));
    }
}
