# White Lotus Trading - F.Z.E.

A PHP-based CMS and product catalog for White Lotus Trading, bridging industrial HVAC solutions with organic wellness trading.

## Features

- **Product Catalog** — HVAC and Consumables divisions with search, filter, and pagination
- **Blog Engine** — Category filtering, sidebar with recent posts
- **Inquiry System** — Contact form with division targeting, admin inbox
- **Admin Dashboard** — CRUD for products, blog posts, pages; manage inquiries
- **Security** — CSRF tokens, bcrypt (cost 12), rate-limited login, SQLite sessions, CSP headers

## Tech Stack

- **Backend:** Core PHP 8.1+ (no frameworks)
- **Database:** SQLite via PDO
- **Frontend:** Tailwind CSS v3 (CDN), Material Symbols
- **Typography:** Plus Jakarta Sans + Inter (Google Fonts)

## Setup

### Requirements
- PHP 8.1+
- SQLite3 extension enabled

### Installation

```bash
# Clone the repo
git clone https://github.com/yolk13/whitelotustrading.git
cd whitelotustrading

# Seed the database
php install.php

# Start dev server
php -S localhost:8080 _dev_router.php
```

### Default Admin Login
> **Change this immediately after first login.**
> - URL: `http://localhost:8080/admin/login`
> - Username: `admin`
> - Password: `admin123`

## Project Structure

```
├── admin/           # Admin panel pages
├── core/            # Framework core (Database, Security, Auth, Session, Router)
├── data/            # SQLite database (gitignored)
├── includes/        # Shared templates (header, footer, admin-header)
├── models/          # Data models (Product, Page, Post, Inquiry, User)
├── public/          # Public-facing pages
├── uploads/         # Uploaded images (gitignored except .htaccess)
├── assets/          # CSS, JS, images
├── index.php        # Front controller
├── _dev_router.php  # PHP built-in server router
└── install.php      # Database seeder (delete after first use)
```

## License

All rights reserved. White Lotus Trading - F.Z.E.
