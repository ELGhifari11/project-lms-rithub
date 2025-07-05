# Project LMS Rithub Batch 1

This document provides instructions on how to set up and run the project locally.

## Prerequisites

Before you begin, ensure you have the following installed:

-   A GitHub Account
-   A Terminal (Powershell, CMD, Laragon Terminal, etc.)
-   Git
-   Node.js & NPM
-   PHP
-   Composer

## Repository Link

You can find the project's GitHub repository here:
[https://github.com/ELGhifari11/project-lms-rithub-batch-1](https://github.com/ELGhifari11/project-lms-rithub-batch-1)

---

## Step-by-Step Guide

### A. Initial Setup

1.  **Open the GitHub Repository**
    -   Navigate to the repository using the link provided above.

2.  **Clone the Repository**
    -   Clone the repository using the HTTPS link.
    -   If you are using Laragon, make sure to clone the project into `C:\laragon\www`.
    ```bash
    git clone https://github.com/ELGhifari11/project-lms-rithub-batch-1.git
    ```

3.  **Navigate to the Project Directory**
    -   Change your current directory to the newly cloned project folder.
    ```bash
    cd project-lms-rithub-batch-1
    ```

4.  **Create the `.env` File**
    -   Copy the example environment file to create your own configuration file.
    ```bash
    copy .env.example .env
    ```

5.  **Configure the Database**
    -   In the `.env` file, comment out all database configurations except for `DB_CONNECTION`.
    -   Change `DB_CONNECTION=mysql` to `DB_CONNECTION=sqlite`.
    -   Ensure `CACHE_STORE` is set to `file`.

6.  **Install Composer Dependencies**
    -   Run the following command in your terminal:
    ```bash
    composer install
    ```

7.  **Generate Application Key**
    -   Run the following command:
    ```bash
    php artisan key:generate
    ```

8.  **Run Migrations and Seed the Database**
    -   This will set up your database schema and populate it with initial data.
    ```bash
    php artisan migrate --seed
    ```

9.  **Generate Shield Resources**
    -   Generate all necessary Filament Shield policies and resources.
    ```bash
    php artisan shield:generate --all
    ```

10. **Assign Super Admin Role**
    -   Run the command and select user ID `1` when prompted.
    ```bash
    php artisan shield:super-admin
    ```

11. **Build Frontend Assets**
    -   Compile the frontend assets using NPM.
    ```bash
    npm run build
    ```

12. **Run the Project**
    -   Ensure Laragon is running.
    -   Open your browser and navigate to `project-lms-rithub-batch-1.test`.

13. **Update Environment Configuration**
    -   In your `.env` file, set the `APP_URL` and `APP_DOMAIN` to your local development domain.
    ```
    APP_URL=http://project-lms-rithub-batch-1.test
    APP_DOMAIN=project-lms-rithub-batch-1.test
    ```

### B. Beekeeper Studio Configuration

1.  **Select Connection Type**
    -   Choose `SQLite` as the connection type.

2.  **Choose Database File**
    -   Select the `database.sqlite` file located in the `database` folder of the project.

3.  **Connect**
    -   Click the "Connect" button to open the database.

### C. Push to Your Personal GitHub Repository

1.  **Remove the Original Remote**
    -   This disconnects the project from the original repository.
    ```bash
    git remote remove origin
    ```

2.  **Create a New Repository**
    -   Create a new, empty repository on your personal GitHub account.
    -   Add your new repository as a remote for your local project. Replace `nama_remote` with a name of your choice (e.g., `origin`) and `git_url` with your new repository's URL.
    ```bash
    git remote add <nama_remote> <git_url>
    ```

3.  **Stage Your Files**
    -   Add all files to be committed.
    ```bash
    git add .
    ```

4.  **Push to Your Repository**
    -   Push your code to your new remote repository.
    ```bash
    git push -u <nama_remote> main
    ```
