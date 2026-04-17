<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Jaime;
use App\Entity\Thread;
use App\Entity\User;
use App\Entity\Vote;
use App\Form\ThreadType;
use App\Service\ModerationService;
use App\Service\NotificationService;
use App\Service\ReputationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/thread')]
class ThreadController extends AbstractController
{
    #[Route('/', name: 'thread_index')]
    public function index(Request $request, EntityManagerInterface $em, ReputationService $rep): Response
    {
        $search = $request->query->get('search', '');
        $sort = $request->query->get('sort', 'date');

        // Requête de base : toujours sélectionner le thread et son score calculé
        $qb = $em->getRepository(Thread::class)->createQueryBuilder('t');
        $qb->select('t, (SELECT SUM(CASE WHEN v.typeVote = \'up\' THEN 1 ELSE -1 END) 
                     FROM App\Entity\Vote v WHERE v.thread = t) as score');

        if (!empty($search)) {
            $qb->andWhere('t.titre LIKE :search OR t.contenu LIKE :search OR t.tags LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        // Application du tri
        if ($sort === 'score') {
            $qb->orderBy('score', 'DESC');
        } else {
            $qb->orderBy('t.dateCreation', 'DESC');
        }

        $results = $qb->getQuery()->getResult();

        $user = $this->getUser();
        
        // Optimisation : Récupérer tous les votes et likes de l'utilisateur en une fois
        $userVotes = [];
        $userLikes = [];
        if ($user) {
            $votes = $em->getRepository(Vote::class)->findBy(['user' => $user]);
            foreach ($votes as $v) $userVotes[$v->getThread()->getIdThread()] = $v->getTypeVote();
            
            $likes = $em->getRepository(Jaime::class)->findBy(['user' => $user]);
            foreach ($likes as $l) $userLikes[$l->getThread()->getIdThread()] = true;
        }

        // Hydratation des threads
        $threads = [];
        foreach ($results as $result) {
            $thread = $result[0];
            $thread->score = (int) ($result['score'] ?? 0);
            $thread->likesCount = $em->getRepository(Jaime::class)->count(['thread' => $thread]);
            $thread->userVote = $userVotes[$thread->getIdThread()] ?? null;
            $thread->liked = $userLikes[$thread->getIdThread()] ?? false;
            $threads[] = $thread;
        }

        $reputationPoints = 0;
        $reputationBadge = '🌱 Débutant';

        if ($user) {
            $reputation = $rep->getReputation($user->getIdUser());
            $reputationPoints = $reputation->getPoints();
            $reputationBadge = $reputation->getBadge();

            $voteRepo = $em->getRepository(Vote::class);
            foreach ($threads as $thread) {
                // Vote de l'utilisateur courant
                $vote = $voteRepo->findOneBy([
                    'thread' => $thread,
                    'user' => $user
                ]);
                $thread->userVote = $vote ? $vote->getTypeVote() : null;

                // Like de l'utilisateur courant
                $liked = $em->getRepository(Jaime::class)->findOneBy([
                    'thread' => $thread,
                    'user' => $user
                ]);
                $thread->liked = (bool)$liked;
                $thread->likesCount = $em->getRepository(Jaime::class)->count(['thread' => $thread]);
            }
        }

        return $this->render('thread/index.html.twig', [
            'threads' => $threads,
            'search' => $search,
            'sort' => $sort,
            'reputationPoints' => $reputationPoints,
            'reputationBadge' => $reputationBadge,
        ]);
    }

    #[Route('/new', name: 'thread_new')]
    public function new(Request $request, EntityManagerInterface $em, ModerationService $moderation, ReputationService $rep, ValidatorInterface $validator): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour créer un thread.');
            return $this->redirectToRoute('app_login');
        }

        $thread = new Thread();
        $form = $this->createForm(ThreadType::class, $thread);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingTitles = $em->getRepository(Thread::class)->createQueryBuilder('t')
                ->select('t.titre')
                ->getQuery()
                ->getSingleColumnResult();
            $similarity = $moderation->checkSimilarity($thread->getTitre(), $existingTitles);
            if ($similarity['similar']) {
                $this->addFlash('warning', 'Un thread similaire existe déjà : ' . $similarity['title']);
                return $this->redirectToRoute('thread_new');
            }

            $modResult = $moderation->moderate($thread->getTitre() . ' ' . $thread->getContenu());
            if (!$modResult['allowed']) {
                $this->addFlash('error', 'Contenu inapproprié : ' . $modResult['reason']);
                return $this->redirectToRoute('thread_new');
            }

            $thread->setAuteur($user);
            $thread->setDateCreation(new \DateTime());
            $thread->setDateUpdate(new \DateTime());
            $thread->setStatut('actif');
            $thread->setSentiment($modResult['sentiment']);
            $em->persist($thread);
            $em->flush();

            $rep->addPoints($user->getIdUser(), ReputationService::POINTS_THREAD);

            $this->addFlash('success', 'Thread publié avec succès !');
            return $this->redirectToRoute('thread_index');
        }

        return $this->render('thread/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'thread_show')]
    public function show(Thread $thread, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        
        // Calcul du score global
        $score = $em->getRepository(Vote::class)->createQueryBuilder('v')
            ->select('SUM(CASE WHEN v.typeVote = \'up\' THEN 1 ELSE -1 END)')
            ->where('v.thread = :thread')
            ->setParameter('thread', $thread)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
            
        $thread->score = (int) $score;
        $thread->likesCount = $em->getRepository(Jaime::class)->count(['thread' => $thread]);
        
        if ($user) {
            $vote = $em->getRepository(Vote::class)->findOneBy(['thread' => $thread, 'user' => $user]);
            $thread->userVote = $vote ? $vote->getTypeVote() : null;
            
            $liked = $em->getRepository(Jaime::class)->findOneBy(['thread' => $thread, 'user' => $user]);
            $thread->liked = (bool)$liked;
        }

        $comments = $em->getRepository(Commentaire::class)->findBy(
            ['thread' => $thread],
            ['dateCreation' => 'ASC']
        );
        
        return $this->render('thread/show.html.twig', [
            'thread' => $thread,
            'comments' => $comments,
        ]);
    }

    #[Route('/{id}/edit', name: 'thread_edit')]
    public function edit(Request $request, Thread $thread, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $user = $this->getUser();
        if (!$user || $thread->getAuteur()?->getIdUser() !== $user->getIdUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier ce thread.');
            return $this->redirectToRoute('thread_show', ['id' => $thread->getIdThread()]);
        }

        $form = $this->createForm(ThreadType::class, $thread);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $thread->setDateUpdate(new \DateTime());
            $em->flush();
            $this->addFlash('success', 'Thread modifié avec succès.');
            return $this->redirectToRoute('thread_show', ['id' => $thread->getIdThread()]);
        }

        return $this->render('thread/edit.html.twig', [
            'form' => $form->createView(),
            'thread' => $thread,
        ]);
    }

    #[Route('/{id}/delete', name: 'thread_delete', methods: ['POST'])]
    public function delete(Request $request, Thread $thread, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user || $thread->getAuteur()?->getIdUser() !== $user->getIdUser()) {
            $this->addFlash('error', 'Action non autorisée.');
            return $this->redirectToRoute('thread_index');
        }

        if ($this->isCsrfTokenValid('delete' . $thread->getIdThread(), $request->request->get('_token'))) {
            $jaimes = $em->getRepository(Jaime::class)->findBy(['thread' => $thread]);
            foreach ($jaimes as $jaime) $em->remove($jaime);
            $votes = $em->getRepository(Vote::class)->findBy(['thread' => $thread]);
            foreach ($votes as $vote) $em->remove($vote);
            $comments = $em->getRepository(Commentaire::class)->findBy(['thread' => $thread]);
            foreach ($comments as $comment) $em->remove($comment);
            $em->flush();

            $em->remove($thread);
            $em->flush();
            $this->addFlash('success', 'Thread supprimé.');
        }
        return $this->redirectToRoute('thread_index');
    }

    #[Route('/{id}/vote/{type}', name: 'thread_vote', methods: ['POST'])]
    public function vote(Thread $thread, string $type, EntityManagerInterface $em, ReputationService $rep, NotificationService $notif): JsonResponse
    {
        try {
            $user = $this->getUser();
            if (!$user) {
                return $this->json(['error' => 'Vous devez être connecté pour voter'], 401);
            }

            if (!in_array($type, ['up', 'down'])) {
                return $this->json(['error' => 'Type de vote invalide'], 400);
            }

            $voteRepo = $em->getRepository(Vote::class);
            $existing = $voteRepo->findOneBy([
                'thread' => $thread,
                'user' => $user
            ]);

            $isOwnThread = ($thread->getAuteur()?->getIdUser() === $user->getIdUser());

            if ($existing && $existing->getTypeVote() === $type) {
                // Annulation du vote
                $em->remove($existing);
                if (!$isOwnThread && $type === 'up') {
                    $rep->deductPoints($thread->getAuteur()?->getIdUser(), ReputationService::POINTS_UPVOTE);
                }
            } else {
                if ($existing) {
                    // Changement de vote
                    $oldType = $existing->getTypeVote();
                    $existing->setTypeVote($type);
                    if (!$isOwnThread) {
                        if ($oldType === 'up') {
                            $rep->deductPoints($thread->getAuteur()?->getIdUser(), ReputationService::POINTS_UPVOTE);
                        }
                        if ($type === 'up') {
                            $rep->addPoints($thread->getAuteur()?->getIdUser(), ReputationService::POINTS_UPVOTE);
                        }
                    }
                } else {
                    // Nouveau vote
                    $vote = new Vote();
                    $vote->setThread($thread);
                    $vote->setUser($user);
                    $vote->setTypeVote($type);
                    $vote->setDateVote(new \DateTime());
                    $em->persist($vote);
                    
                    if ($thread->getAuteur() && !$isOwnThread) {
                        if ($type === 'up') {
                            $rep->addPoints($thread->getAuteur()->getIdUser(), ReputationService::POINTS_UPVOTE);
                        }
                        $notif->create(
                            $thread->getAuteur()->getIdUser(),
                            "👍 L'utilisateur {$user->getNom()} a voté pour votre thread « {$thread->getTitre()} »",
                            'vote'
                        );
                    }
                }
            }
            $em->flush();

            // Recalcul du score
            $score = $voteRepo->createQueryBuilder('v')
                ->select('SUM(CASE WHEN v.typeVote = \'up\' THEN 1 ELSE -1 END)')
                ->where('v.thread = :thread')
                ->setParameter('thread', $thread)
                ->getQuery()
                ->getSingleScalarResult() ?? 0;

            return $this->json(['score' => $score]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/{id}/like', name: 'thread_like', methods: ['POST'])]
    public function like(Thread $thread, EntityManagerInterface $em, ReputationService $rep, NotificationService $notif): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->json(['error' => 'Non authentifié'], 401);
        }

        $jaimeRepo = $em->getRepository(Jaime::class);
        $existing = $jaimeRepo->findOneBy([
            'thread' => $thread,
            'user' => $user
        ]);

        $isOwnThread = ($thread->getAuteur()?->getIdUser() === $user->getIdUser());

        if ($existing) {
            $em->remove($existing);
            $liked = false;
            if (!$isOwnThread) {
                $rep->deductPoints($thread->getAuteur()?->getIdUser(), ReputationService::POINTS_LIKE);
            }
        } else {
            $jaime = new Jaime();
            $jaime->setThread($thread);
            $jaime->setUser($user);
            $jaime->setDateJaime(new \DateTime());
            $em->persist($jaime);
            $liked = true;
            if ($thread->getAuteur() && !$isOwnThread) {
                $rep->addPoints($thread->getAuteur()->getIdUser(), ReputationService::POINTS_LIKE);
                $notif->create(
                    $thread->getAuteur()->getIdUser(),
                    "❤️ L'utilisateur {$user->getNom()} a aimé votre thread « {$thread->getTitre()} »",
                    'like'
                );
            }
        }
        $em->flush();

        $likesCount = $jaimeRepo->count(['thread' => $thread]);
        return $this->json(['liked' => $liked, 'likes' => $likesCount]);
    }
}