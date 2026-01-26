@extends('layouts.app')

@section('title', 'Calendario')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-calendar3 me-2"></i>Calendario Lavori e Pagamenti</h2>
</div>

<div class="card">
    <div class="card-body">
        <div id="calendar"></div>
    </div>
</div>

<!-- Modal Dettagli Giorno -->
<div class="modal fade" id="modalDettagliGiorno" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDettagliGiornoTitle">Dettagli del Giorno</h5>
                <button type="button" class="btn-close" data-mdb-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalDettagliGiornoBody">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Caricamento...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/main.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.10/main.min.css" rel="stylesheet">
<style>
    #calendar {
        max-width: 100%;
        margin: 0 auto;
    }
    
    .fc-event {
        cursor: pointer;
        border: none;
        padding: 2px 5px;
    }
    
    .fc-event-lavoro {
        background-color: #2196F3;
        border-left: 3px solid #1976D2;
    }
    
    .fc-event-pagamento-in-sospeso {
        background-color: #FFC107;
        color: #000;
        border-left: 3px solid #FFA000;
    }
    
    .fc-event-pagamento-pagato {
        background-color: #4CAF50;
        border-left: 3px solid #388E3C;
    }
    
    .fc-event-pagamento-annullato {
        background-color: #F44336;
        border-left: 3px solid #D32F2F;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');
        const calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin, interactionPlugin],
            initialView: 'dayGridMonth',
            locale: 'it',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,dayGridWeek'
            },
            buttonText: {
                today: 'Oggi',
                month: 'Mese',
                week: 'Settimana'
            },
            events: function(info, successCallback, failureCallback) {
                fetch('{{ route('calendario.eventi') }}?start=' + info.startStr + '&end=' + info.endStr)
                    .then(response => response.json())
                    .then(data => {
                        successCallback(data);
                    })
                    .catch(error => {
                        console.error('Errore nel caricamento degli eventi:', error);
                        failureCallback(error);
                    });
            },
            dateClick: function(info) {
                mostraDettagliGiorno(info.dateStr);
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                mostraDettagliGiorno(info.event.startStr);
            },
            height: 'auto'
        });
        
        calendar.render();

        function mostraDettagliGiorno(data) {
            const modal = new mdb.Modal(document.getElementById('modalDettagliGiorno'));
            const modalBody = document.getElementById('modalDettagliGiornoBody');
            const modalTitle = document.getElementById('modalDettagliGiornoTitle');
            
            // Formatta la data
            const dataObj = new Date(data + 'T00:00:00');
            const dataFormattata = dataObj.toLocaleDateString('it-IT', { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
            
            modalTitle.textContent = dataFormattata.charAt(0).toUpperCase() + dataFormattata.slice(1);
            
            // Mostra loading
            modalBody.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Caricamento...</span>
                    </div>
                </div>
            `;
            
            modal.show();
            
            // Carica i dettagli
            fetch('{{ route('calendario.dettagli-giorno') }}?data=' + data)
                .then(response => response.json())
                .then(data => {
                    let html = '';
                    
                    // Lavori
                    if (data.lavori && data.lavori.length > 0) {
                        html += '<h6 class="mb-3"><i class="bi bi-briefcase me-2"></i>Lavori</h6>';
                        data.lavori.forEach(lavoro => {
                            html += `
                                <div class="card mb-3 border-start border-primary border-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">${lavoro.cliente.nome}</h6>
                                                <p class="mb-2">${lavoro.descrizione}</p>
                                                <span class="badge badge-${lavoro.stato}">${lavoro.stato_label}</span>
                                            </div>
                                            <a href="/lavori/${lavoro.id}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                    
                    // Pagamenti
                    if (data.pagamenti && data.pagamenti.length > 0) {
                        html += '<h6 class="mb-3 mt-4"><i class="bi bi-cash-coin me-2"></i>Pagamenti</h6>';
                        data.pagamenti.forEach(pagamento => {
                            html += `
                                <div class="card mb-3 border-start border-${pagamento.stato == 'in_sospeso' ? 'warning' : (pagamento.stato == 'pagato' ? 'success' : 'danger')} border-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">${pagamento.cliente.nome}</h6>
                                                <p class="mb-2">${pagamento.tipo_lavoro}</p>
                                                <strong class="text-success">€ ${pagamento.importo_formattato}</strong>
                                                <span class="badge badge-${pagamento.stato} ms-2">${pagamento.stato_label}</span>
                                            </div>
                                            <a href="/pagamenti/${pagamento.id}" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                    }
                    
                    if (html === '') {
                        html = '<p class="text-muted text-center">Nessun evento per questa data.</p>';
                    }
                    
                    modalBody.innerHTML = html;
                })
                .catch(error => {
                    console.error('Errore:', error);
                    modalBody.innerHTML = '<div class="alert alert-danger">Errore nel caricamento dei dettagli.</div>';
                });
        }
    });
</script>
@endpush
