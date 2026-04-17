<?php

namespace App\Service;

class ModerationService
{
    public function __construct(private ApiNinjasClient $apiNinjas) {}

    public function moderate(string $text): array
    {
        $profanity = $this->apiNinjas->detectProfanity($text);
        $isInappropriate = $profanity['has_profanity'] ?? false;
        $reason = $isInappropriate ? ($profanity['censored'] ?? 'Contenu inapproprié') : '';
        $sentiment = $this->apiNinjas->analyzeSentiment($text);

        return [
            'allowed' => !$isInappropriate,
            'reason' => $reason,
            'sentiment' => $sentiment,
        ];
    }

    public function checkSimilarity(string $title, array $existingTitles): array
    {
        $maxScore = 0;
        $similarTitle = null;
        foreach ($existingTitles as $existingTitle) {
            $score = $this->apiNinjas->checkSimilarity($title, $existingTitle);
            if ($score > $maxScore) {
                $maxScore = $score;
                $similarTitle = $existingTitle;
            }
        }
        return ['similar' => $maxScore >= 0.7, 'score' => round($maxScore * 100), 'title' => $similarTitle];
    }
}