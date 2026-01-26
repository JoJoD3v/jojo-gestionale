import './bootstrap';

// Import MDB UI Kit
import * as mdb from 'mdb-ui-kit';

// Import FullCalendar
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';

// Make MDB available globally
window.mdb = mdb;

// Make FullCalendar available globally
window.Calendar = Calendar;
window.dayGridPlugin = dayGridPlugin;
window.interactionPlugin = interactionPlugin;
window.listPlugin = listPlugin;

// Sidebar toggle
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar?.classList.toggle('collapsed');
            mainContent?.classList.toggle('expanded');
        });
    }

    // Mobile sidebar toggle
    const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
    if (mobileSidebarToggle) {
        mobileSidebarToggle.addEventListener('click', function() {
            sidebar?.classList.toggle('show');
        });
    }

    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new mdb.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
