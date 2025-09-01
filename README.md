# Canteen_System-
Draft 1

HELLOO! Here's what you need to do to get our Canteen_System project running on your local machine after pulling from the repo.
What You'll Need First
System Requirements:
•	PHP 8.2 or higher (this is mandatory - our Laravel 12 won't work with older versions)
•	Composer (for PHP dependencies)
•	Node.js and npm (for our frontend stuff)
•	A database (MySQL, PostgreSQL, or SQLite)
Step-by-Step Setup
1.	Clone our repository (if you haven't already):
bash
git clone https://github.com/systeamcls/Canteen_System-.git
cd Canteen_System-
2.	Install PHP dependencies:
bash
composer install
3.	Install frontend dependencies:
bash
npm install
4.	Set up your environment:
bash
# Copy the environment example file
cp .env.example .env

# Generate a unique application key
php artisan key:generate
5.	Configure your database in the .env file:
o	Update DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
6.	Run database migrations:
bash
php artisan migrate
7.	Build the frontend assets:
bash
# For development (keeps watching for changes)
npm run dev
8.	Start the application:
bash
# Start our Laravel server
php artisan serve

