# BlueShores Music Taste Tracker

A web application that allows employees to share and vote on their favorite music albums, helping the company adapt the office music to employee preferences.

## Features

- User registration and authentication
- Album browsing with search functionality
- Upvoting and downvoting albums
- Admin capabilities for album management
- Responsive design for all devices

## Tech Stack

- **Backend**: Laravel 10
- **Frontend**: Vue 3 with TypeScript
- **State Management**: Pinia
- **Routing**: Vue Router
- **Styling**: SCSS (no external libraries)

## Setup Instructions

### Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js 16 or higher
- npm or yarn
- MySQL or PostgreSQL
- Docker and Docker Compose (optional)

### Installation

1. Clone the repository:
```bash
git clone https://github.com/christianmartincabucos/music-taste-tracker.git
cd music-taste-tracker
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
npm install
```

4. Set up the environment file:
```bash
cp .env.example .env
```
Update the `.env` file with your database credentials and other configuration settings.

5. Generate the application key:
```bash
php artisan key:generate
```

6. Run database migrations:
```bash
php artisan migrate
```

7. Start the development server:
```bash
php artisan serve
```

8. Compile frontend assets:
```bash
npm run dev
```

9. Access the application in your browser at `http://localhost:8000`.

### Using Docker (Optional)

If you prefer to use Docker for development, follow these steps:

1. Ensure Docker and Docker Compose are installed on your machine.

2. Build and start the containers:
```bash
docker-compose up --build
```

3. Access the application in your browser at `http://localhost:8000`.

4. To stop the containers, run:
```bash
docker-compose down
```

### Deployment

For production, ensure you:
- Use `php artisan config:cache` to cache configuration.
- Use `npm run build` to compile production-ready assets.
- Set up a web server like Nginx or Apache to serve the application.
- If using Docker, configure your production environment in the `docker-compose.prod.yml` file.
