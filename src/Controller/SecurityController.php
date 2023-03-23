<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('main');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

//    #[Route('/oubli-pass', name: 'forgotten_password')]
//    public function forgottenPassword(
//        Request                 $request,
//        UserRepository          $usersRepository,
//        TokenGeneratorInterface $TokenGenerator,
//        EntityManagerInterface  $entityManager,
//        SendMailService         $mail,
//
//
//    ): Response
//    {
//        $form = $this->createForm(ResetPasswordRequestFormType::class);
//
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            //on va recherche l'utilisateur par son mail
//
//            $user = $usersRepository->findOneByEmail($form->get('email')->getData());
//            // on vérifier si on a un utilisateur
//            if ($user) {
//                //on génére un token de réinitialisation
//                $token = $TokenGenerator->generateToken();
//                $user->setResetToken($token);
//                $entityManager->persist($user);
//                $entityManager->flush();
//
//                // on genére un lien de réinitialisation du mot de passe
//                $url = $this->generateUrl('reset_pass', ['token' => $token],
//                    UrlGeneratorInterface::ABSOLUTE_URL);
//                // on créer les données du mail
//                $context = compact('url', 'user');
//                //envoi du mail
//                $mail->send(
//                    'no-reply@analysis.com',
//                    $user->getEmail(),
//                    'Réinitialisation de mot de pass',
//                    'password_reset',
//                    $context
//                );
//
//                $this->addFlash('success', 'Email envoyé avec succés');
//                return $this->redirectToRoute('app_login');
//
//
//            }
//            // user est null
//            $this->addFlash('danger', 'Un probleme est survenu');
//            return $this->redirectToRoute('app_login');
//
//        }
//
//        return $this->render('security/reset_password_request.html.twig', ['requestPassForm' => $form->createView()
//        ]);
//    }
//
//    #[Route('/oubli-pass/{token}', name: 'reset_pass')]
//    public function resetPass(
//        string                      $token,
//        Request                     $request,
//        UserRepository              $usersRepository,
//        EntityManagerInterface      $entityManager,
//        UserPasswordHasherInterface $userpasswordhasher
//    ): Response
//    {
//        // on vérifier si on a ce token dans la base
//        $user = $usersRepository->findOneByResetToken($token);
//        if ($user) {
//            $form = $this->createForm(ResetPasswordFormType::class);
//            $form->handleRequest($request);
//            if ($form->isSubmitted() && $form->isValid()) {
//                //on efface le token
//                $user->setResetToken('');
//                $user->setPassword(
//                    $userpasswordhasher->hashPassword(
//                        $user,
//                        $form->get('password')->getData()
//                    )
//                );
//                $entityManager->persist($user);
//                $entityManager->flush();
//
//                $this->addFlash('success', 'Mot de passe changé avec succés');
//                return $this->redirectToRoute('app_login');
//
//
//            }
//
//            return $this->render('security/reset_password.html.twig', [
//                'passForm' => $form->createView()
//            ]);
//
//        }
//        $this->addFlash('danger', 'Jeton invalide');
//        return $this->redirectToRoute('app_login');
//
//    }
}
