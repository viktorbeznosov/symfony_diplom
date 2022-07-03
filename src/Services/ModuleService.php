<?php

namespace App\Services;

use App\Entity\Module;
use App\Entity\User;
use App\Repository\ModuleRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Request;
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

    private const WORDCASE = [
        0 => 'article_word_nominative_case',
        1 => 'article_word_genitive_case',
        2 => 'article_word_dative_case',
        3 => 'article_word_accusative_case',
        4 => 'article_word_creative_case',
        5 => 'article_word_prepositional_case',
        6 => 'article_word_plural'
    ];

    private const PARAGRAPH = 'Lorem Ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';

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

    /**
     * @param Request $request
     * @return array
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
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

    /**
     * Возвращает массив контентов модулей с подставленными данными из request
     * @param Request $request
     * @return array
     */
    public function getUserModuleContents(Request $request): array
    {
        $result = [];

        foreach ($this->getUserModules($this->security->getUser()) as $module) {
            $content = $this->parseModuleContent($request, $module->getContent());

            $result[] = $content;
        }

        return $result;
    }

    /**
     * Заменяет плейсхолдеры на данные из request
     * @param Request $request
     * @param string $content
     * @return string
     */
    public function parseModuleContent(Request $request, string $content): string
    {
        $regPlaceholder = '/{{([\s\S]+?)}}/';

        $content = preg_replace_callback($regPlaceholder, function ($matches) use ($request){

            $matches[1] = $this->getPlaceholder($request, trim($matches[1]));

            return $matches[1];
        }, $content);

        return $content;
    }

    /**
     * По плейсхолдеру возвращает данные из request
     * @param Request $request
     * @param string $placeholder
     * @return string
     */
    public function getPlaceholder(Request $request, string $placeholder): string
    {
        $data = $request->request->all();

        switch ($placeholder) {
            case (strpos($placeholder, 'keyword')):
                preg_match('/keyword\|morph\([0-6]\)/', $placeholder, $matches);
                if (!empty($matches[0])) {
                    preg_match('/\d/', $matches[0], $number);
                    $number = $number[0];
                    $key = self::WORDCASE[$number];

                    return (!empty($data[$key])) ? $data[$key] : '';
                }
                return (!empty($data['article_word_nominative_case'])) ? $data['article_word_nominative_case'] : '';
                break;
            case 'title':
                return (!empty($data['articte_title'])) ? $data['articte_title'] : '';
                break;
            case 'paragraph':
                return self::PARAGRAPH;
                break;
            case 'paragraphs':
                $result = '';
                $count = rand(2, 5);
                for ($i = 1; $i <= $count; $i++) {
                    $result .= '<p>' . self::PARAGRAPH .  '</p>';
                }
                return $result;
                break;
            case 'imageSrc':
                return '<img class="mr-3" src="/uploads/images/themes/food1.jpg" width="250" height="250" alt="">';
                break;
            default:
                return '';
                break;

        }
    }
}