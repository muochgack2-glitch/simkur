import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';

window.initCalendar = function(elementId, events, options = {}) {
    const calendarEl = document.getElementById(elementId);
    
    if (!calendarEl) {
        console.error('Calendar element not found:', elementId);
        return null;
    }

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, listPlugin, interactionPlugin],
        initialView: options.initialView || 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridYear,listMonth'
        },
        events: events,
        locale: 'id',
        firstDay: 1, // Monday
        height: 'auto',
        
        // Weekend styling
        dayCellClassNames: function(arg) {
            const dayOfWeek = arg.date.getDay();
            // 0 = Sunday, 6 = Saturday
            if (dayOfWeek === 0 || dayOfWeek === 6) {
                return ['weekend-day'];
            }
            return [];
        },
        
        eventClick: function(info) {
            if (options.onEventClick) {
                options.onEventClick(info.event);
            }
        },
        dateClick: function(info) {
            if (options.onDateClick) {
                options.onDateClick(info.date);
            }
        },
        eventClassNames: function(arg) {
            return ['cursor-pointer', 'hover:opacity-80', 'transition-opacity'];
        },
        
        // Display event time
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false
        },
        
        // Event content
        eventContent: function(arg) {
            const event = arg.event;
            const extendedProps = event.extendedProps || {};
            
            let html = '<div class="fc-event-main-frame">';
            html += '<div class="fc-event-title-container">';
            html += '<div class="fc-event-title fc-sticky font-semibold">' + event.title + '</div>';
            
            // Add target grades badge
            if (extendedProps.target_grades_label) {
                const badgeColor = extendedProps.badge_color || 'gray';
                const colorClasses = {
                    'green': 'bg-green-100 text-green-800',
                    'blue': 'bg-blue-100 text-blue-800',
                    'yellow': 'bg-yellow-100 text-yellow-800',
                    'purple': 'bg-purple-100 text-purple-800',
                    'orange': 'bg-orange-100 text-orange-800',
                    'gray': 'bg-gray-100 text-gray-800'
                };
                
                const classes = colorClasses[badgeColor] || colorClasses['gray'];
                html += '<span class="inline-block text-xs px-2 py-0.5 rounded-full mt-1 ' + classes + '">';
                html += extendedProps.target_grades_label;
                html += '</span>';
            }
            
            html += '</div>';
            html += '</div>';
            return { html: html };
        },
        
        views: {
            dayGridYear: {
                type: 'dayGrid',
                duration: { years: 1 },
                buttonText: 'Tahun'
            },
            dayGridMonth: {
                buttonText: 'Bulan'
            },
            listMonth: {
                buttonText: 'Daftar'
            }
        },
        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            year: 'Tahun',
            list: 'Daftar'
        }
    });

    calendar.render();
    
    // Add custom CSS for weekend styling
    if (!document.getElementById('calendar-custom-styles')) {
        const style = document.createElement('style');
        style.id = 'calendar-custom-styles';
        style.textContent = `
            .weekend-day {
                background-color: #FEF2F2 !important;
            }
            .weekend-day:hover {
                background-color: #FEE2E2 !important;
            }
            .fc .fc-daygrid-day-number {
                padding: 4px;
                font-weight: 500;
            }
            .fc .fc-event {
                border-radius: 4px;
                padding: 2px 4px;
                margin: 1px 2px;
                font-size: 0.875rem;
            }
            .fc .fc-event-title {
                font-weight: 600;
            }
            .fc-day-today {
                background-color: #DBEAFE !important;
            }
            .fc-day-today:hover {
                background-color: #BFDBFE !important;
            }
        `;
        document.head.appendChild(style);
    }
    
    return calendar;
};
