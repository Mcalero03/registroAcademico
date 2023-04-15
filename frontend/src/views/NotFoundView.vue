<template>
  <div class="d-flex justify-center">
    <v-card class="text-center p-4" max-width="600px">
      <v-img
        src="/images/cultura_logo.jpg"
        class="img-fluid mx-auto"
        alt="Logo Cultura"
        style="max-width: 350px"
      />
      <h2>¡Vaya! Ruta no encontrada.</h2>

      <h3 class="my-3">
        Esta ruta no existe. Por favor, regresa a la página inicial.
      </h3>

      <BaseButton title="Inicio" type="secondary" class="mt-3" to="/" />
    </v-card>
  </div>
</template>

<script setup>
import BaseButton from "../components/base-components/BaseButton.vue";
</script>
