# Gestionale Freelance - Guida all'Installazione

Questo è un gestionale web completo in Laravel 11 per monitorare pagamenti e lavori di uno sviluppatore freelance.

## Stack Tecnologico

- **Backend**: Laravel 11
- **Frontend**: Bootstrap 5 con Material Design (mdb-ui-kit)
- **Icone**: Bootstrap Icons
- **Calendario**: FullCalendar
- **Database**: MySQL
- **Lingua**: Italiano

## Funzionalità Principali

✅ **Autenticazione**: Sistema completo di login/logout con Laravel Breeze
✅ **Gestione Clienti**: CRUD completo con ricerca e paginazione
✅ **Gestione Lavori**: CRUD completo con filtri per cliente, data e stato
✅ **Gestione Pagamenti**: CRUD completo con supporto per pagamenti one-shot e periodici
✅ **Calendario**: Vista mensile con lavori e pagamenti
✅ **Dashboard**: Statistiche e quick actions
✅ **Responsive**: Funziona su desktop, tablet e mobile

## Requisiti

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL >= 8.0

## Installazione

### 1. Clona il repository (o scarica il progetto)

```bash
cd c:\Users\user\Desktop\Progetti\jojo-gestionale
```

### 2. Installa le dipendenze PHP

```bash
composer install
```

### 3. Copia il file .env

```bash
copy .env.example .env
```

### 4. Configura il database nel file .env

Apri il file `.env` e configura la connessione al database:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jojo_gestionale
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Genera la chiave dell'applicazione

```bash
php artisan key:generate
```

### 6. Crea il database

Crea un database MySQL chiamato `jojo_gestionale` (o il nome che hai specificato nel .env)

### 7. Esegui le migration

```bash
php artisan migrate
```

### 8. Esegui i seeder per dati di esempio (opzionale)

```bash
php artisan db:seed
```

Questo creerà:
- 5 clienti di esempio
- 10 lavori di esempio
- 15 pagamenti di esempio
- 1 utente di test (email: test@example.com, password: password)

### 9. Installa le dipendenze Node.js

```bash
npm install
```

### 10. Compila gli asset

**Modalità sviluppo (con auto-reload):**
```bash
npm run dev
```

**Oppure per produzione:**
```bash
npm run build
```

### 11. Avvia il server Laravel

Apri un nuovo terminale e esegui:

```bash
php artisan serve
```

### 12. Accedi all'applicazione

Apri il browser e vai su: `http://localhost:8000`

## Credenziali di Accesso

Se hai eseguito i seeder, puoi accedere con:

- **Email**: test@example.com
- **Password**: password

Altrimenti, registra un nuovo utente dalla pagina di registrazione.

## Struttura del Progetto

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── ClienteController.php
│   │   ├── LavoroController.php
│   │   ├── PagamentoController.php
│   │   ├── CalendarioController.php
│   │   └── DashboardController.php
│   └── Requests/
│       ├── StoreClienteRequest.php
│       ├── UpdateClienteRequest.php
│       ├── StoreLavoroRequest.php
│       ├── UpdateLavoroRequest.php
│       ├── StorePagamentoRequest.php
│       └── UpdatePagamentoRequest.php
├── Models/
│   ├── Cliente.php
│   ├── Lavoro.php
│   └── Pagamento.php
resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php (Layout principale con sidebar)
│   ├── clienti/ (Views CRUD clienti)
│   ├── lavori/ (Views CRUD lavori)
│   ├── pagamenti/ (Views CRUD pagamenti)
│   ├── calendario/ (View calendario)
│   └── dashboard.blade.php
├── css/
│   └── app.css (Stili Bootstrap + Material Design)
└── js/
    └── app.js (JavaScript + FullCalendar)
```

## Caratteristiche Implementate

### Gestione Clienti
- ✅ Lista con ricerca e paginazione (15 elementi per pagina)
- ✅ Creazione, modifica, visualizzazione ed eliminazione
- ✅ Soft deletes
- ✅ Validazione form lato server

### Gestione Lavori
- ✅ Lista con filtri per cliente, data e stato
- ✅ Stati: da_fare, in_corso, completato
- ✅ Relazione con clienti
- ✅ Date con formato italiano

### Gestione Pagamenti
- ✅ Lista con filtri multipli
- ✅ Supporto pagamenti one-shot e periodici (mensile, trimestrale, annuale)
- ✅ Stati: in_sospeso, pagato, annullato
- ✅ Pulsanti CTA per cambiare stato
- ✅ Totali e statistiche
- ✅ Evidenziazione pagamenti scaduti

### Calendario
- ✅ Vista mensile con FullCalendar
- ✅ Eventi per lavori (blu) e pagamenti (colorati per stato)
- ✅ Click su data per vedere dettagli
- ✅ Modal con lista lavori e pagamenti del giorno

### Dashboard
- ✅ 4 card statistiche
- ✅ Lista prossimi lavori (7 giorni)
- ✅ Lista pagamenti in scadenza (30 giorni)
- ✅ Quick actions per creazione rapida

## Comandi Utili

### Cancellare il database e ricrearlo

```bash
php artisan migrate:fresh --seed
```

### Creare un nuovo utente manualmente

```bash
php artisan tinker
```

Poi nel tinker:
```php
User::create([
    'name' => 'Il Tuo Nome',
    'email' => 'tuaemail@example.com',
    'password' => bcrypt('tuapassword')
]);
```

### Pulire la cache

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Note Importanti

1. **Assicurati di avere Node.js installato** e di aver eseguito `npm install` e `npm run dev` (o `npm run build`)
2. **Il server Laravel deve essere in esecuzione** (`php artisan serve`)
3. **Soft Deletes**: I record eliminati non vengono cancellati fisicamente ma marcati come eliminati
4. **Validazione**: Tutti i form hanno validazione lato server
5. **Lingua**: L'applicazione è completamente in italiano

## Troubleshooting

### Errore "Class 'mdb' not found"
Assicurati di aver eseguito `npm install` e `npm run dev`

### Errore nel calendario
Verifica che FullCalendar sia installato: `npm list @fullcalendar/core`

### Errori CSS
Esegui `npm run build` e ricarica la pagina con Ctrl+F5

### Errore database
Verifica le credenziali nel file `.env` e che il database esista

## Licenza

Questo progetto è stato creato per scopi didattici.
