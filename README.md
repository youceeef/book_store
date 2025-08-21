# Book Store

This is a Laravel-based web application for an online book store. It provides a platform for users to browse, purchase books, and for administrators to manage the store's inventory, orders, and promotions.

## Features

### User Features

-   **Browse Books:** View a list of all available books with details like title, author, price, and description.
-   **Filter by Category:** Filter books based on their category.
-   **Shopping Cart:** Add books to a shopping cart, update quantities, or remove them.
-   **Coupon System:** Apply discount coupons to the cart.
-   **Secure Checkout:** A seamless and secure checkout process powered by Stripe.
-   **Order History:** View a history of all past orders.
-   **User Profile Management:** Update personal information and password.

### Admin Features

-   **Dashboard:** An overview of the store's performance, including total sales, orders, and new customers.
-   **Book Management:** Full CRUD (Create, Read, Update, Delete) functionality for books.
-   **Category Management:** Manage book categories.
-   **Order Management:** View and manage all customer orders.
-   **Coupon Management:** Create, edit, and delete discount coupons.

## Built With

-   [Laravel](https://laravel.com/) - The web application framework used.
-   [PHP](https://www.php.net/) - The core programming language.
-   [MySQL](https://www.mysql.com/) - The database used for storing data.
-   [Stripe](https://stripe.com/) - For processing payments.
-   [Tailwind CSS](https://tailwindcss.com/) - For styling the user interface.
-   [Vite](https://vitejs.dev/) - For the frontend build process.

## Installation

To get a local copy up and running, follow these simple steps.

### Prerequisites

-   PHP >= 8.1
-   Composer
-   Node.js & npm
-   A database (e.g., MySQL)

### Steps

1. **Clone the repository:**

    ```sh
    git clone https://github.com/youceeef/book_store.git
    cd book_store
    ```

2. **Install PHP dependencies:**

    ```sh
    composer install
    ```

3. **Install NPM dependencies:**

    ```sh
    npm install
    ```

4. **Set up your environment:**

    - Copy the `.env.example` file to `.env`.
    - Generate an application key:
        ```sh
        php artisan key:generate
        ```
    - Configure your database and Stripe credentials in the `.env` file.

5. **Run database migrations:**

    ```sh
    php artisan migrate
    ```

6. **Build frontend assets:**

    ```sh
    npm run dev
    ```

7. **Start the development server:**
    ```sh
    php artisan serve
    ```

## Usage

After installation, you can access the application in your web browser at the address provided by `php artisan serve` (usually `http://127.0.0.1:8000`).

-   **Admin Access:** To access the admin panel, you will need to set a user's `is_admin` flag to `1` in the `users` table. The admin panel is available at `/admin/dashboard`.

## Testing

To run the test suite for the application, execute the following command:

```sh
php artisan test
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
