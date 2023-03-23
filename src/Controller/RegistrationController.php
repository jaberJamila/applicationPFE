<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use App\service\JWTService;
use App\service\SendMailService;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator,  EntityManagerInterface $entityManager, SendMailService $mail, JWTService $jwt): Response
    {
        $user = new Users();
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


            // On génére le JWT de l'utilisateur
            // On crée le header
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            // on crée le payload
            $payload = [
                'user_id' => $user->getId()
            ];

            // On génére le token
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));
           

            // on envoie un mail
            $mail->send(
                'no-reply@monsite.net',
                $user->getEmail(),
                'Activation de votre compte sur Analysis' ,
                'register' ,
                compact('user', 'token')
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser($token, JWTService $jwt, UsersRepository $usersRepository, EntityManagerInterface $em): Response
    {
        // on verifier si le token est valide, n'a pas expiré et n'a pas été modifier
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))){
         

            // on récupere le payload
            $payload = $jwt->getPayload($token);
            // on récupere le user du token
            $user = $usersRepository->find($payload['user-id']);

            // on vérifie que l'utilisation existe et n'a pas encore activé son compte
            if($user && !$user->getIsVerified()){
                $user->setIsVerfified(true);
                $em->flush($user);
                $this->addFlash('success', 'utilisateur active');
                return $this->redirectToRoute('profile_index');
                
            }
        }

        //ici un probleme se pose dans le token
        $this->addFlash('danger', 'le token est invalide ou a expiré');
        return $this->redirectToRoute('app_login');
    }
    #[Route('/renvoiverif', name: 'resend_verif')]
    public function resendVerif(JWTService $jwt, SendMailService $mail, UsersRepository $usersRepository): Response
    {
        $user = $this->getUser();

        if(!$user){
            $this->addFlash('danger', 'Vous devez etre connecté pour acceder à cette page');
            return $this->redirectToRoute('app_login');
        }

        if($user->getIsVerified()){
            $this->addFlash('warning', 'cet utilisateur est deja active');
            return $this->redirectToRoute('profile_index');
        }
         // On génére le JWT de l'utilisateur
            // On crée le header
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            // on crée le payload
            $payload = [
                'user_id' => $user->getId()
            ];

            // On génére le token
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));
           

            // on envoie un mail
            $mail->send(
                'no-reply@monsite.net',
                $user->getEmail(),
                'Activation de votre compte sur Analysis' ,
                'register' ,
                compact('user', 'token')
            );

            if($user->getIsVerified()){
                $this->addFlash('success', 'Email de vérification envoyer');
                return $this->redirectToRoute('profile_index');
            }
    }
}
