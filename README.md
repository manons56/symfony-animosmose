# Animosmose — Installation Guide (Symfony + MAMP/WAMP)

Welcome to the **Animosmose** project, an application developed with the **Symfony** framework.
This guide will walk you step by step through the installation and configuration of the project on your local environment using **MAMP (macOS)** or **WAMP (Windows)**.

---

## Prerequisites

### Before starting

Make sure you have the following installed on your machine:

* **PHP ≥ 8.2** (recommended: 8.3 — included with MAMP)
* **Composer** — PHP dependency manager
* **Node.js** and **npm** — for front-end assets
* **Git** — to clone the repository
* **MAMP** — used only for the **MySQL database**
* *(optional)* **FileZilla** — for deployment

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

1. Make sure all dependencies are installed:
   ```bash
   composer install
   npm install
   ```
   
2. Launch MAMP to start the MySQL database server.

💡 By default, MySQL runs on port 8889 in MAMP.
You can check or change this in MAMP → Preferences → Ports.

3. Start the Symfony built-in web server:
    ```bash
    symfony serve
    ```

4. Once started, the terminal will display a local URL, for example:

    Web server listening on http://127.0.0.1:8005


5. Open this URL in your browser to access the application:

    http://127.0.0.1:8005/home

    
    💡 If the port 8005 is already in use, Symfony may automatically pick another one (e.g. 8000).
    You can manually specify a port with:
    ```bash
    symfony serve --port=8005
    ```
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
DATABASE_URL="mysql://root:root@localhost/symfony-animosmose?unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock&serverVersion=5.7&charset=utf8mb4"
```

* **WAMP Windows**:

```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/symfony-animosmose"
```

> By default:
>
> * MAMP macOS: user `root`, password `root`, MySQL port `8889`
> * WAMP Windows: user `root`, no password, MySQL port `3306`

---

### 4. Database configuration

1. Open **phpMyAdmin** to verify that MySQL is running:
   * **MAMP (macOS)** → [http://localhost:8888/phpMyAdmin](http://localhost:8888/phpMyAdmin)
   * **WAMP (Windows)** → [http://localhost/phpmyadmin](http://localhost/phpmyadmin)

2. Create a new empty database named: symfony-animosmose



3. In your terminal, generate the database and its schema using Doctrine:
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

💡 If no SQL file is provided, these commands will automatically create all the necessary tables from your Doctrine entities and migration files.

### 5. Install PHP dependencies

```bash
composer install
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

Start the Symfony built-in web server:

```bash
symfony serve
```

Once the server is running, open the URL displayed in the terminal.
For example:

http://127.0.0.1:8005/home

💡 If the port 8005 is already in use, Symfony may automatically choose another one (e.g. 8000).
You can manually specify it with:

```bash
symfony serve --port=8005
```
Make sur MAMP is running in the background to provide the MySQL database connection.

---


## Troubleshooting (Common issues)

|           Problem          |           Possible cause             |               Solution               |
|----------------------------|--------------------------------------|--------------------------------------|
| **500 error / blank page** | Incorrect `.env.local` configuration | Check your database credentials and environment variables.                                                                         Make sure `.env.local` matches your local setup. |
| **Database connection error** | Wrong credentials or MySQL port | Verify the MySQL port in **MAMP → Preferences → Ports** (default: `8889`) and update `.env.local` if needed. |
| **Composer error** | Outdated Composer version | Run `composer self-update`, then retry `composer install`. |
| **npm error** | Corrupted or missing `node_modules` | Delete the folder and reinstall dependencies: `rm -rf node_modules && npm install`. |
| **Port already in use** | Another local server is running on the same port | Stop other servers (MAMP, PHP, etc.) or start Symfony on a different port: `symfony serve --port=8005`. |
| **.env.local not loaded** | File missing or misnamed | Ensure you created the `.env.local` file (copied from `.env`) and that it’s located at the project root. |


---

## Deployment (FTP / Remote hosting)

### 1. Prepare the project for production

```bash
composer install --no-dev --optimize-autoloader
npm run build
php bin/console cache:clear --env=prod
```

2. Transfer files via FTP
Open FileZilla and connect to your FTP server.

Upload all project files except:

- /node_modules

- /vendor (can be reinstalled on the server via Composer if available)

Check file permissions:

- Folders → 755

- Files → 644

Ensure that the DocumentRoot (or “root directory” on your hosting) points to the /public folder.

💡 Example: if your domain is https://www.animosmose.com,
your server should serve files from /public, not the project root.

Quick summary
| Step | Command / Action |
|------|------------------|
| **Clone the project** | `git clone <repository-url>` |
| **Install PHP dependencies** | `composer install` |
| **Install JS dependencies** | `npm install && npm run build` |
| **Configure the database** | `.env.local` + `php bin/console doctrine:migrations:migrate` |
| **Run the local server** | `symfony serve` (check the URL in your terminal) |

Useful tips
- Clear cache:
  
```bash
php bin/console cache:clear
```


- Create a new Doctrine entity:

```bash
php bin/console make:entity
```

- Start Symfony’s internal server (alternative to MAMP/WAMP):

```bash
symfony serve
```
---

**Author:** Animosmose Team
**Version:** 1.0
**Framework:** Symfony 7.x
