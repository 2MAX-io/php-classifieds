<?php

use App\Kernel;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;
use Twig\TwigFilter;
use Twig\TwigFunction;

require __DIR__ . '/zz_engine/config/bootstrap.php';

define('PUBLIC_DIR_LOCATION', __DIR__);
define('SYMFONY_LOCATION', __DIR__ . '/zz_engine');

$loader = new \Twig\Loader\FilesystemLoader(SYMFONY_LOCATION . '/templates');
$twig = new \Twig\Environment($loader, [
//    'cache' => SYMFONY_LOCATION . '/var/cache/perf_app/twig',
]);

$twig->addFilter(new TwigFilter('trans', 'fooReturnSame'));
$twig->addFunction(new TwigFunction('path', 'fooReturnSame'));
$twig->addFunction(new TwigFunction('asset', 'fooReturnSame'));
$twig->addFunction(new TwigFunction('lowSecurityCheckIsAdminInPublic', function() {
    return false;
}));

echo $twig->render('index.html.twig', ['name' => 'zz']);


function fooReturnSame($val) {
    return $val;
}
