import { defineStore } from "pinia";
import { ref } from "vue";

export const useMenuStore = defineStore("menu", () => {
  const stateSideBar = ref("inactive");

  return {
    stateSideBar,
  };
});
