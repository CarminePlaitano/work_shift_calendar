const WorkShiftCalendarModule = (function() {
    let ec = null;

    async function retrieveResources() {
        try {
            const res = await fetch('work_shift_calendar/default/resources');
            return await res.json();
        } catch (err) {
            console.error('Errore caricando risorse:', err);
            return [];
        }
    }

    async function retrieveEvents() {
        try {
            const res = await fetch('work_shift_calendar/default/events');
            return await res.json();
        } catch (err) {
            console.error('Errore caricando eventi:', err);
            return [];
        }
    }

    function openModal(eventData = {}) {
        // Puoi mettere qui il codice per aprire e popolare il modal
        const {id, title, start, end, type, isAllDay, backgroundColor, resourceIds} = eventData;

        document.getElementById('eventTitle').value = title || '';
        document.getElementById('eventStart').value = start || '';
        document.getElementById('eventEnd').value = end || '';
        document.getElementById('eventResource').value = type || '';
        document.getElementById('eventAllDay').checked = isAllDay || false;
        document.getElementById('eventColor').value = backgroundColor || '#779ECB';
        document.getElementById('eventForm').dataset.eventId = id || '';
        document.getElementById('eventForm').dataset.resourceIds = resourceIds || '';

        new bootstrap.Modal(document.getElementById('eventModal')).show();
    }

    async function initializeCalendar(container) {
        const resources = await retrieveResources();

        ec = EventCalendar.create(container, {
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

            dateClick: function(info) {
                const start = moment.tz(info.dateStr, 'Europe/Rome');
                const end = start.clone().add(1, 'hour');
                openModal({
                    start: start.format("YYYY-MM-DDTHH:mm"),
                    end: end.format("YYYY-MM-DDTHH:mm")
                });
            },

            select: function(selectInfo) {
                openModal({
                    start: selectInfo.startStr,
                    end: selectInfo.endStr
                });
            },

            eventClick: function(info) {
                const ev = info.event;
                openModal({
                    id: ev.id,
                    title: ev.title,
                    start: moment.tz(ev.start, 'Europe/Rome').format("YYYY-MM-DDTHH:mm"),
                    end: moment.tz(ev.end, 'Europe/Rome').format("YYYY-MM-DDTHH:mm"),
                    type: ev.extendedProps.type,
                    isAllDay: ev.allDay,
                    backgroundColor: ev.backgroundColor,
                    resourceIds: ev.resourceIds
                });
            },

            eventDrop: async function(info) {
                await updateEventDate(info.event);
            },

            eventResize: async function(info) {
                await updateEventDate(info.event);
            }
        });

        const events = await retrieveEvents();
        events.forEach(ev => {
            const resourceIds = ev.employees.map(emp => emp.id);
            ec.addEvent({
                id: ev.id,
                title: ev.title,
                start: ev.start,
                end: ev.end,
                backgroundColor: ev.color,
                resourceIds: resourceIds,
                extendedProps: { type: ev.type, color: ev.color }
            });
        });

        bindFormEvents();

        return ec;
    }

    async function updateEventDate(event) {
        const id = event.id;
        const fmt = 'YYYY-MM-DD HH:mm:ss';
        const start = moment.tz(event.start, 'Europe/Rome').format(fmt);
        const end = moment.tz(event.end, 'Europe/Rome').format(fmt);

        let payload = { start, end };
        if (event.resourceIds?.length) payload.resourceIds = event.resourceIds;
        if (event.extendedProps?.type != null) payload.type = event.extendedProps.type;

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
            if (!data.success) alert(JSON.stringify(data.errors));
        } catch (err) {
            console.error('Errore aggiornando evento:', err);
            event.revert();  // Se EventCalendar supporta revert
        }
    }

    function bindFormEvents() {
        const form = document.getElementById('eventForm');
        form.onsubmit = async function(e) {
            e.preventDefault();

            const id = form.dataset.eventId;
            const payload = {
                title: document.getElementById('eventTitle').value,
                start: document.getElementById('eventStart').value.replace('T', ' '),
                end: document.getElementById('eventEnd').value.replace('T', ' '),
                type: parseInt(document.getElementById('eventResource').value, 10),
                all_day: document.getElementById('eventAllDay').checked ? 1 : 0,
                color: document.getElementById('eventColor').value,
                resourceIds: []  // Da adattare se serve
            };

            const url = id
                ? `work_shift_calendar/default/update-event?id=${encodeURIComponent(id)}`
                : 'work_shift_calendar/default/save-event';

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            try {
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
                    if (!id) {
                        ec.addEvent({
                            id: data.data.id,
                            title: data.data.title,
                            start: data.data.start,
                            end: data.data.end,
                            type: data.data.type,
                            allDay: Boolean(data.data.all_day),
                            color: data.data.color
                        });
                    } else {
                        location.reload(); // oppure aggiornare evento in ec
                    }
                    bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
                } else {
                    alert('Errore: ' + JSON.stringify(data.errors));
                }
            } catch (err) {
                console.error('Errore nel salvataggio evento:', err);
            }
        };

        document.getElementById('deleteBtn').addEventListener('click', async () => {
            const id = form.dataset.eventId;
            if (!id) {
                bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
                return;
            }

            if (!confirm('Sei sicuro di voler eliminare questo evento?')) return;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
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
    }

    return {
        init: (container) => initializeCalendar(container)
    };
})();