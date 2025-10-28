## Installation

### 1. Clone the repository
git clone https://github.com/your-username/your-project.git
cd your-project


### 2. Install dependencies
Symfony uses Composer to manage dependencies:
composer install


### 3. Configure the environment
Copy the example .env file:
cp .env .env.local

Edit .env.local to set your environment variables (database, secret key, etc.):
DATABASE_URL="mysql://user:password@127.0.0.1:3306/database_name"
APP_ENV=dev
APP_SECRET=your_secret_key


### 4. Create the database and run migrations

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate


### 5. Start the development server
symfony serve

Or with PHP:
php -S 127.0.0.1:8000 -t public


### 6. Check that everything works
Open in your browser: http://127.0.0.1:8000

You should see the home page of your Symfony project.
