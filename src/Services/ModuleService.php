<?php

namespace App\Services;

use App\Entity\Module;
use App\Entity\User;
use App\Repository\ModuleRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ModuleService
{
    /**
     * @var ModuleRepository
     */
    private $moduleRepository;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var Security
     */
    private $security;

    /**
     * ModuleService constructor.
     * @param ModuleRepository $moduleRepository
     */
    public function __construct(
        ModuleRepository $moduleRepository,
        ValidatorInterface $validator,
        Security $security
    )
    {
        $this->moduleRepository = $moduleRepository;
        $this->validator = $validator;
        $this->security = $security;
    }

    public function addModule(Request $request): array
    {
        $title = $request->get('title');
        $content = $request->get('content');

        $module = new Module();
        $module->setTitle($title);
        $module->setContent($content);
        $module->setUser($this->security->getUser());

        $errors = $this->validator->validate($module);

        if ($errors->count() == 0) {
            $this->moduleRepository->add($module);
        }


        return [
            'module_id' => !empty($module) ? $module->getId() : null,
            'errors' => $errors,
            'modules' => $this->getUserModules($module->getUser())
        ];
    }

    /**
     * @param Request $request
     * @return array|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeModule(Request $request): ?array
    {
        $moduleId = $request->get('module_id');
        $module = $this->moduleRepository->find($moduleId);
        $this->moduleRepository->remove($module);

        return $this->getUserModules($module->getUser());
    }

    /**
     * @param User $user
     * @return Collection|null
     */
    public function getUserModules(User $user): ?array
    {
        return $this->moduleRepository->getUserModules($user->getId());
    }
}