<?php
?>
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

<script>
    async function retrieveResources() {
        try {
            const res = await fetch('work_shift_calendar/default/resources');

            return await res.json();
        } catch (err) {
            console.error('Errore caricando risorse:', err);
            return [];
        }
    }

    async function initializeCalendar() {
        const resources = await retrieveResources();

        const ec = EventCalendar.create(document.getElementById('ec'), {
            view: 'resourceTimelineWeek',
            headerToolbar: {
                start: 'prev,next today',
                center: 'title',
                end: 'resourceTimelineWeek, resourceTimelineMonth'
            },
            buttonText: {
                today: 'Oggi',
                dayGridMonth: 'Mese',
                timeGridWeek: 'Settimana',
                timeGridDay: 'Giorno',
                listWeek: 'Elenco sett.',
                resourceTimelineWeek: 'Timeline settimanale',
                resourceTimelineMonth: 'Timeline mensile'
            },
            resources: resources,
            resourceGroupField: 'group',
            scrollTime: '09:00:00',
            events: [],
            selectable: true,
            nowIndicator: true,

            dateClick: function(dateClickInfo) {
                const start = moment.tz(dateClickInfo.dateStr, 'Europe/Rome');
                const end = start.clone().add(1, 'hour');
                const modal = new bootstrap.Modal(document.getElementById('eventModal'));
                const formatForInput = "YYYY-MM-DDTHH:mm";

                document.getElementById('eventStart').value = start.format(formatForInput);
                document.getElementById('eventEnd').value   = end.format(formatForInput);

                modal.show();
            },

            select(selectInfo) {
                console.log('info from select: ',selectInfo)
                openModal({
                    title: '',
                    start: selectInfo.startStr,
                    end:   selectInfo.endStr
                });
            },

            eventClick(eventClickInfo) {
                const ev = eventClickInfo.event;
                const formatForInput = "YYYY-MM-DDTHH:mm";

                openModal({
                    id: ev.id,
                    title: ev.title,
                    start: moment.tz(ev.start, 'Europe/Rome').format(formatForInput),
                    end: moment.tz(ev.end, 'Europe/Rome').format(formatForInput),
                    type: ev.extendedProps.type,
                    isAllDay: ev.allDay,
                    backgroundColor: ev.backgroundColor,
                    resourceIds: ev.resourceIds
                });
            },

            eventDrop: async function(info) {
                const ev = info.event;
                const id = ev.id;
                const fmt = 'YYYY-MM-DD HH:mm:ss';
                const start = moment.tz(ev.start, 'Europe/Rome').format(fmt);
                const end = moment.tz(ev.end, 'Europe/Rome').format(fmt);
                const payload = { start, end };

                if (ev.resourceIds && ev.resourceIds.length) {
                    payload.resourceIds = ev.resourceIds;
                }

                if (ev.extendedProps && ev.extendedProps.type != null) {
                    payload.type = ev.extendedProps.type;
                }

                try {
                    const res = await fetch(`work_shift_calendar/default/update-event?id=${encodeURIComponent(id)}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(payload)
                    });
                    const data = await res.json();
                    if (!data.success) {
                        alert(JSON.stringify(data.errors));
                    }

                } catch (err) {
                    console.error('Errore aggiornando evento:', err);
                    info.revert();
                }
            },

            eventResize: async function(info) {
                const ev = info.event;
                const id = ev.id;
                const fmt = 'YYYY-MM-DD HH:mm:ss';
                const start = moment.tz(ev.start, 'Europe/Rome').format(fmt);
                const end = moment.tz(ev.end,   'Europe/Rome').format(fmt);

                try {
                    const res = await fetch(`work_shift_calendar/default/update-event?id=${encodeURIComponent(id)}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ start, end })
                    });
                    const data = await res.json();
                    if (!data.success) {
                        alert(JSON.stringify(data.errors));
                    }
                } catch (err) {
                    console.error('Errore aggiornando durata evento:', err);
                    info.revert();
                }
            },
        });

        await retrieveEvents(ec);

        async function retrieveEvents(ec) {
            try {
                const res = await fetch('work_shift_calendar/default/events');
                const data = await res.json();
                data.forEach(rec => {
                    const resourceIds = rec.employees.map(emp => emp.id);

                    console.log(rec.employees)
                    ec.addEvent({
                        id: rec.id,
                        title: rec.title,
                        start: rec.start,
                        end:   rec.end,
                        backgroundColor: rec.color,
                        resourceIds: resourceIds,
                        extendedProps: {
                            type:  rec.type,
                            color: rec.color
                        }
                    });
                });
            }
            catch (err) {
                console.error('Errore caricando eventi:', err);
            }
        }
    }

    function openModal({id, title, start, end, type, isAllDay, backgroundColor, resourceIds}) {
        document.getElementById('eventTitle').value = title;
        document.getElementById('eventStart').value = start;
        document.getElementById('eventEnd').value = end;
        document.getElementById('eventResource').value = type;
        document.getElementById('eventAllDay').value = isAllDay;
        document.getElementById('eventColor').value = backgroundColor;
        document.getElementById('eventForm').dataset.eventId = id;
        document.getElementById('eventForm').dataset.resourceIds = id;

        new bootstrap.Modal(document.getElementById('eventModal')).show();
    }

    document.getElementById('eventForm').onsubmit = async function(e) {
        e.preventDefault();
        const id = document.getElementById('eventForm').dataset.eventId;

        const payload = {
            title: document.getElementById('eventTitle').value,
            start: document.getElementById('eventStart').value.replace('T', ' '),
            end: document.getElementById('eventEnd').value.replace('T', ' '),
            type: parseInt(document.getElementById('eventResource').value, 10),
            all_day: document.getElementById('eventAllDay').checked ? 1 : 0,
            color: document.getElementById('eventColor').value,
            resourceIds: []
        };

        const url = id > 0
            ? `work_shift_calendar/default/update-event?id=${encodeURIComponent(id)}`
            : 'work_shift_calendar/default/save-event';

        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content');

        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': csrfToken
            },
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (data.success) {
            if (id) {
                location.reload();
            } else {
                ec.addEvent({
                    id: data.data.id,
                    title: data.data.title,
                    start: data.data.start,
                    end: data.data.end,
                    type: data.data.type,
                    allDay: Boolean(data.data.all_day),
                    color: data.data.color
                });
            }
        } else {
            alert('Errore: ' + JSON.stringify(data.errors));
        }

        bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
    };

    document.getElementById('deleteBtn').addEventListener('click', async () => {
        const id = document.getElementById('eventForm').dataset.eventId;

        if (!id) {
            bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
            return;
        }

        if (!confirm('Sei sicuro di voler eliminare questo evento?')) {
            return;
        }

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const res = await fetch(`work_shift_calendar/default/delete-event?id=${encodeURIComponent(id)}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': csrfToken
                },
                body: JSON.stringify({})
            });
            const data = await res.json();

            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
                location.reload();
            } else {
                alert('Errore eliminando evento: ' + JSON.stringify(data.errors));
            }
        } catch (err) {
            console.error('Errore in fetch delete:', err);
            alert('Errore eliminando evento.');
        }
    });

    $(document).ready(async () => await initializeCalendar());
</script>