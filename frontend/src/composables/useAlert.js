import { storeToRefs } from "pinia";
import { useAlertStore } from "../stores/alert";
import { watch } from "vue";

const useAlert = () => {
  const alertStore = useAlertStore();

  const { message, time, type, show } = storeToRefs(alertStore);
  const alert = new Alert(message, time, type, show);

  watch(show, (newValue, oldValue) => {
    if (newValue && time.value != -1) {
      console.log("Ocultando alerta");
      setTimeout(() => {
        show.value = false;
      }, time.value);
    }
  });

  watch(time, (newValue, oldValue) => {
    if (newValue != -1) {
      console.log("Ocultando alerta");
      setTimeout(() => {
        show.value = false;
      }, time.value);
    }
  });

  return { message, time, type, show, alert };
};

class Alert {
  constructor(message, time, type, show) {
    this.message = message;
    this.time = time;
    this.type = type;
    this.show = show;
  }

  success(msg, tim = 5000) {
    this.message.value = msg;
    this.type.value = "alert-success";
    this.time.value = tim;
    this.show.value = true;
  }

  error(msg, tim = 5000) {
    this.message.value = msg;
    this.type.value = "alert-error";
    this.time.value = tim;
    this.show.value = true;
  }

  hide() {
    this.show.value = false;
    this.time.value = 5000;
  }
}

export default useAlert;
