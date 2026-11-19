<?php

declare(strict_types=1);

namespace App\Controller;

final class CampaignController extends AppController
{
    public function index(): void
    {
        $this->render('campaigns', [
            'pageTitle' => 'Campaigns',
        ]);
    }

    public function show(int $id): void
    {
        $this->render('game_room', [
            'pageTitle' => 'Pokój rozgrywki',
            'campaignId' => $id,
        ]);
    }

    public function join(): void
    {
        $this->render('join', [
            'pageTitle' => 'Dołączanie do rozgrywki',
        ]);
    }

    public function joinPost(): void
    {
        $roomCode = trim((string) ($_POST['room_code'] ?? ''));
        $username = trim((string) ($_POST['username'] ?? ''));
        $errors = [];

        if ($roomCode === '') {
            $errors[] = 'Please enter a room code.';
        }

        if ($username === '') {
            $errors[] = 'Please enter a username.';
        }

        if ($errors !== []) {
            $this->render('join', [
                'pageTitle' => 'Dołączanie do rozgrywki',
                'errors' => $errors,
                'old' => [
                    'room_code' => $roomCode,
                    'username' => $username,
                ],
            ]);
            return;
        }

        $this->redirect('/campaigns/1');
    }
}