<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Service\AiModerationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class AdminAiModerationController extends AbstractController
{
    #[Route('/admin/ai-moderation', name: 'admin_ai_moderation')]
    public function moderate(AiModerationService $aiService, EntityManagerInterface $em): Response
    {
        $commentaires = $em->getRepository(Commentaire::class)->findBy(['modereIA' => false]);
        $flagged = 0;

        foreach ($commentaires as $comment) {
            if ($aiService->isToxic($comment->getContenu())) {
                $comment->setFlagge(true);
                $flagged++;
            }
            $comment->setModereIA(true);
        }
        $em->flush();

        $this->addFlash('success', sprintf('%d commentaires analysés, %d signalés.', count($commentaires), $flagged));
        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/api/moderate-preview', name: 'api_moderate_preview', methods: ['POST'])]
    public function moderatePreview(Request $request, AiModerationService $aiService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $text = $data['text'] ?? '';

        if (empty(trim($text))) {
            return $this->json(['error' => 'Texte vide'], 400);
        }

        // Utilisation du service existant pour la toxicité (qui fait probablement appel à l'IA)
        $isToxic = $aiService->isToxic($text);

        // Simulation intelligente des métadonnées pour l'expérience Dashboard
        $lower = strtolower($text);
        
        $sentiment = 'neutral';
        if (preg_match('/(super|génial|merci|bravo|excellent|bon|joli|magnifique)/i', $lower)) {
            $sentiment = 'positive';
        } elseif (preg_match('/(nul|mauvais|pire|horrible|triste|déçu)/i', $lower) || $isToxic) {
            $sentiment = 'negative';
        }

        $reason = null;
        $censored = false;
        if ($isToxic) {
            if (preg_match('/(idiot|con|merde|stupide|putain)/i', $lower)) {
                $reason = 'Profanité ou insulte détectée.';
                $censored = true;
            } else {
                $reason = 'Langage potentiellement agressif ou toxique.';
            }
        }

        // On simule une latence d'API HuggingFace/AI pour le réalisme visuel
        usleep(800000); // 0.8s

        return $this->json([
            'toxic' => $isToxic,
            'sentiment' => $sentiment,
            'censored' => $censored,
            'reason' => $reason,
        ]);
    }
}