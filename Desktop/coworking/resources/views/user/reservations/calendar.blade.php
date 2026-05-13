@extends('layouts.user')
@section('title', 'Calendrier')
@section('page-title', 'Calendrier des réservations')

@push('styles')
<style>
.fc { font-family: 'Poppins', sans-serif !important; }
.fc-toolbar-title { font-family: 'Poppins', sans-serif !important; font-size: 20px !important; font-weight: 700 !important; color: var(--text) !important; }
.fc-button { background: var(--surface3) !important; border: 1px solid #475569 !important; color: var(--text) !important; border-radius: 8px !important; font-family: 'Poppins', sans-serif !important; font-size: 13px !important; font-weight: 600 !important; padding: 6px 14px !important; }
.fc-button:hover { background: var(--accent) !important; border-color: var(--accent) !important; }
.fc-button-active { background: var(--accent) !important; border-color: var(--accent) !important; }
.fc-daygrid-day { background: var(--surface2) !important; border-color: var(--border) !important; }
.fc-daygrid-day:hover { background: rgba(79,126,247,0.08) !important; }
.fc-day-today { background: rgba(79,126,247,0.12) !important; }
.fc-col-header-cell { background: var(--surface) !important; color: var(--text-muted) !important; font-size: 12px !important; font-weight: 700 !important; text-transform: uppercase !important; letter-spacing: 0.05em !important; border-color: var(--border) !important; }
.fc-daygrid-day-number { color: var(--text) !important; font-weight: 600 !important; padding: 6px 8px !important; }
.fc-event { border-radius: 6px !important; font-size: 12px !important; font-weight: 600 !important; border: none !important; padding: 2px 6px !important; }
.fc-scrollgrid { border-color: var(--border) !important; }
.fc-scrollgrid-sync-table { border-color: var(--border) !important; }
</style>
@endpush

@section('content')
<div class="fade-in">

    <!-- Legend -->
    <div style="display: flex; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; justify-content: space-between;">
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 6px; font-size: 13px;">
                <div style="width: 12px; height: 12px; border-radius: 3px; background: #f59e0b;"></div>
                <span style="color: var(--text-muted);">En attente</span>
            </div>
            <div style="display: flex; align-items: center; gap: 6px; font-size: 13px;">
                <div style="width: 12px; height: 12px; border-radius: 3px; background: #10b981;"></div>
                <span style="color: var(--text-muted);">Confirmée</span>
            </div>
        </div>
        <a href="{{ route('reservations.create') }}" class="btn btn-primary btn-sm">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nouvelle réservation
        </a>
    </div>

    <div class="card">
        <div id="calendar"></div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/6.1.10/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        buttonText: {
            today: "Aujourd'hui",
            month: 'Mois',
            week: 'Semaine',
            list: 'Liste'
        },
        events: @json($reservations),
        eventClick: function(info) {
            window.location.href = info.event.url;
            info.jsEvent.preventDefault();
        },
        eventMouseEnter: function(info) {
            info.el.style.transform = 'scale(1.02)';
            info.el.style.transition = 'transform 0.1s';
        },
        eventMouseLeave: function(info) {
            info.el.style.transform = 'scale(1)';
        },
        height: 700,
        dayMaxEvents: 3,
        eventDisplay: 'block',
    });

    calendar.render();
});
</script>
@endpush
@endsection
