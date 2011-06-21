<?php
include 'lib/shrimp.php';

get('/', function($app) {
    $app->render('home');
});

get('/user/:username', function($app) {
    $app->set('username', $app->request("username"));
    $app->render('user/show');
});

get('/user/:username/posts', function($app) {
    $app->set('username', $app->request("username"));
    $app->render('user/posts');
});


get('/redirect', function($app) {
    $app->redirect('user/tim');
});