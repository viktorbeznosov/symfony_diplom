<?php

namespace App\Controller;

use App\Services\ModuleService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModuleController extends AbstractController
{
    /**
     * @var ModuleService
     */
    private $moduleService;

    /**
     * ModuleController constructor.
     * @param ModuleService $moduleService
     */
    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }

    /**
     * @Route("module/add", name="app_module_add", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function addModule(Request $request): Response
    {
        $data = $this->moduleService->addModule($request->request->all());
        $errors = $data['errors'];

        foreach ($errors as $error) {
            $this->addFlash('error', $error->getMessage());
        }

        if ($errors->count() == 0) {
            $this->addFlash('flash_message', 'Модуль успешно добавлен');
        }

        return $this->redirectToRoute('app_account_modules');
    }

    /**
     * @Route("module/remove", name="app_module_remove", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function removeModule(Request $request): Response
    {
        $result = $this->moduleService->removeModule($request->request->all());
        $this->addFlash('delete_module_message', 'Модуль удален');

        return $this->redirectToRoute('app_account_modules');
    }
}
