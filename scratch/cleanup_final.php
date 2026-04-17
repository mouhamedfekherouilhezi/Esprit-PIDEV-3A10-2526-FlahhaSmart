<?php
// Script de nettoyage de la base de données via Symfony Kernel

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(dirname(__DIR__) . '/.env');

$kernel = new Kernel($_ENV['APP_ENV'], (bool) $_ENV['APP_DEBUG']);
$kernel->boot();
$container = $kernel->getContainer();
if ($container->has('doctrine')) {
    $em = $container->get('doctrine')->getManager();
    $conn = $em->getConnection();
    
    echo "Nettoyage des erreurs de traduction...\n";
    
    $sql = "UPDATE threads SET contenu = REPLACE(contenu, '[[Traduit par l\'IA]] [Erreur de traduction IA: HTTP/2 404 returned for \"https://api-inference.huggingface.co/models/Helsinki-NLP/opus-mt-fr-en\".]', ''), titre = REPLACE(titre, '[EN] [Erreur de traduction IA: HTTP/2 404 returned for \"https://api-inference.huggingface.co/models/Helsinki-NLP/opus-mt-fr-en\".]', '')";
    
    $result = $conn->executeStatement($sql);
    
    echo "Lignes affectées : $result\n";
    echo "Nettoyage terminé.\n";
} else {
    echo "Doctrine non trouvé.\n";
}
