<?php
// src/Service/YourAIService.php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class YourAIService
{
    private $httpClient;
    private $logger;
    
    public function __construct(HttpClientInterface $httpClient, LoggerInterface $logger)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    public function generateDescription(string $title): string
    {
        $apiKey = $_ENV['OPENAI_API_KEY'] ?? null;

        if (!$apiKey) {
            $this->logger->error('OpenAI API key is missing in environment variables');
            throw new \RuntimeException('API key not configured');
        }

        try {
            $response = $this->httpClient->request('POST', 'https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system', 
                            'content' => 'Tu es un assistant spécialisé dans la rédaction de descriptions touristiques en français. Sois concis et attractif.'
                        ],
                        [
                            'role' => 'user',
                            'content' => "Rédige une description attrayante pour une excursion intitulée : '$title'"
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 150
                ],
                'timeout' => 15 // Augmentez le timeout si nécessaire
            ]);

            // Vérification du statut HTTP
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                $this->logger->error("OpenAI API returned status code: $statusCode");
                throw new \RuntimeException("API returned status code: $statusCode");
            }

            $data = $response->toArray();
            
            if (!isset($data['choices'][0]['message']['content'])) {
                $this->logger->error('Unexpected API response format', ['response' => $data]);
                throw new \RuntimeException('Unexpected API response format');
            }

            return $data['choices'][0]['message']['content'];

        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Network error: ' . $e->getMessage());
            throw new \RuntimeException('Network error occurred');
        } catch (\Exception $e) {
            $this->logger->error('OpenAI API error: ' . $e->getMessage());
            throw new \RuntimeException('Failed to generate description');
        }
    }
}