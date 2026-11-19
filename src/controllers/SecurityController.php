<?php

declare(strict_types=1);

namespace App\Controller;


final class SecurityController extends AppController
{
    public function login(): void
    {
        $this->render('login', [
            'pageTitle' => 'Logowanie do konta',
        ]);
    }

    public function loginPost(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $errors = [];

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (trim($password) === '') {
            $errors[] = 'Please enter your password.';
        }

        if ($errors !== []) {
            $this->render('login', [
                'pageTitle' => 'Logowanie do konta',
                'errors' => $errors,
                'old' => ['email' => $email],
            ]);
            return;
        }

        $this->ensureSession();
        session_regenerate_id(true);

        $displayName = strstr($email, '@', true);
        $_SESSION['user'] = [
            'name' => ($displayName !== false && $displayName !== '') ? $displayName : $email,
            'email' => $email,
        ];

        $this->redirect('/campaigns');
    }

    public function register(): void
    {
        $this->render('register', [
            'pageTitle' => 'Rejestracja konta',
        ]);
    }

    public function registerPost(): void
    {
        $username = trim((string) ($_POST['username'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');
        $errors = [];

        if ($username === '') {
            $errors[] = 'Please enter a username.';
        }

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (trim($password) === '') {
            $errors[] = 'Please enter a password.';
        }

        if ($password !== $passwordConfirm) {
            $errors[] = 'Passwords do not match.';
        }

        if ($errors !== []) {
            $this->render('register', [
                'pageTitle' => 'Rejestracja konta',
                'errors' => $errors,
                'old' => [
                    'username' => $username,
                    'email' => $email,
                ],
            ]);
            return;
        }

        $this->redirect('/login');
    }

    public function forgotPassword(): void
    {
        $this->render('forgot-password', [
            'pageTitle' => 'Odzyskiwanie hasła',
        ]);
    }

    public function resetPassword(): void
    {
        $this->render('reset-password', [
            'pageTitle' => 'Ustawienie nowego hasła',
        ]);
    }
}