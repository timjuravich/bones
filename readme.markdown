## Background
Bones is based on a concept written by Dan Horrigan called Sammy. Bones was then adapted to create a simple PHP and CouchDB application to be covered in the upcoming book published by Packt Publishing

## Bones
Bones is a quick attempt at a PHP sinatra-ish environment.

## Warning
I've only played with this a bit, working only from localhost, it's possible that other configurations will break the logic. I'll test it more soon!

## Example
Bones is a simple lib that you can add to a php file that will allow the following

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