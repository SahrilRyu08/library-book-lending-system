# Sistem Peminjaman Buku Perpustakaan

## Tech Stack

- Laravel 12
- Bootstrap 5
- MySQL

## Setup

composer install

cp .env.example .env

change in file .env QUEUE_CONNECTION=database => QUEUE_CONNECTION=sync

php artisan key:generate

php artisan migrate:fresh --seed

php artisan storage:link

php artisan serve



## Login

Admin

admin@moco.app
password

Member

sahril@moco.app
password

zhavira@moco.app
password

david@moco.app
password

adid@moco.app
password


