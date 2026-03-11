# Database schema (da migrations)

Tabelle principali:

clienti
- id (PK)
- nome (string)
- email (string, unique)
- telefono (string, nullable)
- partita_iva (string, nullable)
- note (text, nullable)
- timestamps
- deleted_at (soft delete)

lavori
- id (PK)
- cliente_id (FK -> clienti.id)
- data_lavoro (date)
- descrizione (text)
- stato (enum: da_fare, in_corso, completato)
- timestamps
- deleted_at (soft delete)

pagamenti
- id (PK)
- cliente_id (FK -> clienti.id)
- tipo_lavoro (string)
- importo (decimal 10,2)
- cadenza (enum: oneshot, periodico)
- frequenza (enum: mensile, trimestrale, annuale, nullable)
- data_inizio (date, nullable)
- data_scadenza (date)
- stato (enum: in_sospeso, pagato, annullato)
- timestamps
- deleted_at (soft delete)

compiti_lavoro (task)
- id (PK)
- nome (string)
- lavoro_id (FK -> lavori.id)
- scadenza (date)
- status (enum: in_sospeso, completato)
- timestamps
- deleted_at (soft delete)

ricorrenze_pagamenti
- id (PK)
- pagamento_id (FK -> pagamenti.id)
- data_ricorrenza (date)
- stato (enum: in_sospeso, pagato, annullato)
- data_pagamento (date, nullable)
- note (text, nullable)
- timestamps
- unique (pagamento_id, data_ricorrenza)

Relazioni principali:
- Cliente hasMany Lavoro
- Cliente hasMany Pagamento
- Lavoro belongsTo Cliente
- Lavoro hasMany Task
- Task belongsTo Lavoro
- Pagamento belongsTo Cliente
- Pagamento hasMany PagamentoRicorrenza
- PagamentoRicorrenza belongsTo Pagamento

Note importanti:
- La tabella dei task si chiama compiti_lavoro (non tasks)
- I modelli usano SoftDeletes dove presente il campo deleted_at
