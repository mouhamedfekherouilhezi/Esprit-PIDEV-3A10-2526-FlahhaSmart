<?php

namespace App\Controller;

use App\Entity\Thread;
use App\Entity\Commentaire;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Knp\Component\Pager\PaginatorInterface;
use App\Service\AiTranslationService;
use App\Service\MailService;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        $threadCount = $em->getRepository(Thread::class)->count([]);
        $commentCount = $em->getRepository(Commentaire::class)->count([]);
        $userCount = $em->getRepository(User::class)->count([]);
        $recentThreads = $em->getRepository(Thread::class)->findBy([], ['dateCreation' => 'DESC'], 5);

        return $this->render('admin/dashboard.html.twig', [
            'threadCount' => $threadCount,
            'commentCount' => $commentCount,
            'userCount' => $userCount,
            'recentThreads' => $recentThreads,
        ]);
    }

    #[Route('/threads', name: 'admin_threads')]
    public function listThreads(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $em->getRepository(Thread::class)->createQueryBuilder('t')->orderBy('t.dateCreation', 'DESC');
        $threads = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 10);
        return $this->render('admin/threads.html.twig', ['threads' => $threads]);
    }

    #[Route('/thread/{id}/edit', name: 'admin_thread_edit', methods: ['GET', 'POST'])]
    public function editThread(Request $request, Thread $thread, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            if ($this->isCsrfTokenValid('edit' . $thread->getIdThread(), $request->request->get('_token'))) {
                $thread->setTitre($request->request->get('titre'));
                $thread->setContenu($request->request->get('contenu'));
                $em->flush();
                $this->addFlash('success', 'Thread mis à jour avec succès.');
                return $this->redirectToRoute('admin_threads');
            }
        }
        return $this->render('admin/thread_edit.html.twig', ['thread' => $thread]);
    }

    #[Route('/thread/{id}/delete', name: 'admin_thread_delete', methods: ['POST'])]
    public function deleteThread(Request $request, Thread $thread, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $thread->getIdThread(), $request->request->get('_token'))) {
            // Supprimer les dépendances
            $em->createQueryBuilder()
                ->delete('App\Entity\Jaime', 'j')
                ->where('j.thread = :id')
                ->setParameter('id', $thread)
                ->getQuery()
                ->execute();
            $em->createQueryBuilder()
                ->delete('App\Entity\Vote', 'v')
                ->where('v.thread = :id')
                ->setParameter('id', $thread)
                ->getQuery()
                ->execute();
            $em->createQueryBuilder()
                ->delete('App\Entity\Commentaire', 'c')
                ->where('c.thread = :id')
                ->setParameter('id', $thread)
                ->getQuery()
                ->execute();

            $em->remove($thread);
            $em->flush();
            $this->addFlash('success', 'Thread supprimé.');
        }
        return $this->redirectToRoute('admin_threads');
    }

    #[Route('/thread/{id}/translate', name: 'admin_thread_translate', methods: ['POST'])]
    public function translateThread(Request $request, Thread $thread, EntityManagerInterface $em, AiTranslationService $translator): Response
    {
        if ($this->isCsrfTokenValid('translate' . $thread->getIdThread(), $request->request->get('_token'))) {
            if ($thread->getContenu()) {
                $translatedContent = $translator->translateFrToEn($thread->getContenu());
                $thread->setContenu("[[Traduit par l'IA]]\n" . $translatedContent);
            }
            if ($thread->getTitre()) {
                $translatedTitle = $translator->translateFrToEn($thread->getTitre());
                $thread->setTitre("[EN] " . $translatedTitle);
            }
            $em->flush();
            $this->addFlash('success', 'Thread traduit avec succès vers l\'Anglais par le modèle d\'IA !');
        }
        return $this->redirectToRoute('admin_threads');
    }

    #[Route('/commentaires', name: 'admin_commentaires')]
    public function listCommentaires(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $em->getRepository(Commentaire::class)->createQueryBuilder('c')->orderBy('c.dateCreation', 'DESC');
        $commentaires = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 15);
        return $this->render('admin/commentaires.html.twig', ['commentaires' => $commentaires]);
    }

    #[Route('/commentaire/{id}/edit', name: 'admin_commentaire_edit', methods: ['GET', 'POST'])]
    public function editCommentaire(Request $request, Commentaire $commentaire, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            if ($this->isCsrfTokenValid('edit' . $commentaire->getIdCommentaire(), $request->request->get('_token'))) {
                $commentaire->setContenu($request->request->get('contenu'));
                $em->flush();
                $this->addFlash('success', 'Commentaire mis à jour avec succès.');
                return $this->redirectToRoute('admin_commentaires');
            }
        }
        return $this->render('admin/commentaire_edit.html.twig', ['commentaire' => $commentaire]);
    }

    #[Route('/commentaire/{id}/translate', name: 'admin_commentaire_translate', methods: ['POST'])]
    public function translateCommentaire(Request $request, Commentaire $commentaire, EntityManagerInterface $em, AiTranslationService $translator): Response
    {
        if ($this->isCsrfTokenValid('translate' . $commentaire->getIdCommentaire(), $request->request->get('_token'))) {
            if ($commentaire->getContenu()) {
                $translatedContent = $translator->translateFrToEn($commentaire->getContenu());
                $commentaire->setContenu("[[Traduit par l'IA]]\n" . $translatedContent);
                $em->flush();
                $this->addFlash('success', 'Commentaire traduit avec succès vers l\'Anglais par le modèle d\'IA !');
            }
        }
        return $this->redirectToRoute('admin_commentaires');
    }

    #[Route('/commentaire/{id}/delete', name: 'admin_commentaire_delete', methods: ['POST'])]
    public function deleteCommentaire(Request $request, Commentaire $commentaire, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $commentaire->getIdCommentaire(), $request->request->get('_token'))) {
            $em->remove($commentaire);
            $em->flush();
            $this->addFlash('success', 'Commentaire supprimé.');
        }
        return $this->redirectToRoute('admin_commentaires');
    }

    #[Route('/utilisateurs', name: 'admin_users')]
    public function listUsers(Request $request, EntityManagerInterface $em, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $em->getRepository(User::class)->createQueryBuilder('u')->orderBy('u.idUser', 'DESC');
        $pagination = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 20);
        
        // Calcul du score toxique pour chaque utilisateur de la page actuelle
        foreach ($pagination->getItems() as $user) {
            $badThreadsCount = $em->getRepository(Thread::class)->count(['auteur' => $user, 'sentiment' => 'negatif']);
            $badCommentsCount = $em->getRepository(Commentaire::class)->count(['auteur' => $user, 'sentiment' => 'negatif']);
            
            // Propriété virtuelle pour la vue
            $user->toxicScore = $badThreadsCount + $badCommentsCount;
        }

        return $this->render('admin/users.html.twig', ['users' => $pagination]);
    }

    #[Route('/utilisateur/{id}/ban', name: 'admin_user_ban', methods: ['POST'])]
    public function banUser(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('ban' . $user->getIdUser(), $request->request->get('_token'))) {
            if ($user->getRole() === 'ADMINISTRATEUR') {
                $this->addFlash('error', 'Impossible de bannir un administrateur.');
            } else {
                $user->setActif(false);
                $em->flush();
                $this->addFlash('success', 'Utilisateur ' . $user->getEmail() . ' a été banni.');
            }
        }
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/utilisateur/{id}/unban', name: 'admin_user_unban', methods: ['POST'])]
    public function unbanUser(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('unban' . $user->getIdUser(), $request->request->get('_token'))) {
            $user->setActif(true);
            $em->flush();
            $this->addFlash('success', 'Accès restauré pour ' . $user->getEmail() . '.');
        }
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/utilisateur/{id}/promote', name: 'admin_user_promote', methods: ['POST'])]
    public function promoteUser(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('promote' . $user->getIdUser(), $request->request->get('_token'))) {
            $user->setRole('ADMINISTRATEUR');
            $em->flush();
            $this->addFlash('success', 'Utilisateur promu administrateur.');
        }
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/utilisateur/{id}/demote', name: 'admin_user_demote', methods: ['POST'])]
    public function demoteUser(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('demote' . $user->getIdUser(), $request->request->get('_token'))) {
            $user->setRole('AGRICULTEUR');
            $em->flush();
            $this->addFlash('success', 'Administrateur rétrogradé.');
        }
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/utilisateur/{id}/notify', name: 'admin_user_notify', methods: ['POST'])]
    public function notifyUser(Request $request, User $user, MailService $mailService): Response
    {
        if ($this->isCsrfTokenValid('notify' . $user->getIdUser(), $request->request->get('_token'))) {
            $message = $request->request->get('message', 'Bonjour, vous avez une nouvelle mise à jour sur votre compte FlahaSmart.');
            
            try {
                $mailService->sendNotificationEmail($user->getEmail(), $user->getNom() ?? 'Client', $message);
                $this->addFlash('success', 'Email de notification envoyé avec succès à ' . $user->getEmail());
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
            }
        }
        return $this->redirectToRoute('admin_users');
    }
}