<?php
// Script de nettoyage de la base de données pour FlahaSmart

require dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Doctrine\DBAL\DriverManager;

$dotenv = new Dotenv();
$dotenv->load(dirname(__DIR__) . '/.env');

$connectionParams = [
    'url' => $_ENV['DATABASE_URL'],
];

try {
    $conn = DriverManager::getConnection($connectionParams);
    
    echo "Nettoyage des erreurs de traduction dans les threads...\n";
    
    // Remplacement des chaines d'erreur spécifiques par du vide ou le texte original
    $sqlContent = "UPDATE threads SET contenu = REPLACE(contenu, '[[Traduit par l\'IA]] [Erreur de traduction IA: HTTP/2 404 returned for \"https://api-inference.huggingface.co/models/Helsinki-NLP/opus-mt-fr-en\".]', '')";
    $sqlTitle = "UPDATE threads SET titre = REPLACE(titre, '[EN] [Erreur de traduction IA: HTTP/2 404 returned for \"https://api-inference.huggingface.co/models/Helsinki-NLP/opus-mt-fr-en\".]', '')";
    
    $conn->executeStatement($sqlContent);
    $conn->executeStatement($sqlTitle);
    
    echo "Nettoyage terminé avec succès.\n";
    
} catch (\Exception $e) {
    echo "Erreur lors du nettoyage : " . $e->getMessage() . "\n";
}
