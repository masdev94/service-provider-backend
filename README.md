# Service Provider Directory

A high-performance Laravel 11 backend application that powers a Service Provider Directory web module. This application allows users to browse and filter service providers by category and view detailed information about each provider.

## Features

- Display a comprehensive list of service providers with key information
- Filter providers by category with efficient database queries 
- View detailed provider information on dedicated pages
- Performance-optimized with a focus on preventing N+1 query issues

## Tech Stack

- **Backend**: Laravel 11 with Eloquent ORM
- **Testing**: PHPUnit
- **Database**: MySQL/SQLite (configurable)

## Prerequisites

- PHP 8.2 or higher
- Composer
- SQLite or MySQL database
- Node.js & NPM (for frontend development)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/service-provider-be.git
   cd service-provider-be
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Create and configure your environment file:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure your database in the `.env` file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=service_provider_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Add Some .png / .jpg images in Storage/app/public/seed-logos

6. Run the migrations and seed the database:
   ```bash
   php artisan migrate --seed
   ```

7. Create a symbolic link for storage:
   ```bash
   php artisan storage:link
   ```

8. Start the development server:
   ```bash
   php artisan serve
   ```

## API Endpoints

| Method | Endpoint                  | Description                                  |
|--------|---------------------------|----------------------------------------------|
| GET    | `/api/providers`          | List all providers (with optional filtering) |
| GET    | `/api/providers/{slug}`   | Get detailed information about a provider    |
| GET    | `/api/categories`         | List all available categories                |

### Query Parameters for `/api/providers`

- `category`: Filter by category name
- `category_id`: Filter by category ID  
- `per_page`: Customize the number of results per page (default: 12)

## Database Structure

The application uses two main models:

- **Category**: Represents a service provider category
  - Fields: id, name, slug, timestamps

- **ServiceProvider**: Represents a service provider
  - Fields: id, name, slug, short_description, description, logo, category_id, timestamps
  - Relationships: Belongs to a Category

## Performance Optimizations

This application implements several performance optimizations:

- **Eager Loading**: Prevents N+1 query issues by loading related data efficiently
- **Selective Column Selection**: Only retrieves necessary columns from the database
- **Pagination**: Limits data transfer size by paginating results
- **Query Optimization**: Uses optimized database queries for filtering

## Testing

Run the test suite using:

```bash
php artisan test
```

The test suite includes comprehensive tests for:
- Provider listing functionality
- Category filtering
- Pagination
- N+1 query prevention
- Detailed provider information retrieval

## Future Enhancements

- Full-text search capabilities
- Advanced filtering options
- User authentication and provider favoriting
- Admin panel for managing providers and categories

## License

This project is licensed under the MIT License - see the LICENSE file for details.
