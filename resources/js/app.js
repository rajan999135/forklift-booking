import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
import flatpickr from "flatpickr";

// 24h with minutes; disable past dates
document.addEventListener('DOMContentLoaded', () => {
  const now = new Date();
  const minDate = now.toISOString().slice(0,16); // YYYY-MM-DDTHH:mm

  flatpickr("#start_time", {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    time_24hr: true,
    minDate: "today",
    minuteIncrement: 5,
    onChange: ([sel]) => {
      // set end to +1h if empty or earlier
      const end = document.getElementById('end_time')._flatpickr;
      if (sel && end) {
        const plus1h = new Date(sel.getTime() + 60*60*1000);
        if (!end.selectedDates[0] || end.selectedDates[0] < plus1h) {
          end.setDate(plus1h, true);
        }
      }
    }
  });

  flatpickr("#end_time", {
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    time_24hr: true,
    minDate: "today",
    minuteIncrement: 5,
  });
});
