# Haarlem Festival

A full-stack web application built for the Haarlem Festival — a multi-day event in the city of Haarlem featuring jazz, dance, magic, and food. This app handles everything from browsing events and buying tickets to managing the festival through an admin CMS. It was built as a school project at Hogeschool Inholland.

---

## Documentation & Design

- [📋 Figma Documentation](https://www.figma.com/design/kGgpd20OKJPyzFJRY4MJAQ/HaarlemFestivalDocumentation-IT2C-Grp4?node-id=0-1&t=FEGwT3mtH1ni8inX-1)
- [🎨 Figma Design](https://www.figma.com/design/z2mHRFXuuakpjFqsZa3gZG/IT2CGroup4?node-id=12300-61594&t=GwRH3TRw4t3zxWU8-1)
- 📁 **[Documentation/](./Documentation/)** — Raw diagram files and PNG exports of the same diagrams for offline viewing

> **Note:** All documentation and diagrams are hosted in Figma to provide a comfortable viewing experience for large, complex diagrams. Local exports (raw files and PNGs) are available in the Documentation folder for offline reference.

---

## Tech Stack

| Layer            | Technology                                |
| ---------------- | ----------------------------------------- |
| Backend          | PHP (custom MVC with nikic/fast-route)    |
| Database         | MySQL hosted on Aiven Cloud               |
| Frontend         | HTML, Tailwind CSS, Vanilla JavaScript    |
| Payments         | Stripe (with webhook support)             |
| Email            | PHPMailer via Gmail SMTP                  |
| PDF Tickets      | DomPDF                                    |
| QR Codes         | chillerlan/php-qrcode                     |
| CMS Editor       | TinyMCE                                   |
| Containerization | Docker (Nginx, PHP-FPM, Cron, PhpMyAdmin) |

---

## Getting Started

### Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- [Node.js & npm](https://nodejs.org/) (for Tailwind CSS)
- [Composer](https://getcomposer.org/) (optional if you want to run things outside Docker)

### 1. Clone the repo

```bash
git clone <repo-url>
cd Haarlem-Festival
```

### 2. Set up your environment

Copy the example env files and fill in your credentials:

```bash
cp .env.example .env
cp app/.env.example app/.env
```

You'll need to provide:

- MySQL / Aiven database credentials
- Stripe API keys (public + secret)
- Gmail SMTP credentials
- Google Maps API key
- Google reCAPTCHA keys

A database dump file (`DatabaseDump.sql`) is available in the root for local development. Import it into your database to get started with sample data.

### 3. Start Docker

```bash
docker-compose up -d
```

This spins up Nginx, PHP-FPM, PhpMyAdmin, Stripe CLI (for local webhook testing), and the cron container.

The app will be available at `http://localhost`.
PhpMyAdmin is available at `http://localhost:8080`.

### 4. Configure Stripe webhooks

The Stripe CLI container automatically forwards webhook events to your local app. After Docker starts, grab the webhook signing secret it generates:

```bash
docker-compose logs stripe-cli | grep "webhook signing secret"
```

Copy the `whsec_...` value and paste it into `STRIPE_WEBHOOK_SECRET` in both `.env` and `app/.env`. Without this step, payments will process but order confirmation won't trigger.

### 5. Start the CSS watcher

In a separate terminal from the project root:

```bash
cd app && npm run watch
```

> Keep this running while you develop — Tailwind won't generate styles without it.

---

## File Structure

```
Haarlem-Festival/
├── app/                          # Main PHP application
│   ├── public/                   # Web root (entry point)
│   │   ├── index.php             # Front controller
│   │   ├── Assets/               # Images, fonts, etc.
│   │   ├── Js/                   # JavaScript files
│   │   └── css/                  # Compiled Tailwind output
│   ├── src/                      # Application source code
│   │   ├── Controllers/          # Route handlers (one per feature area)
│   │   ├── Models/               # Data models
│   │   │   ├── Enums/
│   │   │   ├── History/
│   │   │   ├── MusicEvent/
│   │   │   ├── Payment/
│   │   │   └── Yummy/
│   │   ├── Repositories/         # Database access layer
│   │   │   └── Interfaces/
│   │   ├── Services/             # Business logic layer
│   │   │   └── Interfaces/
│   │   ├── ViewModels/           # Data passed to views
│   │   │   ├── Dance/
│   │   │   ├── History/
│   │   │   ├── Home/
│   │   │   ├── Magic/
│   │   │   ├── ShoppingCart/
│   │   │   └── Yummy/
│   │   ├── CmsModels/            # CMS-specific models
│   │   ├── Framework/            # Custom routing & core framework
│   │   ├── Middleware/           # Auth and request middleware
│   │   ├── Exceptions/           # Custom exception classes
│   │   └── PhpConverters/        # Data transformation helpers
│   ├── Views/                    # HTML templates (PHP views)
│   │   ├── Account/
│   │   ├── Cms/
│   │   ├── Dance/
│   │   ├── Email/
│   │   ├── Errors/
│   │   ├── History/
│   │   ├── Home/
│   │   ├── Jazz/
│   │   ├── Magic/
│   │   ├── Orders/
│   │   ├── ShoppingCart/
│   │   ├── Yummy/
│   │   └── layouts/              # Shared layout templates
│   ├── CmsModels/
│   ├── config/                   # Runtime config files
│   ├── cli/                      # CLI scripts for cron jobs
│   ├── migrations/               # App-level migration scripts
│   ├── logs/                     # Application logs
│   ├── TicketPDFs/               # Generated PDF tickets
│   ├── vendor/                   # Composer dependencies
│   ├── composer.json
│   ├── package.json
│   └── tailwind.config.js
├── database/
│   └── migrations/               # SQL migration files
├── PHP.Dockerfile                # PHP-FPM container definition
├── Cron.Dockerfile               # Cron job container definition
├── docker-compose.yml            # Full service orchestration
├── nginx.conf                    # Nginx server config
├── ca.pem                        # Aiven SSL certificate
├── composer.json                 # Root-level PHP dependencies
└── .gitignore
```

---

## Features

### Festival Programs

- **Jazz** — Browse jazz performances, venues, and artists. Buy tickets per session.
- **Dance** — Dance event listings with artist profiles and ticketing.
- **Magic** — Magic show schedule and ticket purchasing.
- **Yummy** — Restaurant and food vendor listings for the festival's food program.
- **History** — Informational page about the history of the Haarlem Festival.

### Ticketing & Orders

- Add tickets to a shopping cart
- Checkout with Stripe (test & live mode)
- PDF tickets generated and emailed on purchase
- QR codes embedded in each ticket
- Order history available in user accounts

### User Accounts

- Register and log in
- View your orders and personal program
- Build a custom schedule from events you're interested in

### CMS / Admin

- Manage event content via a TinyMCE-powered editor
- Manage artists, landmarks, and employees
- Admin-only routes protected by middleware

---

## Test User Data

### Regular Users

| Name      | Email          | Password     | Notes |
| --------- | -------------- | ------------ | ----- |
| Test User | user@test.mail | Password@123 |       |

### Employee Users

| Name          | Email              | Password     | Notes |
| ------------- | ------------------ | ------------ | ----- |
| Test Employee | employee@test.mail | Password@123 |       |

### Admin / CMS Users

| Name       | Email           | Password     | Role  |
| ---------- | --------------- | ------------ | ----- |
| Test Admin | admin@test.mail | Password@123 | Admin |

### Test Payment Cards (Stripe)

| Card Number         | Expiry          | CVC | Scenario           |
| ------------------- | --------------- | --- | ------------------ |
| 4242 4242 4242 4242 | Any future date | Any | Successful payment |
| 4000 0000 0000 9995 | Any future date | Any | Declined card      |

---

## Troubleshooting

### CSS not working?

Run the Tailwind watcher from the project root:

```bash
cd app && npm run watch
```

If that doesn't work on Windows:

```bash
cd .\app\
cmd /c npm run watch
```

Keep this terminal running while developing.

### Stripe webhooks not firing locally?

The Docker setup includes the Stripe CLI. Make sure your `STRIPE_WEBHOOK_SECRET` in `app/.env` matches the secret output by the Stripe CLI container.
