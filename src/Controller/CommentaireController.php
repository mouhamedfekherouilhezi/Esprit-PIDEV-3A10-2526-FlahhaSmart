<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Thread;
use App\Entity\User;
use App\Service\ModerationService;
use App\Service\NotificationService;
use App\Service\ReputationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/commentaire')]
class CommentaireController extends AbstractController
{
    #[Route('/new/{threadId}', name: 'commentaire_new', methods: ['POST'])]
    public function new(int $threadId, Request $request, EntityManagerInterface $em, ModerationService $moderation, ReputationService $rep, NotificationService $notif, ValidatorInterface $validator): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour commenter.');
            return $this->redirectToRoute('app_login');
        }

        $thread = $em->getRepository(Thread::class)->find($threadId);
        if (!$thread) {
            throw $this->createNotFoundException('Thread non trouvé');
        }

        $contenu = $request->request->get('contenu');

        // Validation PHP uniquement
        $commentaire = new Commentaire();
        $commentaire->setContenu($contenu);
        $errors = $validator->validate($commentaire);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->addFlash('error', $error->getMessage());
            }
            return $this->redirectToRoute('thread_show', ['id' => $threadId]);
        }

        $modResult = $moderation->moderate($contenu);
        if (!$modResult['allowed']) {
            $this->addFlash('error', 'Commentaire refusé : ' . $modResult['reason']);
            return $this->redirectToRoute('thread_show', ['id' => $threadId]);
        }

        $commentaire->setIdThread($threadId);
        $commentaire->setIdUser($user->getIdUser());
        $commentaire->setDateCreation(new \DateTime());
        $commentaire->setStatut('actif');
        $commentaire->setSentiment($modResult['sentiment']);

        $em->persist($commentaire);
        $em->flush();

        if ($thread->getIdUser() !== $user->getIdUser()) {
            $rep->addPoints($thread->getIdUser(), ReputationService::POINTS_COMMENTAIRE);
            $notif->create($thread->getIdUser(), "💬 User {$user->getIdUser()} a commenté votre thread « {$thread->getTitre()} »", 'commentaire');
        }

        $this->addFlash('success', 'Commentaire ajouté avec succès.');
        return $this->redirectToRoute('thread_show', ['id' => $threadId]);
    }

    #[Route('/edit/{id}', name: 'commentaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commentaire $commentaire, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $user = $this->getUser();
        if (!$user || $commentaire->getIdUser() !== $user->getIdUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier ce commentaire.');
            return $this->redirectToRoute('thread_show', ['id' => $commentaire->getIdThread()]);
        }

        if ($request->isMethod('POST')) {
            $nouveauContenu = $request->request->get('contenu');
            $commentaire->setContenu($nouveauContenu);
            $errors = $validator->validate($commentaire);
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
                return $this->redirectToRoute('commentaire_edit', ['id' => $commentaire->getIdCommentaire()]);
            }
            $em->flush();
            $this->addFlash('success', 'Commentaire modifié.');
            return $this->redirectToRoute('thread_show', ['id' => $commentaire->getIdThread()]);
        }

        return $this->render('commentaire/edit.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

    #[Route('/delete/{id}', name: 'commentaire_delete', methods: ['POST'])]
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user || $commentaire->getIdUser() !== $user->getIdUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer ce commentaire.');
            return $this->redirectToRoute('thread_show', ['id' => $commentaire->getIdThread()]);
        }

        if ($this->isCsrfTokenValid('delete' . $commentaire->getIdCommentaire(), $request->request->get('_token'))) {
            $threadId = $commentaire->getIdThread();
            $em->remove($commentaire);
            $em->flush();
            $this->addFlash('success', 'Commentaire supprimé.');
            return $this->redirectToRoute('thread_show', ['id' => $threadId]);
        }

        $this->addFlash('error', 'Token invalide.');
        return $this->redirectToRoute('thread_show', ['id' => $commentaire->getIdThread()]);
    }
}