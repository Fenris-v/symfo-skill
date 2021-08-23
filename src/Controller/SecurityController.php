<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Model\UserRegistrationFormModel;
use App\Form\UserRegistrationFormType;
use App\Security\LoginFormAuthenticator;
use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login/", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $errors = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'errors' => $errors
            ]
        );
    }

    /**
     * @Route("/logout/", name="app_logout")
     */
    public function logout()
    {
        throw new LogicException(
            'This method can be blank - it will be intercepted by the logout key on your firewall.'
        );
    }

    /**
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param UserAuthenticatorInterface $userAuthenticator
     * @param LoginFormAuthenticator $loginFormAuthenticator
     * @param EntityManagerInterface $em
     * @param Mailer $mailer
     * @return Response|null
     * @throws TransportExceptionInterface
     * @Route("/register/", name="app_register")
     */
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        LoginFormAuthenticator $loginFormAuthenticator,
        EntityManagerInterface $em,
        Mailer $mailer
    ): ?Response {
        $form = $this->createForm(UserRegistrationFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserRegistrationFormModel $userModel */
            $userModel = $form->getData();

            $user = new User();

            $user->setEmail($userModel->email)
                ->setFirstName($userModel->firstName)
                ->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $userModel->plainPassword
                    )
                );

            $em->persist($user);
            $em->flush();

            $mailer->sendWelcomeMail($user);

            return $userAuthenticator->authenticateUser(
                $user,
                $loginFormAuthenticator,
                $request
            );
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
