<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <FullCalendar defaultView="dayGridMonth" :plugins="calendarPlugins" />
      </div>
    </div>
  </div>
</template>

<script>
import FullCalendar from '@fullcalendar/vue';
import dayGridPlugin from '@fullcalendar/daygrid';
import bootstrapPlugin from '@fullcalendar/bootstrap';


export default {
  components: {
    FullCalendar
  },
  data() {
    return {
      calendarPlugins: [
        dayGridPlugin,
        bootstrapPlugin
      ]
    }
  }
}
</script>

<style lang='scss'>

@import '~@fullcalendar/core/main.css';
@import '~@fullcalendar/daygrid/main.css';
@import '~@fullcalendar/bootstrap/main.css';

.fc-dayGrid-view {
  border: 1px solid #ddd;
}

</style>
