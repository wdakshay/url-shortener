## Local Setup Instructions

Follow these steps to set up the project locally for testing:

1. **Clone the repository**:

    ```bash
    git clone https://github.com/wdakshay/url-shortener.git
    ```

2. **Navigate to the folder**:

    ```bash
    cd url-shortener
    ```

3. **Create the .env file**:
    - Copy `.env.example` to `.env`:
        ```bash
        cp .env.example .env
        ```

4. **Install PHP dependencies**:

    ```bash
    composer install
    ```

5. **Configure the Database**:
    - Open `.env` and configure your **MySQL** database settings:
        ```env
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=your_database_name
        DB_USERNAME=your_username
        DB_PASSWORD=your_password
        ```

6. **Generate Application Key**:

    ```bash
    php artisan key:generate
    ```

7. **Run Migrations and Seed**:
    ```bash
    php artisan migrate --seed
    ```

### Default Credentials

- **SuperAdmin**:
    - Email: `superadmin@example.com`
    - Password: `Admin@123`

## Functional Requirements Summary

- **Authentication**: SuperAdmin, Admin, Member roles.
- **Invitations**:
    - SuperAdmin invites Admins to companies.
    - Admins invite Admins/Members to their own company.
- **Access Control**:
    - SuperAdmin: View all URLs, cannot create URLs.
    - Admin: View company URLs, can create URLs.
    - Member: View own URLs, can create URLs.
- **Public Access**: Short URLs are publicly resolvable.
