<?php

declare(strict_types=1);

namespace App\Controller;

final class CharacterController extends AppController
{
    public function index(int $id): void
    {
        $this->forbidGuest();
        $this->rememberLastDmCampaignId($id);

        $this->render('characters', [
            'pageTitle' => 'Karty postaci',
            'campaignId' => $id,
        ]);
    }

    public function show(int $id): void
    {
        $this->render('character', [
            'pageTitle' => 'postać',
            'characterId' => $id,
        ]);
    }
}