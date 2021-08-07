<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;

class LoginFormAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private UserRepository $userRepository
    ) {
    }

    /**
     * Проверяет маршрут для авторизации
     * @param Request $request
     * @return bool|null
     */
    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'app_login' &&
            $request->isMethod('POST');
    }

    /**
     * Вызывает начало авторизации, если передан нужный маршрут
     * @param Request $request
     * @return PassportInterface
     */
    public function authenticate(Request $request): PassportInterface
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $csrf = $request->request->get('_csrf_token');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        $user = $this->userRepository->findOneBy(['email' => $email]);
        if ($user && !$user->getIsActive()) {
            throw new CustomUserMessageAuthenticationException('Уходи бабайка!');
        }

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrf),
                new RememberMeBadge()
            ]
        );
    }

    /**
     * Успешная авторизация
     * @param Request $request
     * @param TokenInterface $token
     * @param string $firewallName
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    /**
     * Ошибка при авторизации
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, [$exception->getMessage()]);

        return null;
    }
}
