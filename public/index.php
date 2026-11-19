<?php

declare(strict_types=1);

use App\Controller\DefaultController;
use App\Controller\CampaignController;
use App\Controller\SecurityController;
use App\Controller\CharacterController;
use App\Routing;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$routing = new Routing();

$routing->get('/', DefaultController::class, 'index');

$routing->get('/login', SecurityController::class, 'login');
$routing->post('/login', SecurityController::class, 'loginPost');
$routing->post('/logout', SecurityController::class, 'logoutPost');
$routing->get('/register', SecurityController::class, 'register');
$routing->post('/register', SecurityController::class, 'registerPost');
$routing->get('/forgot-password', SecurityController::class, 'forgotPassword');
$routing->post('/forgot-password', SecurityController::class, 'forgotPasswordPost');
$routing->get('/reset-password', SecurityController::class, 'resetPassword');
$routing->post('/reset-password', SecurityController::class, 'resetPasswordPost');

$routing->get('/campaigns', CampaignController::class, 'index');
$routing->get('/campaigns/join', CampaignController::class, 'join');
$routing->get('/campaigns/{id}', CampaignController::class, 'show');

$routing->get('/campaigns/{id}/characters', CharacterController::class, 'index');

$routeFound = $routing->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
);

if (!$routeFound) {
    http_response_code(404);
    (new DefaultController())->notFound();
}