<?php

declare(strict_types=1);

namespace App\Controller;

abstract class AppController
{
    protected function render(string $template, array $variables = []): void
    {
        $viewsPath = dirname(__DIR__, 2) . '/public/views';
        $templatePath = $viewsPath . '/' . $template . '.html.php';
        $layoutPath = $viewsPath . '/layout.html.php';

        if (!is_file($templatePath)) {
            throw new \RuntimeException('View not found: ' . $template);
        }

        $userRole = $this->getUserRole();
        $currentUser = $_SESSION['user'] ?? null;
        $isLoggedIn = $userRole !== null;
        $userName = $isLoggedIn ? (string) ($currentUser['name'] ?? 'Adventurer') : 'User not logged in';
        $guestCampaignId = ($userRole === 'guest') ? $this->getGuestCampaignId() : null;

        $globalVariables = [
            'isLoggedIn' => $isLoggedIn,
            'userRole' => $userRole,
            'userName' => $userName,
            'guestCampaignId' => $guestCampaignId,
        ];

        extract(array_merge($globalVariables, $variables), EXTR_SKIP);

        ob_start();
        require $templatePath;
        $content = (string) ob_get_clean();

        require $layoutPath;
    }

    protected function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function getUserRole(): ?string
    {
        $this->ensureSession();

        $currentUser = $_SESSION['user'] ?? null;
        if (!is_array($currentUser)) {
            return null;
        }

        return (string) ($currentUser['role'] ?? 'dm');
    }

    protected function getGuestCampaignId(): int
    {
        $this->ensureSession();

        return (int) ($_SESSION['user']['campaign_id'] ?? 1);
    }

    protected function rememberLastDmCampaignId(int $campaignId): void
    {
        if ($this->getUserRole() === 'guest') {
            return;
        }

        $this->ensureSession();
        $_SESSION['last_dm_campaign_id'] = $campaignId;
    }

    protected function getLastDmCampaignId(): ?int
    {
        $this->ensureSession();

        $lastCampaignId = $_SESSION['last_dm_campaign_id'] ?? null;

        return is_int($lastCampaignId) && $lastCampaignId > 0 ? $lastCampaignId : null;
    }

    protected function redirectIfAuthenticated(): void
    {
        $role = $this->getUserRole();

        if ($role === 'dm') {
            $this->redirect('/campaigns');
        }

        if ($role === 'guest') {
            $this->redirect('/campaigns/' . $this->getGuestCampaignId());
        }
    }

    protected function forbidGuest(): void
    {
        if ($this->getUserRole() === 'guest') {
            $this->redirect('/campaigns/' . $this->getGuestCampaignId());
        }
    }

    protected function redirect(string $path): never
    {
        header('Location: ' . $path, true, 303);
        exit;
    }
}