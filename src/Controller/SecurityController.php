<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class SecurityController extends AbstractController
{
    /**
     * @throws TransportExceptionInterface
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $transport = new EsmtpTransport('smtp.mailersend.net', 587, false);
        $transport->setUsername('MS_avxfPp@trial-jy7zpl99y8pl5vx6.mlsender.net');
        $transport->setPassword('mIjhHd6JQnj8tmho');
        $mailer = new Mailer($transport);
        $email = (new Email())
            ->from('contact@duoimportmdg.com')
            ->to('thierry1804@yopmail.com')
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>')
        ;

        $mailer->send($email);
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank -
        it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/login-check', name: 'login_check')]
    public function check(): never
    {
        throw new \LogicException('You must configure the check path to be handled by the firewall using
        form_login in your security firewall configuration.');
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/request-login-link/{code}', name: 'app_request_login_link')]
    public function requestLoginLink(LoginLinkHandlerInterface $loginLinkHandler, UserRepository $userRepository,
                                     EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher,
                                     string $code): Response
    {
        $user = $userRepository->findOneBy(['code' => $code]);

        if ($user === null) {
            $user = new User();
            $user->setCode($code);
            $user->setEmail('user' . bin2hex($code) . '@duoimport.mg');
            // This is required for the user to be able to login using the login link
            $hashedPassword = $passwordHasher->hashPassword($user, $code);
            $user->setPassword($hashedPassword);
            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();
        }

        $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
        $loginLink = $loginLinkDetails->getUrl();

        return new Response('Login link : ' . $loginLink, Response::HTTP_OK);
    }
}
