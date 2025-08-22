# Book Sharing Platform API

This is a **Laravel-based RESTful API** for a simple Book Sharing Platform with **User** and **Admin** functionalities. Users can register, login, share books, and view nearby books. Admins can view all users, view all books, and delete books.

---

## Features

* User registration and login (JWT authentication)
* Share a book (title, author, description)
* View nearby books using geolocation
* Admin panel APIs:

    * View all users
    * View all books
    * Delete books
* Health check API
* Proper request validation
* Database migrations and seeders included

---

## Requirements

* PHP >= 8.1 (tested on 8.3)
* Laravel >= 12
* MySQL >= 8
* Composer
* Node.js & npm (optional if using Laravel Mix for assets)

---

## Installation

1. **Clone the repository**

```bash
git clone https://github.com/shishir-chowdhury/book-sharing-platform.git
cd book-sharing-platform
```

2. **Install PHP dependencies**

```bash
composer install
```

3. **Copy `.env` file**

```bash
cp .env.example .env
```

4. **Configure `.env`**

Update the database credentials and other configurations:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bookshare
DB_USERNAME=root
DB_PASSWORD=secret

APP_NAME=BookShare
APP_ENV=local
APP_KEY=base64:GENERATED_KEY
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

BOOK_RADIUS_KM=10
VERSION=1.0.0
```

Generate the Laravel app key:

```bash
php artisan key:generate
```

---

## Database Setup

1. **Create the database** in MySQL:

```sql
CREATE DATABASE bookshare;
```

2. **Run migrations**:

```bash
php artisan migrate
```

3. **Seed sample data** (optional for books):

```bash
php artisan db:seed
```

---

## Run the Application

Start the Laravel development server:

```bash
php artisan serve
```

The API will be available at:

```
http://127.0.0.1:8000/api
```

---

## API Endpoints

### User APIs

* `POST /api/register` → Register user
* `POST /api/login` → Login and get JWT token
* `POST /api/books` → Share a book (auth required)
* `GET /api/books/nearby` → View nearby books (auth required)

### Admin APIs

* `GET /api/admin/users` → View all users
* `GET /api/admin/books` → View all books
* `DELETE /api/delete-books/{id}` → Delete a book

### Health Check

* `GET /api/health` → Check application status

> All protected endpoints require JWT token in the header:

```
Authorization: Bearer {token}
```

---

## Notes

* Nearby books distance is calculated using MySQL `POINT` column and `ST_Distance_Sphere()`.
* Admin routes are protected by `is_admin` middleware.
* `.env` variable `BOOK_RADIUS_KM` can be changed to set the default nearby search radius.

---

## Testing

You can test all endpoints using **Postman** or any API client. Use the token received from login for protected routes.

---

## License

MIT License
