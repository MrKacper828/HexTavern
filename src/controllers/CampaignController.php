<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CampaignRepository;
use DateTimeImmutable;
use Throwable;

final class CampaignController extends AppController
{
    public function index(): void
    {
        $this->forbidGuest();

        $campaignRepository = new CampaignRepository();
        $rawCampaigns = $campaignRepository->findAllWithDm();
        $campaigns = array_map(
            fn (array $campaign): array => array_merge($campaign, [
                'last_played_label' => $this->formatLastPlayed(
                    isset($campaign['last_played_at']) ? (string) $campaign['last_played_at'] : null,
                ),
            ]),
            $rawCampaigns,
        );

        $this->render('campaigns', [
            'pageTitle' => 'Campaign Manager',
            'campaigns' => $campaigns,
            'activeCampaignId' => $this->resolveActiveCampaignId($campaigns),
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

        $this->rememberLastDmCampaignId($id);
        $campaign['last_played_label'] = $this->formatLastPlayed(
            isset($campaign['last_played_at']) ? (string) $campaign['last_played_at'] : null,
        );

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

    private function resolveActiveCampaignId(array $campaigns): ?int
    {
        if ($campaigns === []) {
            return null;
        }

        $campaignIds = array_map(
            static fn (array $campaign): int => (int) $campaign['id'],
            $campaigns,
        );

        $lastCampaignId = $this->getLastDmCampaignId();
        if ($lastCampaignId !== null && in_array($lastCampaignId, $campaignIds, true)) {
            return $lastCampaignId;
        }

        return $campaignIds[0];
    }

    private function formatLastPlayed(?string $timestamp): string
    {
        if ($timestamp === null || trim($timestamp) === '') {
            return 'Not played yet';
        }

        try {
            $date = new DateTimeImmutable($timestamp);

            return $date->format('d M Y, H:i');
        } catch (Throwable) {
            return 'Not played yet';
        }
    }
}