<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiNinjasClient
{
    private const API_KEY = 'cmeKBOabvJHdMH4RigJBmSG6nGFvLR9X8pzwS8vL';
    private const BASE_URL = 'https://api.api-ninjas.com/v1/';

    public function __construct(private HttpClientInterface $httpClient) {}

    public function detectProfanity(string $text): array
    {
        $response = $this->httpClient->request('GET', self::BASE_URL . 'profanityfilter', [
            'query' => ['text' => $text],
            'headers' => ['X-Api-Key' => self::API_KEY],
            'verify_peer' => false,
            'verify_host' => false,
        ]);
        return $response->toArray();
    }

    public function analyzeSentiment(string $text): string
    {
        $response = $this->httpClient->request('GET', self::BASE_URL . 'sentiment', [
            'query' => ['text' => $text],
            'headers' => ['X-Api-Key' => self::API_KEY],
            'verify_peer' => false,
            'verify_host' => false,
        ]);
        $data = $response->toArray();
        return strtolower($data['sentiment'] ?? 'neutre');
    }

    public function checkSimilarity(string $text1, string $text2): float
    {
        $response = $this->httpClient->request('POST', self::BASE_URL . 'textsimilarity', [
            'json' => ['text_1' => $text1, 'text_2' => $text2],
            'headers' => ['X-Api-Key' => self::API_KEY],
            'verify_peer' => false,
            'verify_host' => false,
        ]);
        $data = $response->toArray();
        return $data['similarity'] ?? 0.0;
    }
}