<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LandingController extends AbstractController
{
    /**
     * @Route("/", name="app_landing_main")
     */
    public function index(): Response
    {
        return $this->render('landing/index.html.twig', [
            'main_page' => true,
        ]);
    }

    /**
     * @Route("/create", name="app_landing_create")
     */
    public function create(): Response
    {
        return $this->render('landing/create.hеml.twig', []);
    }
}
