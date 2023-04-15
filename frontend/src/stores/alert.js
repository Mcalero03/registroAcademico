import { defineStore } from "pinia";
import { ref } from "vue";

export const useAlertStore = defineStore("alert", () => {
  const message = ref("");
  const time = ref(5000);
  const type = ref("success");
  const show = ref(false);

  return {
    message,
    time,
    type,
    show,
  };
});
