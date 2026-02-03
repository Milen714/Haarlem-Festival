<?php
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





echo "Stripe Secret Key: " . $stripeSecretKey . "<br>";
echo "Domain URL: " . $DOMAIN_URL . "<br>";