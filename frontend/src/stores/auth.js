import { defineStore } from "pinia";
import { ref } from "vue";

export const useAuthStore = defineStore("auth", () => {
  const state = ref(null);
  const verifier = ref(null);
  const challenge = ref(null);
  const accessToken = ref(null);
  const refreshToken = ref(null);
  const user = ref(null);
  const isLoggedIn = ref(false);

  return {
    state,
    verifier,
    challenge,
    accessToken,
    refreshToken,
    user,
    isLoggedIn,
  };
});
