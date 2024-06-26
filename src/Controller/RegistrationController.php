<?php

namespace HouseOfAgile\NakaCMSBundle\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use HouseOfAgile\NakaCMSBundle\Component\Communication\NotificationManager;
use HouseOfAgile\NakaCMSBundle\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        ManagerRegistry $doctrine,
        Mailer $mailer,
        UserPasswordHasherInterface $userPasswordHasher,
        VerifyEmailHelperInterface $verifyEmailHelper
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $entityManager = $doctrine->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                $signatureComponents = $verifyEmailHelper->generateSignature(
                    'app_verify_email',
                    $user->getId(),
                    $user->getEmail(),
                    ['id' => $user->getId()]
                );

                $mailer->sendVerifyMessageToNewMember($user, $signatureComponents->getSignedUrl());

                $this->addFlash('success', 'flash.register.checkYourEmailAndVerifyYourEmail');

                return $this->redirectToRoute('app_homepage');
            }
        }

        return $this->render('@NakaCMS/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify', name: 'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        VerifyEmailHelperInterface $verifyEmailHelper,
        NotificationManager $notificationManager,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $userRepository->find($request->query->get('id'));
        if (!$user) {
            throw $this->createNotFoundException();
        }
        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail(),
            );
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());
            return $this->redirectToRoute('app_register');
        }
        $user->setIsVerified(true);
        $entityManager->flush();
        $this->addFlash('success', 'Account Verified! You can now log in.');
        $notificationManager->notificationNewMemberVerified($user);

        return $this->redirectToRoute('app_login');
    }


    #[Route('/verify/resend', name: 'app_verify_resend_email')]
    public function resendVerifyEmail(
        Request $request,
        UserRepository $userRepository,
        Mailer $mailer,
        VerifyEmailHelperInterface $verifyEmailHelper,
        EntityManagerInterface $entityManager
    ): Response {
        $email = $request->query->get('email', '');

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);

            if ($user && !$user->getIsVerified()) {
                $now = new \DateTime();
                $lastSent = $user->getLastVerificationEmailSentAt();

                if ($lastSent && $lastSent->diff($now)->i < 30) {
                    $this->addFlash('error', 'You can only request a new verification email every 30 minutes.');
                } else {
                    $signatureComponents = $verifyEmailHelper->generateSignature(
                        'app_verify_email',
                        $user->getId(),
                        $user->getEmail(),
                        ['id' => $user->getId()]
                    );

                    $mailer->sendVerifyMessageToNewMember($user, $signatureComponents->getSignedUrl());

                    $user->setLastVerificationEmailSentAt($now);
                    $entityManager->flush();

                    $this->addFlash('success', 'authenticate.flash.verifyEmail.emailSent');
                    return $this->redirectToRoute('app_verify_resend_email', ['email' => $email]);
                }
            } else {
                $this->addFlash('error', 'authenticate.flash.verifyEmail.emailNotFoundOrAlreadyVerified');
            }
        }

        return $this->render('@NakaCMS/registration/resend_verify_email.html.twig', [
            'email' => $email,
        ]);
    }
}
