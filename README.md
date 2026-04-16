<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Portfolio API (Admin + Public)

This backend now includes a portfolio API where:

- Admin can log in.
- Admin can create folders.
- Admin can upload work sample images from desktop (multipart form-data, not image URLs).
- Public users can fetch folders with all work samples for slider/card UI.

### 1) Environment setup

Update `.env` with a working MySQL connection and these values:

```env
APP_URL=http://localhost:8000
FRONTEND_URL=http://localhost:3000

ADMIN_NAME="Portfolio Admin"
ADMIN_EMAIL=admin@portfolio.test
ADMIN_PASSWORD=admin12345
```

Then run:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

### 2) Auth flow (Admin)

#### Login

`POST /api/admin/login`

```json
{
    "email": "admin@portfolio.test",
    "password": "admin12345"
}
```

Response includes `token`.

Use this header for protected routes:

```text
Authorization: Bearer YOUR_TOKEN
```

#### Logout

`POST /api/admin/logout`

### 3) Folder management (Admin only)

- `GET /api/admin/folders`
- `POST /api/admin/folders`
- `GET /api/admin/folders/{id}`
- `PUT /api/admin/folders/{id}`
- `DELETE /api/admin/folders/{id}`

Create folder body:

```json
{
    "name": "Web Apps",
    "description": "React and Laravel projects",
    "sort_order": 1
}
```

### 4) Work samples (Admin only)

#### Add work sample with desktop image upload

`POST /api/admin/folders/{folderId}/samples`

`Content-Type: multipart/form-data`

Fields:

- `project_name` (text, required)
- `description` (text, optional)
- `sort_order` (number, optional)
- `image` (file, required: jpg/jpeg/png/webp, max 8MB)

#### Update sample

`PUT /api/admin/samples/{sampleId}`

Accepts same fields; `image` is optional.

#### Delete sample

`DELETE /api/admin/samples/{sampleId}`

### 5) Public endpoints (Frontend)

Use these for folder cards + image slides:

- `GET /api/portfolio/folders`
- `GET /api/portfolio/folders/{id}`

Each work sample includes:

- `project_name`
- `description`
- `image_url`

`image_url` is a full URL, ready to use directly in the frontend slider image source.
