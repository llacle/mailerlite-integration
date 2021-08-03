# MailerLite Subscriber Manager

A small web application to manage MailerLite subscribers.

## Features

- Manage API Key
- Add/edit & delete subscribers
- Search subscribers

## Installation

**1**: Get the code:
```sh
git clone https://github.com/llacle/mailerlite-integration.git mailerlite_app
```
or download and extract: https://github.com/llacle/mailerlite-integration/archive/refs/heads/main.zip

**2**: Use Composer to install dependencies
```sh
cd /path/to/mailerlite_app
composer install
```

**3**: Install additiona dependencies
```sh
npm install
```

To compile css and js assets run the following command:
```sh
npm run dev
```

**4**: Copy .env.example to .env and edit the file to configure your database settings
```sh
php -r "copy('.env.example', '.env');"
php artisan key:generate
```

**5**: Complete your database setup. Import the SQL file (mailerlite_app_dump.sql)
**6**: Execute Artisan serve to access the web app
```sh
php artisan serve
```

## Additional configuration for Tests
Edit phpunit.xml and set the value for MAILERLITE_KEY with your API Key
