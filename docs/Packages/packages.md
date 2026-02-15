# Authentication & Teams (Jetstream with Livewire)
composer require laravel/jetstream
php artisan jetstream:install livewire --teams

# Roles & Permissions
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Media Library for file uploads
composer require spatie/laravel-medialibrary
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider"
php artisan migrate

# PDF Generation
composer require barryvdh/laravel-dompdf

# Calendar (FullCalendar via NPM)
npm install fullcalendar

# Charts
composer require laravel/chart
npm install chart.js

# SMS (Twilio)
composer require twilio/sdk

# Queue dashboard (optional, for monitoring)
composer require laravel/horizon
php artisan horizon:install

# Debug & Dev Tools
composer require --dev barryvdh/laravel-debugbar
composer require --dev laravel/telescope
php artisan telescope:install
php artisan migrate