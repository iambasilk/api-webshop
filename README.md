# API Webshop

Simplified mini webshop consisting of customers, products and orders and their endpoints.

## Clone the repository:

```bash
git clone https://github.com/iambasilk/api-webshop.git
```

## Install dependencies:

```
cd <repo-name>
composer install
```

## Generate an app key:

```
php artisan key:generate

```

## Set up env values in the .env file

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=<database_name>
DB_USERNAME=<database_username>
DB_PASSWORD=<database_password>
LOOP_MASTER_DATA_URL=https://backend-developer.view.agentur-loop.com/
LOOP_MASTER_DATA_USERNAME=<user_name>
LOOP_MASTER_DATA_PASSWORD=<password>
```

## Run the database migrations:

```
php artisan migrate
```

## Start the server:

```
php artisan serve
```

## Test the API endpoints using a Postman or Hoppscotch. Here are the available endpoints:

Import `storage\app\postman_collections\API_Web_shop.postman_collection.json` to postman and test the endpoints
