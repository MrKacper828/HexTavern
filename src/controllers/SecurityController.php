<?php

declare(strict_types=1);

namespace App\Controller;


final class SecurityController extends AppController
{
    public function login(): void
    {
        $this->redirectIfAuthenticated();

        $this->render('login', [
            'pageTitle' => 'Logowanie do konta',
        ]);
    }

    public function loginPost(): void
    {
        $this->redirectIfAuthenticated();

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
            'role' => 'dm',
        ];

        $this->redirect('/campaigns');
    }

    public function logoutPost(): void
    {
        $this->ensureSession();

        $_SESSION = [];

        $sessionName = session_name();
        if (ini_get('session.use_cookies') && is_string($sessionName)) {
            $params = session_get_cookie_params();
            setcookie(
                $sessionName,
                '',
                time() - 42000,
                (string) $params['path'],
                (string) $params['domain'],
                (bool) $params['secure'],
                (bool) $params['httponly'],
            );
        }

        session_destroy();

        $this->redirect('/');
    }

    public function register(): void
    {
        $this->redirectIfAuthenticated();

        $this->render('register', [
            'pageTitle' => 'Rejestracja konta',
        ]);
    }

    public function registerPost(): void
    {
        $this->redirectIfAuthenticated();

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
        $this->redirectIfAuthenticated();

        $this->render('forgot-password', [
            'pageTitle' => 'Odzyskiwanie hasła',
        ]);
    }

    public function forgotPasswordPost(): void
    {
        $this->redirectIfAuthenticated();

        $email = trim((string) ($_POST['email'] ?? ''));
        $errors = [];

        if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Please enter a valid email address.';
        }

        if ($errors !== []) {
            $this->render('forgot-password', [
                'pageTitle' => 'Odzyskiwanie hasła',
                'errors' => $errors,
                'old' => ['email' => $email],
            ]);
            return;
        }

        $this->redirect('/reset-password');
    }

    public function resetPassword(): void
    {
        $this->redirectIfAuthenticated();

        $this->render('reset-password', [
            'pageTitle' => 'Ustawienie nowego hasła',
        ]);
    }

    public function resetPasswordPost(): void
    {
        $this->redirectIfAuthenticated();

        $password = (string) ($_POST['password'] ?? '');
        $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');
        $errors = [];

        if (trim($password) === '') {
            $errors[] = 'Please enter a new password.';
        }

        if ($password !== $passwordConfirm) {
            $errors[] = 'Passwords do not match.';
        }

        if ($errors !== []) {
            $this->render('reset-password', [
                'pageTitle' => 'Ustawienie nowego hasła',
                'errors' => $errors,
            ]);
            return;
        }

        $this->redirect('/login');
    }
}