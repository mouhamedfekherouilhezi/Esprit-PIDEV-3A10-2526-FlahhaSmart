<?php

namespace App\Controller;

use App\Entity\Thread;
use App\Entity\Commentaire;
use App\Service\AiTranslationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AiTranslationController extends AbstractController
{
    #[Route('/ai/translate/thread/{id}', name: 'ai_translate_thread', methods: ['POST'])]
    public function translateThread(Thread $thread, AiTranslationService $translator): JsonResponse
    {
        $content = $thread->getContenu();
        $translated = $translator->translateFrToEn($content);
        
        return $this->json([
            'original' => $content,
            'translated' => $translated,
        ]);
    }

    #[Route('/ai/translate/comment/{id}', name: 'ai_translate_comment', methods: ['POST'])]
    public function translateComment(Commentaire $comment, AiTranslationService $translator): JsonResponse
    {
        $content = $comment->getContenu();
        $translated = $translator->translateFrToEn($content);
        
        return $this->json([
            'original' => $content,
            'translated' => $translated,
        ]);
    }
}
