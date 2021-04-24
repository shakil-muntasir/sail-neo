<p align="center"><img src="/art/logo.svg" alt="Logo Laravel Sail"></p>

<p align="center">
    <a href="https://packagist.org/packages/laravel/sail">
        <img src="https://img.shields.io/packagist/dt/laravel/sail" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/laravel/sail">
        <img src="https://img.shields.io/packagist/v/laravel/sail" alt="Latest Stable Version">
    </a>
    <a href="https://packagist.org/packages/laravel/sail">
        <img src="https://img.shields.io/packagist/l/laravel/sail" alt="License">
    </a>
</p>

# Laravel Sail Neo

Advanced Laravel Sail for Laravel version 7/8 apps.

## Introduction

Sail provides a Docker powered local development experience for Laravel that is compatible with macOS, Windows (WSL2), and Linux. Other than Docker, no software or libraries are required to be installed on your local computer before using Sail. Sail's simple CLI means you can start building your Laravel application without any previous Docker experience.

## Installation

Use the package manager [composer](https://getcomposer.org/) to install package.

```bash
composer require ronvolt/laravel-sail-neo --dev
```

## Sail Neo commands and options
`php artisan sail:install --help` for help
```php
php artisan sail:install --php=7.4 or 8.0 --mysql=5.7 or 8.0
php artisan sail:install --php=7.4 or 8.0 --with=service(s) --mysql=5.7 or 8.0 
```
***Laravel Sail Neo Supports PHP version 7.4 or 8.0 only.***

***Laravel Sail Neo Supports MySQL version 5.7 or 8.0 only.***

---
**NOTE**

If `--php` is not specified or wrong version provided, Sail Neo will use PHP 8.0 by default.

If specified, `--with` can receive one or more values (comma seperated). E.g. `--with=mysql,redis`

***Only use `--mysql` if MySQL is added as a service.***

If `--mysql` is not specified or wrong version provided, Sail Neo will use MySQL 8.0 by default.

---


## Laravel Sail Official Commands
*Documentation for Sail can be found on the [Laravel website](https://laravel.com/docs/sail).*
```php
php artisan sail:install
php artisan sail:publish

alias sail='bash vendor/bin/sail'

sail up
sail up -d
sail down

sail php --version
sail artisan tinker
sail composer require
sail npm run dev

sail share // ngrok tunnel url

sail build --no-cache
```

#### Inspiration

Laravel Sail is inspired by and derived from [Vessel](https://github.com/shipping-docker/vessel) by [Chris Fidao](https://github.com/fideloper). If you're looking for a thorough introduction to Docker, check out Chris' course: [Shipping Docker](https://serversforhackers.com/shipping-docker).

## Official Documentation

Documentation for Sail can be found on the [Laravel website](https://laravel.com/docs/sail).

## Contributing

Thank you for considering contributing to Sail! You can read the contribution guide [here](.github/CONTRIBUTING.md).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

Please review [our security policy](https://github.com/laravel/sail/security/policy) on how to report security vulnerabilities.

## License

Laravel Sail is open-sourced software licensed under the [MIT license](LICENSE.md).
