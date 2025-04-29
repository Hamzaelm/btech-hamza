# BTECH-HAMZA

## Installation

### Backend Setup
### Install PHP dependencies:
composer install
## Set up the environment variables: Copy .env.example to .env and configure the database settings and JWT secret key:
cp .env.example .env
### Create and update the database:
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
### Set up JWT secret:
php bin/console lexik:jwt:generate-keypair
### Run the Symfony server:
symfony serve

### Frontend Setup
npm install
npm run serve

### Testing
## BackEnd
php bin/phpunit
## FrontEnd
npm run test:unit
