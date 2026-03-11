# Operazioni comuni (local dev)

Setup base:
- composer install
- copy .env.example .env
- php artisan key:generate
- configura DB in .env
- php artisan migrate
- php artisan db:seed (opzionale)

Frontend:
- npm install
- npm run dev (sviluppo)
- npm run build (produzione)

Avvio:
- php artisan serve

Test:
- php artisan test
- vendor\bin\phpunit

Cache:
- php artisan cache:clear
- php artisan config:clear
- php artisan view:clear
