# PHP_Laravel12_Customer_Panel_With_Dashboard

A modern, responsive **Customer Panel with Dashboard** built using **Laravel 12**. This project provides a complete customer-side system including authentication, dashboard analytics, order tracking, and support ticket management. It is suitable for academic projects, production-ready demos, and GitHub portfolios.

---

## Features

### Dashboard

* Overview statistics (total orders, pending orders, open tickets, total spent)
* Recent activity (orders & tickets)
* Quick navigation shortcuts
* Fully responsive layout

### Customer Management

* Secure authentication (Laravel Breeze compatible)
* Profile update (address, phone, personal details)
* Password change functionality
* Email verification ready

### Order System

* Order history with pagination
* Order status tracking (Pending, Processing, Completed, Cancelled)
* Detailed order view

### Support Ticket System

* Create and manage support tickets
* Ticket priority (Low / Medium / High)
* Ticket status tracking (Open / In Progress / Resolved / Closed)

### UI / UX

* Bootstrap 5 based modern UI
* Responsive sidebar navigation
* Status badges and indicators
* Clean dashboard cards

---

## Technology Stack

* **Backend:** Laravel 12 (PHP 8.2+)
* **Frontend:** Blade Templates, Bootstrap 5
* **Database:** MySQL
* **Authentication:** Laravel Breeze
* **Icons:** Font Awesome

---

## Prerequisites

* PHP 8.2 or higher
* Composer
* MySQL / MariaDB
* Node.js & NPM (optional)
* Git

---

## Installation Guide

### Clone Repository

```bash
git clone https://github.com/yourusername/customer-panel.git
cd customer-panel
```

### Install Dependencies

```bash
composer install
```

### Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Update database credentials in `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=customer_panel
DB_USERNAME=root
DB_PASSWORD=
```

### (Optional) Frontend Assets

```bash
npm install
npm run build
```

### Run Migrations & Seeders

```bash
php artisan migrate --seed
```

### Run Application

```bash
php artisan serve
```

Visit: **[http://localhost:8000](http://localhost:8000)**

---

## Default Login (After Seeding)

* **Email:** [john@example.com](mailto:john@example.com)
* **Password:** password

---

### Screenshot

### *Login Page
<img width="904" height="732" alt="image" src="https://github.com/user-attachments/assets/ea409242-0289-48c2-95be-bcde8367a56f" />

### *Customer Panel
<img width="1919" height="966" alt="image" src="https://github.com/user-attachments/assets/1f440c50-ecbb-4de1-8891-53831f8f8c3a" />


## Project Structure

```
customer-panel/
├── app/Http/Controllers/
│   ├── DashboardController.php
│   ├── ProfileController.php
│   ├── OrderController.php
│   └── SupportTicketController.php
├── app/Models/
│   ├── User.php
│   ├── Order.php
│   └── SupportTicket.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/views/
│   ├── layouts/
│   ├── dashboard.blade.php
│   ├── orders/
│   ├── profile/
│   └── tickets/
├── routes/
├── public/
└── README.md
```

---

## Database Schema (Summary)

### Users

* id, name, email, password
* phone, address, city, state, zip
* status, timestamps

### Orders

* id, user_id, order_number
* total_amount, status, notes
* timestamps

### Support Tickets

* id, user_id, ticket_number
* subject, message
* priority, status
* timestamps

---

## Security Features

* CSRF protection
* Password hashing (bcrypt)
* Eloquent ORM (SQL injection safe)
* Session-based authentication
* Input validation via Form Requests

---

## Testing

```bash
php artisan test
```

---

## Deployment Options

### Shared Hosting (cPanel)

* Upload files except `vendor` & `node_modules`
* Set document root to `/public`
* Configure `.env`
* Run migrations via SSH

---

## Future Enhancements

* Admin panel
* Payment gateway integration
* PDF invoices
* Notifications & emails
* API support for mobile apps
* Multi-language support

---


