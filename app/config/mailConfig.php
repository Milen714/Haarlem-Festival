<?php
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();
return [
    'host'       => $_ENV['MAIL_HOST'] ?? '',
    'username'   => $_ENV['MAIL_USERNAME'] ?? '',
    'password'   => $_ENV['MAIL_PASSWORD'] ?? '',
    'port'       => $_ENV['MAIL_PORT'] ?? 587,
    'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls', // tls or ssl
    'from' => [
        'email' => $_ENV['MAIL_EMAIL'] ?? '',
        'name'  => $_ENV['MAIL_NAME'] ?? ''
    ]
];
?>