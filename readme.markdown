## Shrimp
Shrimp is a quick attempt at a PHP sinatra-ish environment.

## Warning
I've only played with this a bit, working only from localhost, it's possible that other configurations will break the logic. I'll test it more soon!

## Example
Shrimp is a simple lib that you can add to a php file that will allow the following

	<?php
	include 'lib/shrimp.php';

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