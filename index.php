<?php
include 'lib/bones.php';

get('/', function($app) {
    $app->render('home');
});

get('/hello/:name', function($app) {
    $app->set('name', $app->request("name"));
    $app->render('hello');
});

post('/hello', function($app) {
    $app->set('name', $app->form('name'));
    $app->render('hello');
});