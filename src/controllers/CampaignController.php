<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CampaignRepository;

final class CampaignController extends AppController
{
    public function index(): void
    {
        $this->forbidGuest();

        $campaignRepository = new CampaignRepository();

        $this->render('campaigns', [
            'pageTitle' => 'Campaigns',
            'campaigns' => $campaignRepository->findAllWithDm(),
        ]);
    }

    public function show(int $id): void
    {
        if ($this->getUserRole() === 'guest' && $id !== $this->getGuestCampaignId()) {
            $this->redirect('/campaigns/' . $this->getGuestCampaignId());
        }

        $campaignRepository = new CampaignRepository();
        $campaign = $campaignRepository->findByIdWithDm($id);

        if ($campaign === null) {
            http_response_code(404);
            $this->render('not-found', [
                'pageTitle' => 'Nie znaleziono kampanii',
            ]);
            return;
        }

        $this->render('game_room', [
            'pageTitle' => 'Pokój rozgrywki',
            'campaignId' => $id,
            'campaign' => $campaign,
        ]);
    }

    public function join(): void
    {
        $this->redirectIfAuthenticated();

        $this->render('join', [
            'pageTitle' => 'Dołączanie do rozgrywki',
        ]);
    }

    public function joinPost(): void
    {
        $this->redirectIfAuthenticated();

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

        $campaignId = 1;

        $this->ensureSession();
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'name' => $username,
            'role' => 'guest',
            'room_code' => $roomCode,
            'campaign_id' => $campaignId,
        ];

        $this->redirect('/campaigns/' . $campaignId);
    }
}