<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use App\Security\EmailVerifier;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{

    public function __construct(private EmailVerifier $emailVerifier) {}

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('mailer@example.com', 'AcmeMailBot'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
                    ->context([
                        'user' => $user,
                    ])
            );

            $this->addFlash('success', 'Inscription réussie ! Veuillez vérifier votre e-mail pour activer votre compte.');

            return $this->redirectToRoute('app_register');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        // Récupère l'ID de l'utilisateur depuis le lien
        $userId = $request->get('id');
        if (!$userId) {
            $this->addFlash('verify_email_error', 'Lien invalide ou incomplet.');
            return $this->redirectToRoute('app_register');
        }

        // Récupère le bon utilisateur
        $user = $entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            $this->addFlash('verify_email_error', 'Utilisateur introuvable.');
            return $this->redirectToRoute('app_register');
        }

        // Valide le lien
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
            $this->addFlash('success', 'Votre adresse e-mail a été vérifiée avec succès !');

            return $this->redirectToRoute('all_ingredient');
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash(
                'verify_email_error',
                $translator->trans($exception->getReason(), [], 'VerifyEmailBundle')
            );

            return $this->redirectToRoute('app_register');
        }
    }

    #[Route('/test/email', name: 'test_email')]
    public function test(SendMailService $mailer)
    {    
        $prenom = 'Junior';
        $nom = 'Fresnel';
        $mailer->send(
            'no-reply@monsite.net',
            'destinataire@monsite.net',
            'Confirme ton Mail',
            'test',
            ['prenom' => $prenom, 'nom' => $nom]
        );
        $this->addFlash("success", "message envoyer");
        return $this->redirectToRoute('app_register');
    }


  /*   #[Route('/test/email', name: 'test_email')]
    public function tests(SendMailService $mailer, UserRepository $userRepository)
    {
        $users = $userRepository->findAll();

        foreach ($users as $user) {
            if ($user->getEmail()) {
                $mailer->send(
                    'no-reply@monsite.net',
                    $user->getEmail(),
                    'Titre de mon message',
                    'test',
                    ['prenom' => $user->getPrenom(), 'nom' => $user->getNom()]
                );
            }
        }
        $this->addFlash("success", "message envoyer");
        return $this->redirectToRoute('app_register');
    } */
}
