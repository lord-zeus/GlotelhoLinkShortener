<p align="center">
    <a href="https://laravel.com" target="_blank">
        <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
    </a>
</p>

<p align="center">
    <a href="https://github.com/laravel/framework/actions">
        <img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status">
    </a>
    <a href="https://packagist.org/packages/laravel/framework">
        <img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/laravel/framework">
        <img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/laravel/framework">
        <img src="https://img.shields.io/packagist/l/laravel/framework" alt="License">
    </a>
</p>

---

# 🔗 GlotelhoLinkShortener

A full-featured URL shortening service built with Laravel, Vue.js, and Docker. Features include custom short URLs, click analytics, and user authentication.

## 🌟 Features

- **URL Shortening**: Convert long URLs to short, memorable links
- **Custom Codes**: Set your own short code aliases
- **Analytics Dashboard**: Track clicks, referrers, and geographic data
- **API Support**: RESTful endpoints for integration
- **User Authentication**: Secure token-based auth with Sanctum
- **Dockerized**: Ready-to-run with Laravel Sail

## 🧰 Requirements

- Docker & Docker Compose
- Node.js v18+
- Composer 2.2+
- PHP 8.2+ (optional for local dev)

---

## 🚀 Quick Start

### 1. Clone and Setup

```bash
git clone https://github.com/your-username/GlotelhoLinkShortener.git
cd GlotelhoLinkShortener
cp .env.example .env
```

## 2. Start Containers
```bash 
./vendor/bin/sail up

```

## 3. Install Dependencies
```bash 
./vendor/bin/sail composer install
./vendor/bin/sail npm install

```

## 4. Configure Application
```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```
## 5. Build Frontend
```bash
./vendor/bin/sail npm run dev
```
## 6. Access Application
```bash
http://localhost:8002
```
## 🛠️ Development Commands
```bash 
sail artisan migrate

sail npm run dev

sail npm run dev
```

