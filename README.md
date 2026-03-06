# 🏗️ Forklift Booking Platform

> A production-grade forklift rental booking system with real-time availability, online payments, and a full admin dashboard — built with Laravel 12 and deployed to a live server.

🌐 **Live Demo:** [View Live](https://forklift.rajanandadev.com) &nbsp;|&nbsp; 

---

## ✨ Features

### Customer-Facing
- 📅 **Real-time booking calendar** — live availability tracking across multiple forklifts and locations
- 💳 **Online payments via Stripe** — secure checkout before booking is confirmed
- 📍 **Google Maps address autocomplete** — fast, accurate address entry
- 📧 **Email OTP verification** — secure account registration and login
- 📱 **Fully mobile responsive** — works seamlessly on all devices

### Admin Dashboard
- 📊 **Analytics & reporting** — bookings, revenue, and utilisation at a glance
- 🗓️ **Booking management** — view, approve, and manage all reservations
- 👥 **User management** — oversee customer accounts and access
- ⚙️ **Fleet & location configuration** — manage forklift inventory and service areas

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 12, PHP 8.3 |
| Database | MySQL |
| Frontend | Blade, Tailwind CSS |
| Payments | Stripe API |
| Maps | Google Maps API |
| Infrastructure | Linux, Git |

---

## 🔥 Engineering Highlights

### 1. Role-Based Authentication with Custom Middleware
Built a custom middleware layer that cleanly separates admin and user access:
- Admins bypass OTP verification for streamlined access
- Users go through a full email OTP verification flow
- Routes secured at the **middleware level** (not controller level) for a cleaner, more scalable architecture

### 2. Real-Time Booking Conflict Prevention
Engineered a conflict-detection system that prevents double-bookings:
- Validates time-slot availability **at the database level** before any payment is processed
- Handles simultaneous bookings across multiple forklifts and locations
- Atomic validation ensures no race conditions during checkout

---

## 🚀 Getting Started

### Prerequisites
- PHP 8.3+
- Composer
- MySQL
- Node.js & npm
- Stripe account (for payments)
- Google Maps API key

### Installation
```bash
# Clone the repository
git clone https://github.com/your-username/forklift-booking.git
cd forklift-booking

# Install PHP dependencies
composer install

# Install Node dependencies
npm install && npm run build

# Set up environment
cp .env.example .env
php artisan key:generate
```

### Environment Configuration

Copy `.env.example` to `.env` and fill in your own values:
```env
DB_DATABASE=forklift_booking
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...

GOOGLE_MAPS_API_KEY=your_google_maps_key

MAIL_MAILER=smtp
MAIL_HOST=your_mail_host
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
```

### Database Setup
```bash
php artisan migrate
php artisan db:seed
```

### Run Locally
```bash
php artisan serve
```

Visit `http://localhost:8000`

---

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

---

## 👋 About the Developer

Built by a Full Stack / Laravel / PHP developer based in **Regina, Saskatchewan** 🇨🇦 — open to local and remote opportunities.
