<script setup>
import { onMounted } from "vue";

import TheFooter from "./components/TheFooter.vue";
import TheHeader from "./components/TheHeader.vue";
import useAuth from "./composables/useAuth";
import TheMenu from "./components/TheMenu.vue";
import Alert from "@/components/Alert.vue";

const { getUserInfo, logout, state, verifier, accessToken, refreshToken } =
  useAuth();

onMounted(async () => {
  accessToken.value = localStorage.getItem("access_token");
  refreshToken.value = localStorage.getItem("refresh_token");
  state.value = localStorage.getItem("state");
  verifier.value = localStorage.getItem("verifier");

  const path = window.location.pathname;
  if (path == "/login" || path == "/callback") {
    return;
  }

  if (!accessToken.value) {
    logout();
    return;
  }

  await getUserInfo();
});
</script>

<template>
  <the-header />
  <the-menu />

  <main>
    <v-container>
      <Alert />
      <RouterView />
    </v-container>
  </main>

  <the-footer />
</template>