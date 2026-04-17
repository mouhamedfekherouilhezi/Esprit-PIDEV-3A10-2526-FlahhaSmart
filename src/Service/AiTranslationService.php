<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class AiTranslationService
{
    private $httpClient;
    private $apiKey;

    public function __construct(HttpClientInterface $httpClient, string $hfApiKey)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $hfApiKey;
    }

    /**
     * Traduit un texte depuis le Français vers l'Anglais.
     * Utilise un modèle robuste (T5 ou NLLB) avec fallback transparent.
     */
    public function translateFrToEn(string $text): string
    {
        if (empty(trim($text))) {
            return $text;
        }

        try {
            // Utilisation d'un modèle très stable pour la traduction Fr-En
            // facebook/nllb-200-distilled-600M est excellent mais T5 est très rapide pour le texte court
            $response = $this->httpClient->request('POST', 'https://api-inference.huggingface.co/models/google-t5/t5-small', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ],
                'json' => [
                    'inputs' => "translate French to English: " . $text,
                    'options' => [
                        'wait_for_model' => true,
                        'use_cache' => true
                    ]
                ],
                'timeout' => 10,
            ]);

            // Vérification du code de statut avant de tenter de lire le JSON
            if ($response->getStatusCode() === 200) {
                $data = $response->toArray();
                if (isset($data[0]['translation_text'])) {
                    return $data[0]['translation_text'];
                }
            }
        } catch (\Throwable $e) {
            // Log discret de l'erreur (devrait être fait avec un LoggerInterface en prod)
            // Error: $e->getMessage();
        }

        // Si l'IA échoue, on tente le fallback Google Translate (discret)
        return $this->fallbackTranslation($text);
    }

    /**
     * Fallback utilisant une API de traduction publique en cas de panne de l'IA principale
     */
    private function fallbackTranslation(string $text): string
    {
        try {
            // URL stable pour le fallback sans clé API
            $url = 'https://translate.googleapis.com/translate_a/single?client=gtx&sl=fr&tl=en&dt=t&q=' . urlencode($text);
            $response = $this->httpClient->request('GET', $url, [
                'timeout' => 5,
            ]);
            
            if ($response->getStatusCode() === 200) {
                $data = $response->toArray();
                if (isset($data[0][0][0])) {
                    return $data[0][0][0];
                }
            }
        } catch (\Throwable $e) {
            // Échec total : on retourne le texte original plutôt qu'un message d'erreur moche
        }

        return $text;
    }
}
