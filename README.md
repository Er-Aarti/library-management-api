# Library Management API

## Overview

This is a library management system built with Laravel 10. Normal Users can view, borrow, and return books. Admins can manage books.

## Features

-   User registration and authentication
-   Role-based access (Admin & User)
-   Book management (CRUD for admins)
-   Borrow and return books (for users)
-   Event-driven actions for Manage Book Borrowing Log
-   Swagger API documentation for easy testing
-   Fake data generation using seeders

## Installation

1. Clone the repo:
   git clone (https://github.com/Er-Aarti/library-management-api.git)
2. Install dependencies:
   composer install
3. Setup .env file and database
4. Run migrations:
   php artisan migrate
5. Run the seeder:
   php artisan db:seed --class=BookSeeder
   php artisan db:seed --class=BookBorrowingSeeder
6. Run the server:
   php artisan serve

## Running Tests

php artisan test

## API Documentation

Swagger UI available at:
http://localhost:8000/api/documentation
