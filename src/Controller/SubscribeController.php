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
        return $this->json($subscribeService->subscribeIssue($security->getUser()->getId(), $request->request->get('subscribe')));
    }
}
