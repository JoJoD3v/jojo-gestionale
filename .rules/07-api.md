# API endpoints e payload

Nota: tutte le route web sono protette da auth dove indicato in routes/web.php.

Chatbot
- GET /chatbot -> view chatbot.index
- POST /chatbot/ask -> JSON
  Richiesta:
  - domanda (string, 1..1000) [required]
  - cronologia (array, max 20) [optional]
    - cronologia.*.role (user|assistant)
    - cronologia.*.content (string, max 2000)
  Risposta:
  - { risposta: string }

Pagamenti
- POST /pagamenti/{pagamento}/marca-pagato
- POST /pagamenti/{pagamento}/annulla

Pagamenti periodici
- GET /pagamenti-periodici?mese=YYYY-MM&stato=&cliente_id=&frequenza=
- POST /pagamenti-periodici/{pagamento}/marca-ricorrenza-pagata
  Richiesta:
  - data_ricorrenza (YYYY-MM-DD)
- POST /pagamenti-periodici/{pagamento}/annulla-ricorrenza
  Richiesta:
  - data_ricorrenza (YYYY-MM-DD)

Pagamenti unici
- GET /pagamenti-unici?stato=&cliente_id=&data_inizio=YYYY-MM-DD&data_fine=YYYY-MM-DD

Calendario
- GET /calendario
- GET /calendario/eventi
- GET /calendario/dettagli-giorno?data=YYYY-MM-DD

Tasks
- POST /tasks/{task}/completa

CRUD resource (rest)
- /clienti
- /lavori
- /pagamenti
- /tasks

Note:
- La route root / reindirizza a /dashboard
- /dashboard protetta da auth+verified
