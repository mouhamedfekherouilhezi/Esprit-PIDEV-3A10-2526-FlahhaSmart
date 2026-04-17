<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupère l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        // Dernier email saisi
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette méthode peut être vide, Symfony intercepte la route /logout
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/redirect-by-role', name: 'redirect_by_role')]
    public function redirectByRole(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $role = $user->getRole(); // Récupère le rôle en base (ADMINISTRATEUR, AGRICULTEUR, CLIENT)
        if ($role === 'ADMINISTRATEUR') {
            return $this->redirectToRoute('admin_dashboard');
        }
        // Pour les agriculteurs et clients, rediriger vers le forum
        return $this->redirectToRoute('thread_index');
    }
}