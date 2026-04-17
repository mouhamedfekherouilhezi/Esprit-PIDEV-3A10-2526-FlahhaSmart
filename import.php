<?php
$file = __DIR__ . '/flahasmart (2).sql';
$pdo = new PDO('mysql:host=127.0.0.1;dbname=flahasmart;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = file_get_contents($file);
if ($sql === false) {
    die("Impossible de lire le fichier SQL.");
}

// Exécuter le script ligne par ligne (optionnel mais plus sûr)
$pdo->exec($sql);
echo "Import terminé avec succès.";