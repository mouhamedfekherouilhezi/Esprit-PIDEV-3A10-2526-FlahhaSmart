<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AiModerationService
{
    private $httpClient;
    private $apiKey;

    public function __construct(HttpClientInterface $httpClient, string $hfApiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $hfApiKey;
    }

    public function isToxic(string $text): bool
    {
        try {
            $response = $this->httpClient->request('POST', 'https://api-inference.huggingface.co/models/cardiffnlp/twitter-roberta-base-offensive', [
                'headers' => ['Authorization' => 'Bearer ' . $this->apiKey],
                'json' => ['inputs' => $text],
                'timeout' => 5, // Avoid long hangs
            ]);

            $data = $response->toArray();
            
            if (isset($data[0]) && is_array($data[0])) {
                foreach ($data[0] as $prediction) {
                    if (($prediction['label'] === 'offensive' || $prediction['label'] === 'toxic') && $prediction['score'] > 0.6) {
                        return true;
                    }
                }
            }
        } catch (\Throwable $e) {
            // Fallback en cas d'erreur de l'API (404, 503, ou réseau)
            // On fait une vérification locale basée sur des mots toxiques évidents (vulgarité, haine)
            return $this->isToxicLocalFallback($text);
        }

        return false;
    }

    private function isToxicLocalFallback(string $text): bool
    {
        $toxicWords = ['idiot', 'con', 'merde', 'stupide', 'putain', 'salope', 'connard', 'haine', 'tuer', 'mort'];
        $lowerText = strtolower($text);

        foreach ($toxicWords as $word) {
            if (strpos($lowerText, $word) !== false) {
                return true;
            }
        }

        return false;
    }
}