# 🏥 Health Manager

[![en](https://img.shields.io/badge/lang-en-red.svg)](https://github.com/formatocd/health-manager/blob/main/README.md)
[![es](https://img.shields.io/badge/lang-es-yellow.svg)](https://github.com/formatocd/health-manager/blob/main/README.es.md)

![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white) ![PHP](https://img.shields.io/badge/PHP-8.4+-777BB4?style=for-the-badge&logo=php&logoColor=white) ![Livewire](https://img.shields.io/badge/Livewire-3.x-4E56A6?style=for-the-badge&logo=livewire&logoColor=white) ![TailwindCSS](https://img.shields.io/badge/Tailwind-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

**Health Manager** is a platform that allows users to keep track of medical appointments, weight control, sports activities, and blood pressure, with the ability to safely share this data with other users (family members, doctors, or caregivers).

## ✨ Main Features

* **📅 Interactive Calendar:** Visual management of medical appointments and health events.
* **⚖️ Health Tracking:** Weight logging and evolution charts.
* **📂 Medical History:** Digital storage for reports and documents.
* **🤝 Data Sharing:** System to share data with other users ("Read-Only" mode).
* **🚀 Self-Installable:** "Zero-Touch" deployment system.

## 🛠️ System Requirements

* PHP 8.4 or higher.
* PHP extensions required by Laravel (Ctype, cURL, DOM, Fileinfo, Filter, Hash, Mbstring, OpenSSL, PCRE, PDO, Session, Tokenizer, XML).
* [Laravel compatible database](https://laravel.com/docs/13.x/database) (MySQL, MariaDB, PostgreSQL, SQLite, etc).

## 🐳 Docker Image

If you prefer not to install the project manually, we have an official Docker image ready to use. You can find all the documentation regarding its configuration and usage on our Docker Hub repository:

👉 [formatocd/health-manager on Docker Hub](https://hub.docker.com/repository/docker/formatocd/health-manager/general)

## 📦 Installation

For either of the two installation methods, the following tools are required:
* **Git**
* **Composer**
* **NPM** (Node.js)

Choose the method that fits your environment:

### 🅰️ Option A: Console Hosting (VPS / SSH)
These operations are performed directly connected to your server's console.

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/formatocd/health-manager.git
    cd health-manager
    ```

2.  **Install dependencies and build assets:**
    ```bash
    composer install --no-dev --optimize-autoloader
    npm install && npm run build
    ```

3.  **Initial setup:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Note: There is no need to run migrations; the system will automatically run them upon accessing the web.*

### 🅱️ Option B: Local Preparation for Shared Hosting (No Console / FTP)
These operations are performed on your personal computer before uploading the files.

1.  **Clone the repository on your machine:**
    ```bash
    git clone https://github.com/formatocd/health-manager.git
    cd health-manager
    ```

2.  **Install dependencies and build assets:**
    ```bash
    composer install --no-dev --optimize-autoloader
    npm install && npm run build
    ```

3.  **Initial setup:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **File upload:**
    Upload **all** project files and folders (including `vendor` and `public/build`) to your hosting via FTP or File Manager.

## ⚙️ Environment Configuration

Once you have the files on the server (whether via Option A or B), you must configure the **`.env`** file and the web server.

### 1. Edit the `.env` file
Make sure to configure the following variables with your hosting data:

* **Application (Production):**
    ```env
    APP_ENV=production
    APP_DEBUG=false
    APP_URL=https://yourdomain.com
    ```
* **Database:**
    ```env
    DB_CONNECTION=mariadb
    DB_HOST=your.db.host
    DB_PORT=3306
    DB_DATABASE=your_db_name
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    ```
* **Mail Server (SMTP):**
    Required for sending notifications and password recovery.
    ```env
    MAIL_MAILER=smtp
    MAIL_HOST=smtp.yourserver.com
    MAIL_PORT=587
    MAIL_USERNAME=your_email@yourdomain.com
    MAIL_PASSWORD=your_email_password
    MAIL_ENCRYPTION=tls
    MAIL_FROM_ADDRESS="no-reply@yourdomain.com"
    MAIL_FROM_NAME="${APP_NAME}"
    ```

### 2. Configure Public Folder
⚠️ **Important:** You must configure your web server or hosting so that the domain points to the **/public** folder of the project, not the root.

## 📖 Usage Instructions

### 1. First Access (Administrator Creation)
The system features a **"First User Takeover"** policy:
* When accessing the web for the first time, the system will detect the empty database and **automatically create the tables**.
* It will force-redirect you to registration.
* **The first registered user** will become the **ADMINISTRATOR** of the application.

### 2. Daily Management
* **Calendar:** Click on any day to view the details of its records. To add a record, click the `+` button in the bottom right corner and select the record type.
* **Profile and Nick:** Set up your `username` (Nick) in your profile so others can find you.
* **Share Data:** Go to `Profile` -> `Share Data` and enter the Nick of an existing user to grant them read access.

## 📄 License
This project is open-sourced software licensed under the [MIT](https://opensource.org/licenses/MIT) license.
