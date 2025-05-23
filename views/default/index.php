<h1>Work Shift Calendar</h1>
<div id="ec"></div>
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="eventForm" class="modal-content">
            <input type="hidden" id="eventId">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Aggiungi / Modifica Evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="eventTitle" class="form-label">Titolo</label>
                    <input type="text" class="form-control" id="eventTitle" required>
                </div>
                <div class="mb-3">
                    <label for="eventStart" class="form-label">Inizio</label>
                    <input type="datetime-local" class="form-control" id="eventStart" required>
                </div>
                <div class="mb-3">
                    <label for="eventEnd" class="form-label">Fine</label>
                    <input type="datetime-local" class="form-control" id="eventEnd" required>
                </div>
                <div class="mb-3">
                    <label for="eventResource" class="form-label">Risorsa</label>
                    <select class="form-select" id="eventResource" required>
                        <option value="">Seleziona...</option>
                        <option value="1">Amministrazione</option>
                        <option value="2">Forestali</option>
                        <option value="3">Forestali - Squadra Antincendio</option>
                    </select>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="eventAllDay">
                    <label class="form-check-label" for="eventAllDay">All Day</label>
                </div>
                <div class="mb-3">
                    <label for="eventColor" class="form-label">Colore</label>
                    <input type="color" class="form-control form-control-color" id="eventColor" value="#779ECB" title="Scegli un colore">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Salva</button>
                <button id="deleteBtn" type="button" class="btn btn-danger">Elimina</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
            </div>
        </form>
    </div>
</div>
<?php
$this->registerJsFile('@web/js/work_shift_calendar.js');
?>