<?php
require 'vendor/autoload.php';
use App\Kernel;
use Symfony\Component\HttpFoundation\Request;

$kernel = new Kernel('dev', true);
$kernel->boot();
$req = Request::create('/admin/commentaires');
try {
    $res = $kernel->handle($req);
    echo $res->getContent();
} catch (\Throwable $e) {
    echo $e->getMessage() . "\n" . $e->getTraceAsString();
}
