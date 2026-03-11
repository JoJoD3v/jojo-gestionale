# Chatbot (Claude/AI) - note operative

Componenti:
- Service: app/Services/ChatbotService.php
- Controller: app/Http/Controllers/ChatbotController.php
- Route: GET /chatbot, POST /chatbot/ask (throttle: 15 richieste/min)
- Config: config/services.php -> openai.api_key e openai.model
- ENV: OPENAI_API_KEY, OPENAI_MODEL (default: gpt-4o-mini)

Comportamento attuale:
- buildContext raccoglie dati in sola lettura da Task, Lavoro, Pagamento, Cliente
- Task: solo in_sospeso, suddivisi in oggi, in ritardo, futuri
- Lavori: stati da_fare e in_corso
- Pagamenti oneshot: in_sospeso, divisi per scaduti / mese corrente / futuri
- Pagamenti periodici: calcolo occorrenze in PHP fino a 12 mesi nel futuro
- Clienti: rubrica completa
- Il prompt impone: risposte in italiano, concise, senza invenzioni

Parametri API:
- endpoint: https://api.openai.com/v1/chat/completions
- max_tokens: 1500
- temperature: 0.4
- timeout: 30s

Regole di modifica:
- Non trasformare il chatbot in un writer: deve restare read-only sui dati del gestionale
- Mantieni il prompt restrittivo (no invenzioni, no supposizioni)
- Se cambi lo schema dati, aggiorna buildContext in modo coerente
- Evita di loggare o esporre API key o PII
