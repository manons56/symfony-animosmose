# Animosmose — Installation Guide (Symfony + MAMP/WAMP)

Welcome to the **Animosmose** project, an application developed with the **Symfony** framework.
This guide will walk you step by step through the installation and configuration of the project on your local environment using **MAMP (macOS)** or **WAMP (Windows)**.

---

## Prerequisites

Before starting, make sure you have the following installed on your machine:

* **PHP ≥ 8.3** (included with MAMP/WAMP)
* **Composer** (PHP dependency manager)
* **MAMP** (macOS) or **WAMP** (Windows)
* **Node.js** and **npm**
* **Git** (to clone the repository)
* *(optional)* **FileZilla** (for FTP deployment)

---

## Installation

### 1. Clone the repository

Open a terminal and run the following command to clone the repository into your local server root directory:

* **macOS with MAMP**:

```bash
cd /Applications/MAMP/htdocs
git clone https://github.com/manons56/symfony-animosmose.git
```

* **Windows with WAMP**:

```bash
cd C:\wamp64\www
git clone https://github.com/manons56/symfony-animosmose.git
```

---

### 2. Local server configuration

1. Launch **MAMP** (macOS) or **WAMP** (Windows).
2. Ensure **Apache** and **MySQL** are running.
3. Note the **Apache port** (often `8888` on macOS MAMP, `80` on WAMP).
4. The application will be accessible at:

   * macOS MAMP: `http://localhost:8888/symfony-animosmose/public`
   * Windows WAMP: `http://localhost/symfony-animosmose/public`

---

### 3. Create the `.env.local` file

Symfony uses a `.env.local` file (not versioned) to store sensitive environment variables.

Create the file at the root of the project:

```bash
cp .env .env.local
```

Then update the database configuration according to your settings:

* **MAMP macOS**:

```env
DATABASE_URL="mysql://root:root@127.0.0.1:8889/bungalow_tropic"
```

* **WAMP Windows**:

```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/bungalow_tropic"
```

> By default:
>
> * MAMP macOS: user `root`, password `root`, MySQL port `8889`
> * WAMP Windows: user `root`, no password, MySQL port `3306`

---

### 4. Database configuration

1. Open **phpMyAdmin**:

   * MAMP macOS: [http://localhost:8888/phpMyAdmin](http://localhost:8888/phpMyAdmin)
   * WAMP Windows: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Create a new database (e.g., `bungalow_tropic`).
3. Import the provided SQL file (if available) or run:

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

---

### 5. Install PHP dependencies

```bash
composer install
composer update
```

---

### 6. Install JavaScript dependencies

```bash
npm install
npm run build
```

> In development mode: `npm run dev`

---

##  Running the project

* MAMP macOS: [http://localhost:8888/bungalow-tropicolor/public](http://localhost:8888/bungalow-tropicolor/public)
* WAMP Windows: [http://localhost/bungalow-tropicolor/public](http://localhost/bungalow-tropicolor/public)

---

##  Troubleshooting (Common issues)

| Problem                       | Possible cause                  | Solution                                     |
| ----------------------------- | ------------------------------- | -------------------------------------------- |
| **500 error / blank page**    | Incorrect `.env.local` config   | Check the database and environment variables |
| **Database connection error** | Wrong credentials or MySQL port | Check the credentials for MAMP/WAMP          |
| **Composer error**            | Outdated Composer               | `composer self-update`                       |
| **npm error**                 | Corrupted files                 | `rm -rf node_modules && npm install`         |

---

## Deployment (FTP / Remote hosting)

### 1. Prepare the project for production

```bash
composer install --no-dev --optimize-autoloader
npm run build
php bin/console cache:clear --env=prod
```

### 2. Transfer files via FTP

1. Open **FileZilla** and connect to your FTP server.
2. Transfer all project files (except `/node_modules` and `/vendor` if not needed).
3. Check permissions:

   * Folders → `755`
   * Files → `644`
4. Ensure the **DocumentRoot** points to the `/public` folder.

---

## Quick summary

| Step                     | Command / Action               |
| ------------------------ | ------------------------------ |
| Clone the project        | `git clone ...`                |
| Install PHP dependencies | `composer install`             |
| Install JS dependencies  | `npm install && npm run build` |
| Configure the database   | `.env.local` + migrations      |
| Run the server           | URL depending on MAMP/WAMP     |

---

## Useful tips

* Clear cache: `php bin/console cache:clear`
* Create a Doctrine entity: `php bin/console make:entity`
* Symfony internal server (alternative to MAMP/WAMP): `symfony serve`

---

**Author:** Animosmose Team
**Version:** 1.0
**Framework:** Symfony 7.x
