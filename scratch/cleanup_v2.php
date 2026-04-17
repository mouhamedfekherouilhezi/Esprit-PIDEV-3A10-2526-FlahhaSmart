<?php
// Script de nettoyage FINAL et ROBUSTE de la base de données FlahaSmart

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(dirname(__DIR__) . '/.env');

$kernel = new Kernel($_ENV['APP_ENV'] ?? 'dev', (bool) ($_ENV['APP_DEBUG'] ?? true));
$kernel->boot();
$container = $kernel->getContainer();

if ($container->has('doctrine')) {
    $em = $container->get('doctrine')->getManager();
    $conn = $em->getConnection();
    
    echo "Démarrage du nettoyage des discussions...\n";
    
    // On récupère tous les threads qui contiennent des traces d'erreurs
    $threads = $conn->fetchAllAssociative("SELECT id_thread, titre, contenu FROM threads WHERE contenu LIKE '%Erreur de traduction%' OR titre LIKE '%Erreur de traduction%'");
    
    $count = 0;
    foreach ($threads as $t) {
        $id = $t['id_thread'];
        $titre = $t['titre'];
        $contenu = $t['contenu'];
        
        // Nettoyage du titre : on retire tout le bloc d'erreur au début
        $titre = preg_replace('/\[EN\]\s*\[Erreur de traduction IA:.*?\]\s*/i', '', $titre);
        
        // Nettoyage du contenu : on retire le bloc de traduction et l'erreur
        $contenu = preg_replace('/\[\[Traduit par l\'IA\]\].*?\[Erreur de traduction IA:.*?\]\s*/is', '', $contenu);
        
        $conn->executeStatement("UPDATE threads SET titre = ?, contenu = ? WHERE id_thread = ?", [$titre, $contenu, $id]);
        $count++;
    }
    
    echo "Nettoyage terminé. $count threads ont été réparés.\n";
} else {
    echo "Erreur: Doctrine non trouvé.\n";
}
