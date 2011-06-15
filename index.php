<?php
include 'lib/shrimp.php';

get('/', function($app) {
    $app->render('home');
});

get('/user/(.*?)', function($app) {
    $app->set('username', $app->segment(2));
    $app->render('user/show');
});

get('/redirect', function($app) {
    $app->redirect('user/tim');
});

$shrimp->run();