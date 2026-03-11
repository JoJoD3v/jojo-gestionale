# Architettura e struttura

Stack principale:
- Laravel 11
- PHP 8.2+
- MySQL
- Frontend: Bootstrap 5 + mdb-ui-kit + Bootstrap Icons

Struttura principale:
- app/Http/Controllers: controller CRUD e dashboard
- app/Models: modelli Eloquent (Cliente, Lavoro, Pagamento, Task, PagamentoRicorrenza)
- app/Services: servizi applicativi (ChatbotService)
- routes/web.php: routing principale con middleware auth
- resources/views: Blade templates (layout, CRUD, dashboard, calendario, chatbot)
- database/migrations: schema DB

Routing chiave (routes/web.php):
- Resource: clienti, lavori, pagamenti, tasks
- pagamenti-unici: index (oneshot)
- pagamenti-periodici: index + azioni ricorrenze
- calendario: index + eventi + dettagli-giorno
- chatbot: index + ask (throttle 15/min)

Note UI/UX:
- Tutta l interfaccia e i messaggi sono in italiano
- Paginazione standard: 15 elementi per pagina
- Stati a enum: mantenere i valori esistenti (vedi schema DB)
