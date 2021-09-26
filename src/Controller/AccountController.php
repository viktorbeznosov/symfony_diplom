<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    /**
     * @Route("/account", name="app_account_dashboard")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function index(): Response
    {
        return $this->render('account/index.html.twig', [

        ]);
    }

    /**
     * @Route("/account/article_create", name="app_account_article_create")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function create(): Response
    {
        return $this->render('account/create.html.twig', [

        ]);
    }

    /**
     * @Route("/account/articles_history", name="app_account_articles_history")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function history(): Response
    {
        return $this->render('account/history.html.twig', [

        ]);
    }

    /**
     * @Route("/account/subscribe", name="app_account_subscribe")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function subscribe(): Response
    {
        return $this->render('account/subscribe.html.twig', [

        ]);
    }

    /**
     * @Route("/account/profile", name="app_account_profile")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function profile(): Response
    {
        return $this->render('account/profile.html.twig', [

        ]);
    }

    /**
     * @Route("/account/modules", name="app_account_modules")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function modules(): Response
    {
        return $this->render('account/modules.html.twig', [

        ]);
    }
}