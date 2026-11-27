<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CampaignRepository;

final class CharacterController extends AppController
{
    public function index(int $id): void
    {
        $this->forbidGuest();

        $campaignRepository = new CampaignRepository();
        $campaign = $campaignRepository->findByIdWithDm($id);

        if ($campaign === null) {
            http_response_code(404);
            $this->render('not-found', [
                'pageTitle' => 'Campaign Not Found',
            ]);
            return;
        }

        $this->rememberLastDmCampaignId($id);

        $this->render('characters', [
            'pageTitle' => 'Character Scrolls',
            'campaignId' => $id,
            'campaign' => $campaign,
            'characters' => $this->getPlaceholderCharacters($id),
        ]);
    }

    public function show(int $id): void
    {
        $this->render('character', [
            'pageTitle' => 'postać',
            'characterId' => $id,
        ]);
    }

    /**
     * Tymczasowa lista przykładowych postaci w formacie docelowego CharacterRepository.
     */
    private function getPlaceholderCharacters(int $campaignId): array
    {
        return [
            [
                'id' => 1,
                'campaign_id' => $campaignId,
                'name' => 'Brokk Ironbreaker',
                'avatar_path' => null,
            ],
            [
                'id' => 2,
                'campaign_id' => $campaignId,
                'name' => 'Lyra Nightshade',
                'avatar_path' => null,
            ],
            [
                'id' => 3,
                'campaign_id' => $campaignId,
                'name' => 'Eldrin the Grey',
                'avatar_path' => null,
            ],
        ];
    }
}