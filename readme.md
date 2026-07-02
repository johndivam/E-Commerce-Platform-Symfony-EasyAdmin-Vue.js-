# E-Commerce Platform

A modern full-stack e-commerce platform built with **Symfony 7.4**, **EasyAdmin 5**, and **Vue.js**. The project provides a powerful administration panel for managing products, orders, customers, and inventory, alongside a fast and responsive customer-facing storefront powered by Vue.js.

## Features

### Administration Panel

* Product management
* Category management
* Brand management
* Customer management
* Order management
* Dashboard statistics
* Role & permission management
* Media upload and management
* Search and filtering tools

### Customer Storefront

* Responsive design
* Product catalog browsing
* Product search and filtering
* Shopping cart
* Customer authentication
* User profile management
* Order history
* Checkout process

### Security

* JWT Authentication
* Role-based access control
* Secure API endpoints
* Password hashing and validation

---

## Tech Stack

### Backend

* PHP 8.2+
* Symfony 7.4
* EasyAdmin 5
* Doctrine ORM
* LexikJWTAuthenticationBundle
* MySQL / PostgreSQL

### Frontend

* Vue.js
* Vue Router
* Pinia / Vuex
* Axios

---

## Requirements

* PHP >= 8.2
* Composer
* Node.js >= 22.20
* NPM or Yarn
* MySQL 8+ or PostgreSQL

---

## Installation

### Clone Repository

```bash
git clone https://github.com/your-company/ecommerce.git
cd ecommerce
```

### Install Backend Dependencies

```bash
composer install
```

### Configure Environment

Copy the environment file:

```bash
cp .env .env.local
```

Update database credentials:

```env
DATABASE_URL="mysql://user:password@127.0.0.1:3306/ecommerce"
```

### Create Database

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### Generate JWT Keys

```bash
php bin/console lexik:jwt:generate-keypair
```

### Install Frontend Dependencies

```bash
cd frontend
npm install
```

### Start Development Servers

Backend:

```bash
symfony server:start
```

Frontend:

```bash
npm run dev-server
```

---


## Screenshots

### Products page 
![Products page](.readme/screenshots/products-page.png)

### Product view
![Product view](.readme/screenshots/product-view.jpg)

### Manage Products (EasyAdmin)
![Manage Products](.readme/screenshots/admin-products.jpg)


## API Authentication

Authenticate using JWT:

```http
POST /api/login
```

Example response:

```json
{
    "token": "jwt_token_here"
}
```

Use the token in subsequent requests:

```http
Authorization: Bearer {token}
```

---

## Project Structure

```text
├── src/
│   ├── Controller/
│   ├── DataFixtures/
│   ├── Entity/
│   ├── Form/
│   ├── Repository/
│   ├── Service/
│   └── Security/
│
├── config/
├── migrations/
├── public/
├── templates/
│
├── assets/
│   ├── components/
│   ├── router/
│   └── services/
│   └── styles/
│   └── app.js
│
└── webpack.config.js
```

---

## Main Entities

* Brand
* Category
* Product
* Client
* Order
* Order Item
* Cart
* Cart Item
* User
* Spatie roles-permissions



## License

This project is licensed under the MIT License.
