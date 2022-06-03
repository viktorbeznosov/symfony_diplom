<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\ThemeRepository;
use App\Services\ArticleService;
use App\Services\SubscribeService;
use App\Services\ThemeDBService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use App\Form\UserAccountProfileFormType;
use App\Form\Model\UserAccountProfileFormModel;
use App\Entity\User;

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
    public function create(ThemeRepository $repository, ThemeDBService $themeService): Response
    {
        return $this->render('account/create.html.twig', [
            'themes' => $themeService->getThemes(),
        ]);
    }

    /**
     * @Route("/account/articles_history", name="app_account_articles_history")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function history(
        ArticleService $articleService,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        $pagination = $articleService->getArticlesHistory($request, $paginator);

        return $this->render('account/history.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/account/article/{id}", name="app_account_article")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     * @param Request $request
     * @param ArticleService $articleService
     * @return Response
     */
    public function article(
        $id,
        ArticleRepository $articleRepository
    ): Response
    {
        $article = $articleRepository->findOneBy(['id' => $id]);
        if (!$this->isGranted('MANAGE', $article)) {
            throw $this->createAccessDeniedException('Доступ запрещен');
        }

        return $this->render('account/article.html.twig', array(
            'article' => $article
        ));
    }

    /**
     * @Route("/account/subscribe", name="app_account_subscribe")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function subscribe(
        SubscribeService $subscribeService,
        Security $security
    ): Response
    {
        $allSubscribes = $subscribeService->getAllSubscribes();

        return $this->render('account/subscribe.html.twig', [
            'subscribes' => $allSubscribes
        ]);
    }

    /**
     * @Route("/account/profile", name="app_account_profile")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function profile(
        Security $security,
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $em
    ): Response {
        $user = $security->getUser();
        /** @var UserAccountProfileFormModel $userModel */
        $userModel = new UserAccountProfileFormModel();
        $userModel->firstName = $user->getFirstName();
        $userModel->email = $user->getEmail();

        $form = $this->createForm(UserAccountProfileFormType::class, $userModel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserAccountProfileFormModel $userModel */
            $userModel = $form->getData();

            $password = $userModel->password ? $passwordEncoder->encodePassword($user, $userModel->password) : $user->getPassword();

            $user
                ->setEmail($userModel->email)
                ->setFirstName($userModel->firstName)
                ->setPassword($password);

            $em->persist($user);
            $em->flush();

            $this->addFlash('flash_message', 'Профиль успешно изменен');
        }

        return $this->render('account/profile.html.twig', [
            'user_id' => $user->getId(),
            'api_token' => $user->getApiToken(),
            'showError' => $form->isSubmitted(),
            'userFrofileForm' => $form->createView(),
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

    /**
     * @Route("account/change-api-token", name="app_account_change_api_token")
     */
    public function changeApiToken(
        Request $request,
        EntityManagerInterface $em,
        Security $security
    ) {
        $userRepository = $em->getRepository(User::class);

        while ($userRepository->isExistAnotherUserByApiToken($security->getUser()->getId(), $apiToken = bin2hex(random_bytes(15)))) {
        }

        $user = $userRepository->find($security->getUser()->getId());
        $user->setApiToken($apiToken);

        $em->persist($user);
        $em->flush();

        return $this->json([
            'user_id' => $security->getUser()->getId(),
            'api_token' => $apiToken,
        ]);
    }
}
