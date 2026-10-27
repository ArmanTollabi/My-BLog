# My-BLog

A Laravel-based blog application built with PHP, Laravel, and MySQL.

## Features

- **Authentication:** User registration, login, and password reset.
- **Post Management:** Create, view, edit, and manage blog posts.
- **Categories and Tags:** Organize posts using categories and tags.
- **Comments and Replies:** Comment on posts and participate in nested discussions.
- **User Management:** Browse user profiles and manage users according to permissions.
- **Admin Panel:** Manage posts and moderate comments through administrative interfaces.
- **Notifications:** Notify users about relevant application activity.
- **REST API:** API endpoints for posts, categories, tags, and users.
- **Image Uploads:** Upload post images and user avatars.
- **Search and Pagination:** Find content and navigate through paginated results.
- **Authorization:** Policies and middleware for access control.

## Tech Stack

- PHP
- Laravel
- MySQL
- Blade
- JavaScript
- Vite
- Tailwind CSS

## Requirements

Before installing the project, make sure you have:

- PHP compatible with the version specified in `composer.json`
- Composer
- Node.js and npm
- A supported database, such as MySQL
- Git

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/ArmanTollabi/My-BLog.git
cd My-BLog
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Configure the environment

```bash
cp .env.example .env
php artisan key:generate
```

On Windows PowerShell, use the following command instead of `cp` if needed:

```powershell
Copy-Item .env.example .env
```

### 4. Configure the database

Create a database in your database server, then update the database settings in `.env`.

For a MySQL configuration, set the appropriate values for:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

Use your own database credentials. Never commit your real `.env` file.

### 5. Run database migrations

```bash
php artisan migrate
```

### 6. Install frontend dependencies

```bash
npm install
npm run build
```

### 7. Create the public storage link

```bash
php artisan storage:link
```

### 8. Start the development server

```bash
php artisan serve
```

Open the local URL displayed in your terminal, usually `http://127.0.0.1:8000`.

## API

The project includes API routes for selected resources.

To inspect the available endpoints, run:

```bash
php artisan route:list --path=api
```

## Testing

Run the automated test suite with:

```bash
php artisan test
```

## Performance Testing

A load-testing script is available at:

`performance-tests/posts-load-test.js`

The script is intended for use with [k6](https://k6.io/). Install k6 and review the script configuration before running it against your local development server.

## Security

- Keep `.env` and all real credentials private.
- Do not commit API keys, passwords, or other secrets.
- Configure the application for production before deploying it publicly.

## License

No project-specific license has been declared yet.
