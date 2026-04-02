<?php
namespace App\Config;
// Keep your Stripe API key protected by including it as an environment variable
// or in a private script that does not publicly expose the source code.

// Load from environment variable - set STRIPE_SECRET_KEY in your .env or server config
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();
// Stripe
$stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'] ?? '';
// Domain URL
$DOMAIN_URL = $_ENV['DOMAIN_URL'] ?? 'http://localhost';

class Secrets
{
    public static string $secretKey;
    public static string $domain;
    public static int $tokenExpirationHours;
    public static string $stripeSecretKey;
    public static string $stripePublicKey;
    public static string $stripeWebhookSecret;
    public static string $reCapchaSiteKey;
    public static string $reCapchaSecretKey;
    public static string $mapsApiKey;

    public static function init(): void
    {
        self::$secretKey = getenv('SECRET_KEY') ?: $_ENV['SECRET_KEY'] ?? 'your-secret-key-change-this-in-production';
        self::$domain = getenv('DOMAIN_URL') ?: $_ENV['DOMAIN_URL'] ?? 'http://localhost';
        self::$tokenExpirationHours = (int)(getenv('TOKEN_EXPIRATION_HOURS') ?: $_ENV['TOKEN_EXPIRATION_HOURS'] ?? 24);
        self::$stripeSecretKey = getenv('STRIPE_SECRET_KEY') ?: $_ENV['STRIPE_SECRET_KEY'] ?? '';
        self::$stripePublicKey = getenv('STRIPE_PUBLISHABLE_KEY') ?: $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? '';
        self::$stripeWebhookSecret = getenv('STRIPE_WEBHOOK_SECRET') ?: $_ENV['STRIPE_WEBHOOK_SECRET'] ?? '';
        self::$reCapchaSiteKey = getenv('RECAPTCHA_SITE_KEY') ?: $_ENV['RECAPTCHA_SITE_KEY'] ?? '';
        self::$reCapchaSecretKey = getenv('RECAPTCHA_SECRET_KEY') ?: $_ENV['RECAPTCHA_SECRET_KEY'] ?? '';
        self::$mapsApiKey = getenv('MAPS_API_KEY') ?: $_ENV['MAPS_API_KEY'] ?? '';
    }
}

Secrets::init();