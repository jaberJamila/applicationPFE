<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Security\UserAuthenticator;
use App\service\JWTService;
use App\service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager, SendMailService $mail, JWTService $jwt): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('contact@analysis', 'Analysis'))
                    ->to($user->getEmail())
                    ->subject('Confirmation de votre e-mail')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            // do anything else you need here, like send an email
            $this->addFlash('success', 'Vérifiez votre e-mail pour le lien de confirmation');
            return $this->redirectToRoute('app_login');


//            // On génére le JWT de l'utilisateur
//            // On crée le header
//            $header = [
//                'typ' => 'JWT',
//                'alg' => 'HS256'
//            ];
//
//            // on crée le payload
//            $payload = [
//                'user_id' => $user->getId()
//            ];
//
//            // On génére le token
//            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));
//
//
//            // on envoie un mail
//            $mail->send(
//                'no-reply@monsite.net',
//                $user->getEmail(),
//                'Activation de votre compte sur Analysis',
//                'register',
//                compact('user', 'token')
//            );
//
//            return $userAuthenticator->authenticateUser(
//                $user,
//                $authenticator,
//                $request
//            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre adresse e-mail a été vérifiée.');

        return $this->redirectToRoute('app_login');
    }

//    #[Route('/verif/{token}', name: 'verify_user')]
//    public function verifyUser($token, JWTService $jwt, UserRepository $usersRepository, EntityManagerInterface $em): Response
//    {
//        // on verifier si le token est valide, n'a pas expiré et n'a pas été modifier
//        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {
//
//
//            // on récupere le payload
//            $payload = $jwt->getPayload($token);
//            // on récupere le user du token
//            $user = $usersRepository->find($payload['user-id']);
//
//            // on vérifie que l'utilisation existe et n'a pas encore activé son compte
//            if ($user && !$user->getIsVerified()) {
//                $user->setIsVerfified(true);
//                $em->flush($user);
//                $this->addFlash('success', 'utilisateur active');
//                return $this->redirectToRoute('profile_index');
//
//            }
//        }
//
//        //ici un probleme se pose dans le token
//        $this->addFlash('danger', 'le token est invalide ou a expiré');
//        return $this->redirectToRoute('app_login');
//    }
//
//    #[Route('/renvoiverif', name: 'resend_verif')]
//    public function resendVerif(JWTService $jwt, SendMailService $mail, UserRepository $usersRepository): Response
//    {
//        $user = $this->getUser();
//
//        if (!$user) {
//            $this->addFlash('danger', 'Vous devez etre connecté pour acceder à cette page');
//            return $this->redirectToRoute('app_login');
//        }
//
//        if ($user->getIsVerified()) {
//            $this->addFlash('warning', 'cet utilisateur est deja active');
//            return $this->redirectToRoute('profile_index');
//        }
//        // On génére le JWT de l'utilisateur
//        // On crée le header
//        $header = [
//            'typ' => 'JWT',
//            'alg' => 'HS256'
//        ];
//
//        // on crée le payload
//        $payload = [
//            'user_id' => $user->getId()
//        ];
//
//        // On génére le token
//        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));
//
//
//        // on envoie un mail
//        $mail->send(
//            'no-reply@monsite.net',
//            $user->getEmail(),
//            'Activation de votre compte sur Analysis',
//            'register',
//            compact('user', 'token')
//        );
//
//        if ($user->getIsVerified()) {
//            $this->addFlash('success', 'Email de vérification envoyer');
//            return $this->redirectToRoute('profile_index');
//        }
//    }
}
