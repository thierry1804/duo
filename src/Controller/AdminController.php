<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use JiriPudil\OTP\Account\SimpleAccountDescriptor;
use JiriPudil\OTP\HmacBasedOTP;
use JiriPudil\OTP\OTP;
use JiriPudil\OTP\OTPType;
use JiriPudil\OTP\TimeBasedOTP;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('PUBLIC_ACCESS')]
#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/users', name: 'app_admin_users_management')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->getUsers();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/generate-otp/{id}', name: 'app_admin_generate_otp')]
    public function generateOTP(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher,
                                ?User $user = null): Response
    {
        $newbie = false;
        $otpType = new TimeBasedOTP();
        $otp = new OTP('Duo Import MDG', $otpType);
        $secret = $otp->generateSecret();

        if ($user === null) {
            $newbie = true;
            $user = new User();
            $user->setEmail('user' . bin2hex(uniqid()) . '@duoimport.mg');
            $user->setRoles(['ROLE_USER']);
        }

        $account = new SimpleAccountDescriptor($user->getEmail(), $secret);
        $code = $otp->generate($account, 6);

        //Générer un code OTP
        $user->setCode($code);

        if ($newbie) {
            // This is required for the user to be able to login using the login link
            $hashedPassword = $passwordHasher->hashPassword($user, $code);
            $user->setPassword($hashedPassword);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'OTP généré avec succès: ' . $code);
        return $this->redirectToRoute('app_admin_users_management');
    }
}
