# Velvue - Laravel API + Vue SPA SaaS Boilerplate

[![](https://img.shields.io/badge/Laravel-11-red)](https://laravel.com)
[![](https://img.shields.io/badge/Vue.js-3-green)](https://nuxt.com)

Saas template for development on Laravel + Vue.js with ready-made authorization (email password / social), Saas dashboard, auth & account pages, image upload with optimization, stripe payments.

You can find frontend - Vue SPA in fe_velvue folder and backend - Laravel API in be_velvue folder.

## Features

### Backend API

- [**Laravel 11**](https://laravel.com/docs/11.x)
- [**Laravel Telescope**](https://laravel.com/docs/11.x/telescope) provides insight into the requests coming into your application, exceptions, log entries, database queries, queued jobs, mail, notifications, cache operations, scheduled tasks, variable dumps, and more.
- [**Laravel Sanctum with Session Cookies**](https://laravel.com/docs/11.x/sanctum) API authentication using same-site session cookies. Considered safer than api tokens.
- [**Laravel Socialite**](https://laravel.com/docs/11.x/socialite) OAuth providers
- [**Laravel Cashier**](https://laravel.com/docs/11.x/billing) Stripe payments
- Activity logs - loggin user actions for audit
- Notifications - sending emails
- Api rate limiting

### Frontend SPA

- [**Vue.js 3**](https://v3.vuejs.org/) The most favored front-end framework for web development by Laravel developers
- [**PrimeVue**](https://primevue.org/) #1 Vue devs loved Next Gen UI library with Tailwind support + modified [**Sakai**](https://github.com/primefaces/sakai-vue) theme
- [**Pinia**](https://pinia.vuejs.org/ssr/nuxt.html) The intuitive store for Vue.js
- [**Vue Router**](https://router.vuejs.org/) The official router for Vue.js
- Authentication: Pre-built pages for login, registration, email confirmation, password recovery, and account management.
- Image Uploads: Drag-and-drop uploads with cropping and optimization.
- Dynamic Forms: Includes reusable form components with validation.
- Dashboard: A basic dashboard template with analytics widgets.
- Responsive Design: Fully responsive and mobile-friendly layout.

## Installation

Ensure you have the following installed:

- [PHP 8.2+](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/)
- [Redis](https://redis.io/)
- [MySQL](https://www.mysql.com/)

For macOS users, you can install these using [Homebrew](https://brew.sh/).

```bash
brew install php node redis mysql
```

## Setup

1. Fork the repository - click the "Fork" button in the top right corner and select your GitHub account.
2. Clone the forked repository to your local machine:

```bash
git clone <your-forked-repo-url>
```

3. Link the original repository to your fork:

```bash
git remote add upstream https://github.com/DreamUpstream/Velvue-Laravel-Vue-SaaS-Starter.git
```

### Backend Setup

1. Install PHP dependencies and set up the database:

```bash
cd velvue/be_velvue && composer install && cp .env.example .env && php artisan key:generate && php artisan migrate --seed && php artisan storage:link
```

2. Update the `.env` file with your database credentials and Stripe keys.
3. Start the Laravel server:

```bash
php artisan serve
```

### Frontend Setup

1. Install Node.js dependencies:

```bash
cd ../fe_velvue && npm install
```

2. Update the `.env` file with the API URL:

```bash
cp .env.example .env
```

```bash
VUE_APP_API_URL=http://localhost:8000
```

3. Start the Vue.js server:

```bash
npm run dev
```

### Updating fork

I created update-fork.sh script to update your fork with the latest changes from the original repository. You can run it with the following command:

```bash
./update-fork.sh
```

<!-- Contributing -->

## Contributing

Contributions are what make the open-source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request
6. Wait for the me to review your PR
