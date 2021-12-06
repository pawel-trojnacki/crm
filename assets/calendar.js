import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";
import bootstrapPlugin from "@fullcalendar/bootstrap";

if (document.getElementById("calendar-holder")) {
  document.addEventListener("DOMContentLoaded", () => {
    var calendarEl = document.getElementById("calendar-holder");

    var eventsUrl = calendarEl.dataset.eventsUrl;

    var calendar = new Calendar(calendarEl, {
      plugins: [dayGridPlugin, timeGridPlugin, listPlugin, bootstrapPlugin],
      themeSystem: "bootstrap",
      headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek,listWeek",
      },
      editable: true,
      eventSources: [
        {
          url: eventsUrl,
          method: "POST",
          extraParams: {
            filters: JSON.stringify({}),
          },
          failure: () => {
            // alert("There was an error while fetching FullCalendar!");
          },
        },
      ],
      timeZone: "UTC",
    });

    calendar.render();
    
    document.querySelector(".fc-prev-button").innerHTML = `<i class="bi bi-arrow-left"></i>`;
    document.querySelector(".fc-next-button").innerHTML = `<i class="bi bi-arrow-right"></i>`;
  });
}
