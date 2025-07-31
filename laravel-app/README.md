This project is a RESTful API built with Laravel 11 to manage:

Basic user authentication (register, login, logout)

Viewing subscription plans

Initiating checkout for Stripe and PayPal

Logging and listing successful transactions

Tech stack:

Laravel 11

Laravel Sanctum (for authentication)

Stripe PHP SDK

PayPal REST API (via Guzzle)

SQLite 

🚀 Project setup

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate app key
php artisan key:generate

# Create sqlite database file (if using sqlite)
touch database/database.sqlite

# Run migrations and seeders
php artisan migrate --seed

# (Optional) Install Sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Serve the app
php artisan serve

📌 API Endpoints

Method

Endpoint

Description

POST

/api/register

Register a new user

POST

/api/login

Login and get token

POST

/api/logout

Logout (token required)

GET

/api/plans

List all subscription plans

POST

/api/checkout/stripe

Initiate Stripe checkout 

POST

/api/checkout/paypal

Initiate PayPal checkout 

GET

/api/my-transactions

List user's transactions (token required)

🧪 Postman / API usage guide

Register a user

POST /api/register
Content-Type: application/json

{
  "name": "Kerolos Bolis Ageeb",
  "email": "kerolos@example.com",
  "password": "password",
}

Login

POST /api/login
Content-Type: application/json

{
  "email": "kerolos@example.com",
  "password": "password"
}

Response will include: token.

Use token
In Postman, set Authorization Beare Token:

Authorization: Bearer <token>

List plans

GET /api/plans

Initiate Stripe checkout

POST /api/checkout/stripe
Content-Type: application/json

{
  "plan_id": 1,"user_id":1
}

Response: { "url": "<payment_url>" }

Initiate PayPal checkout

POST /api/checkout/paypal
Content-Type: application/json

{
  "plan_id": 1,"user_id":1
}

Response: { "url": "<approval_url>" }

List transactions

GET /api/my-transactions
Authorization: Bearer <token>

✅ Notes

Make sure to configure your Stripe and PayPal keys in .env

Only authenticated users (via Laravel Sanctum) can checkout and view their transactions

Plans are seeded into the database using PlanSeeder

Changing the route to api instead of web as laravel 11 or later didn't automatically make the api.php file

Project Structure
app/
├── Http/
│ ├── Controllers/
│ │ ├── AuthController.php
│ │ ├── PlanController.php
│ │ ├── CheckoutController.php
│ └── Requests/
├── Models/
│ ├── User.php
│ ├── Plan.php
│ └── Transaction.php
database/
├── seeders/PlanSeeder.php
routes/
└── api.php
config/
├── stripe.php
└── paypal.php

