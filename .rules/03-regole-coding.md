# Regole di coding

Regole non negoziabili:
- Linguaggio UI e messaggi: italiano
- Usa Eloquent e i modelli esistenti (evita query raw se non necessario)
- Mantieni SoftDeletes dove previsti (non eliminare fisicamente)
- Rispetta gli enum gia definiti in DB (stato, cadenza, frequenza, status)

Conventions pratiche:
- Paginazione standard: 15 elementi per pagina
- Valida sempre input lato server (Form Request o $request->validate)
- Usa Carbon per date e timezone applicativa (Europe/Rome)
- Aggiorna sempre $fillable e $casts quando aggiungi colonne
- Preferisci eager loading (with) per evitare N+1

Modifiche al database:
- Ogni variazione di schema deve passare da nuove migration
- Aggiorna modelli e viste coerentemente con lo schema

Pagamenti periodici:
- Le ricorrenze effettive sono registrate in ricorrenze_pagamenti
- PagamentoPeriodicoController calcola la ricorrenza del mese in runtime
- ChatbotService oggi NON usa la tabella ricorrenze_pagamenti e calcola le occorrenze in PHP
- Se modifichi la logica dei pagamenti periodici, mantieni coerenza tra controller e chatbot

Naming e tabelle:
- La tabella task e compiti_lavoro e il modello e Task
- Evita di rinominare tabelle o colonne senza pianificare migrazione e fix dei modelli
