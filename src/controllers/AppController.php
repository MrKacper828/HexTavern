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

        $this->ensureSession();

        $currentUser = $_SESSION['user'] ?? null;
        $isLoggedIn = $currentUser !== null;
        $userName = $isLoggedIn ? ($currentUser['name'] ?? 'Adventurer') : 'User not logged in';

        $globalVariables = [
            'isLoggedIn' => $isLoggedIn,
            'userName' => $userName,
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

    protected function redirect(string $path): never
    {
        header('Location: ' . $path, true, 303);
        exit;
    }
}