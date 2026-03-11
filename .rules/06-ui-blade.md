# UI e Blade conventions

Principi UI:
- Tutti i testi e messaggi in italiano
- Bootstrap 5 + mdb-ui-kit + Bootstrap Icons
- Layout principale: resources/views/layouts/app.blade.php
- Sidebar + topbar coerenti in tutte le view
- Paginazione standard: 15 elementi per pagina

Naming view (convenzioni attuali):
- Dashboard: resources/views/dashboard.blade.php
- Clienti: resources/views/clienti/index.blade.php, create.blade.php, edit.blade.php, show.blade.php
- Lavori: resources/views/lavori/index.blade.php, create.blade.php, edit.blade.php, show.blade.php
- Pagamenti (CRUD): resources/views/pagamenti/index.blade.php, create.blade.php, edit.blade.php, show.blade.php
- Pagamenti unici: resources/views/pagamenti/unici/index.blade.php
- Pagamenti periodici: resources/views/pagamenti/periodici/index.blade.php
- Calendario: resources/views/calendario/index.blade.php
- Chatbot: resources/views/chatbot/index.blade.php

Pattern UI comuni:
- Filtri in alto, tabella sotto
- Badge per stato pagamenti
- Call to action per cambiare stato pagamento
- Date in formato italiano (gg/mm/aaaa) in output

Regole operative:
- Mantieni la struttura delle view esistente prima di introdurre nuovi componenti
- Se aggiungi nuove view, rispettare naming e cartelle per area funzionale
- Evita di cambiare classi CSS globali senza revisione del layout
