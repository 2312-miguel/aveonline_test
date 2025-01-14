# AveOnline Test Project

## Description
A comprehensive test project for AveOnline, implementing user management, transaction handling, and balance tracking within a banking system.

## Technologies
- **Backend**: PHP 8.1, Laravel 11.x
- **Database**: MySQL 8.0
- **Frontend**: Tailwind CSS
- **Containerization**: Docker & Docker Compose

## Application Access
Once the installation is complete, you can access the application at:
- **URL:** http://localhost:8000/login
- **Default Admin User:** admin@example.com
- **Default Password:** password123

---

## Installation Options
### Using Docker (Recommended)

#### Prerequisites
- Docker Desktop
- Docker Compose
- Git

#### Steps

1. Clone the repository:
   ```bash
   git clone https://github.com/2312-miguel/aveonline_test.git
   cd aveonline_test
   ```

2. Build and start containers:
   ```bash
   docker-compose up -d --build
   ```

3. Configure the environment:
   ```bash
   cp .env.example .env
   docker exec laravel_app php artisan key:generate
   ```

4. Run database migrations:
   ```bash
   docker exec laravel_app php artisan migrate
   ```
5. Run database seeders:
   ```bash
   docker exec laravel_app php artisan db:seed
   ```

6. Access the application:
   Open your browser and navigate to http://localhost:8000/login

#### Docker Services
- **laravel_app**: Laravel application running on PHP 8.1
- **mysql_db**: MySQL 8.0 database

#### Docker Commands
- Start services:
  ```bash
  docker-compose up -d
  ```
- Stop services:
  ```bash
  docker-compose down
  ```
- View logs:
  ```bash
  docker-compose logs -f
  ```
- Run Laravel commands:
  ```bash
  docker exec laravel_app php artisan {command}
  ```

#### Run Test

  ```bash
  docker exec laravel_app php artisan test
  ```

#### Example `.env` Configuration
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=root
```

---

### Local Installation

#### Prerequisites
- Apache
- PHP >= 8.1
- Composer
- MySQL

#### Steps

1. Clone the repository:
   ```bash
   git clone https://github.com/2312-miguel/aveonline_test.git
   cd aveonline_test
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install
   ```

3. Configure the environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Set up the database:
   ```bash
   php artisan migrate
   ```

5. Run database seeders:
```bash
php artisan db:seed
```

#### Test
6. Run tests:
   ```bash
   php artisan test
   ```

---

## API Endpoints

### Authentication
- **POST** `/api/login`
  - Description: User login.
  - Request Body:
    ```json
    {
      "email": "user@example.com",
      "password": "user_password"
    }
    ```
  - Response:
    ```json
    {
      "message": "",
      "token": "auth_token"
    }
    ```

## Protected Routes with Token Check

> **Note:** All routes in this section require the `X-Security-Token` header with the token generated from the login.

### Transactions
- **POST** `/api/transactions`
  - Description: Create a new transaction (deposit/withdrawal).
  - Request Body:
    ```json
    {
      "type": "deposit",
      "amount": 100.0,
      "userId": 1
    }
    ```
  - Response:
    ```json
    {
      "transactionId": 123,
      "status": "success"
    }
    ```

- **GET** `/api/transactions/{transactionNumber}`
  - Description: Retrieve transaction details by number.
  - Response:
    ```json
    {
      "transactionId": 123,
      "type": "deposit",
      "amount": 100.0,
      "status": "success",
      "userId": 1
    }
    ```

### User Balance
- **GET** `/api/users/{userId}/balance`
  - Description: Get the balance of a specific user.
  - Response:
    ```json
    {
      "userId": 1,
      "balance": 1000.0
    }
    ```

- **POST** `/api/users/{userId}/balance`
  - Description: Add funds to a user's account.
  - Request Body:
    ```json
    {
      "amount": 100.0
    }
    ```
  - Response:
    ```json
    {
      "userId": 1,
      "newBalance": 1100.0
    }
    ```

- **GET** `/api/users/{userId}/balance-summary`
  - Description: Get balance summary
  - Response:
    ```json
        {
          "userId": 1,
          "totalDeposits": 5000.0,
          "totalWithdrawals": 4000.0,
          "currentBalance": 1000.0
        }
    ```

### Activity Logged Routes

- **GET** `/api/users/{userId}/details`
  - Description: Get user details
  - Response:
    ```json
        {
          "userId": 1,
          "name": "John Doe",
          "email": "john.doe@example.com",
          "createdAt": "2023-01-01T00:00:00Z"
        }
    ```

### Administrative
- **GET** `/api/logs/download`
  - Description: Download system logs as a file csv.

---

## Project Structure
```
aveonline_test/
├── app/
│   ├── Http/
│   ├── Models/
│   ├── Services/
│   └── Repositories/
├── database/
├── routes/
└── tests/
```

---

## Author
Miguel Simijaca

## License
This project is a technical assessment for AveOnline and is not intended for production use.
