<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    public function __construct(
        UserRepository $userRepository,
        UserPasswordEncoderInterface $passwordEncoder,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager
    )
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @return string
     * Куда отправляем пользователся при неудачной авторизации
     */
    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate('app_login');
    }

    /**
     * @param Request $request
     * @return bool|void
     * Является ли текущий запрос запросом на авторизацию
     */
    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'app_login' && $request->isMethod('POST');
    }

    /**
     * @param Request $request
     * @return mixed|void
     * Возвращает данные, необходимые для авторизации
     */
    public function getCredentials(Request $request)
    {
        $credentials = [
            'csrf_token' => $request->request->get('_csrf_token'),
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password')
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;

    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return UserInterface|void|null
     * Возвращает пользователя
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $csrfToken = new CsrfToken('authenticate', $credentials['csrf_token']);

        if (!$this->csrfTokenManager->isTokenValid($csrfToken)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->userRepository->findOneBy(['email' => $credentials['email']]);

        if ($user && !$user->getIsActive()){
            throw new CustomUserMessageAccountStatusException('User account is blocked');
        }

        return $user;
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool|void
     * Проверка корректности введенных данных (email/пароль)
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        $path = $this->getTargetPath($request->getSession(), $providerKey);
        return new RedirectResponse($path ?: $this->urlGenerator->generate('app_account_dashboard'));
    }
}
