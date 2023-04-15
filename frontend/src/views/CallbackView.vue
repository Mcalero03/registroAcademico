<template>
  <div class="d-flex flex-column justify-center text-center mt-4">
    <Loader />
    <div class="callback">Redireccionando...</div>
  </div>
</template>
 
<script setup>
import { onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";

import useAuth from "../composables/useAuth";
import Loader from "../components/Loader.vue";

const router = useRouter();
const route = useRoute();

const { getAccessToken, getUserInfo } = useAuth();

let verifier = localStorage.getItem("verifier");

onMounted(async () => {
  try {
    const { code, state } = route.query;
    if (!verifier) {
      verifier = state;
    }

    await getAccessToken(verifier, code);

    await getUserInfo();

    router.push("/");
  } catch (error) {
    router.push("/login");
  }
});
</script>