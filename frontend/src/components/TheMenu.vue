<template>
  <div
    class="menu-sidebar d-flex flex-column align-center"
    :class="stateSideBar"
  >
    <div class="menu-button mt-5 mb-4">
      <a href="#" @click="stateSideBar = 'inactive'">
        <v-icon icon="mdi-menu mt-1" :size="45"></v-icon>
      </a>
    </div>
    <div class="menu-options mt-3 text-center">
      <template v-if="isLoggedIn">
        <!-- <RouterLink to="/" class="d-flex flex-column align-center mb-4">
          <v-icon icon="mdi-home" size="25"></v-icon>
          <span>Bases de datos</span>
        </RouterLink> -->
        <RouterLink
          to="/direction"
          class="d-flex flex-column align-center mb-4"
        >
          <v-icon icon="mdi-town-hall" size="25"></v-icon>
          <span>Dirección</span>
        </RouterLink>
        <RouterLink
          to="/pensumType"
          class="d-flex flex-column align-center mb-4"
        >
          <v-icon icon="mdi-school" size="25"></v-icon>
          <span>Tipo Pensum</span>
        </RouterLink>
        <RouterLink to="/group" class="d-flex flex-column align-center mb-4">
          <v-icon icon="mdi-account-group" size="25"></v-icon>
          <span>Grupo</span>
        </RouterLink>
        <RouterLink to="/relative" class="d-flex flex-column align-center mb-4">
          <v-icon icon="mdi-human-male-female-child" size="25"></v-icon>
          <span>Pariente</span>
        </RouterLink>
        <RouterLink to="/subject" class="d-flex flex-column align-center mb-4">
          <v-icon icon="mdi-bookshelf" size="25"></v-icon>
          <span>Materia</span>
        </RouterLink>
        <RouterLink to="/teacher" class="d-flex flex-column align-center mb-4">
          <v-icon icon="mdi-human-male-board" size="25"></v-icon>
          <span>Profesor</span>
        </RouterLink>
        <RouterLink
          to="/department"
          class="d-flex flex-column align-center mb-4"
        >
          <v-icon icon="mdi-earth" size="25"></v-icon>
          <span>Departamento</span>
        </RouterLink>
        <RouterLink to="/college" class="d-flex flex-column align-center mb-4">
          <v-icon icon="mdi-earth" size="25"></v-icon>
          <span>Escuela</span>
        </RouterLink>
        <RouterLink
          to="/"
          class="d-flex flex-column align-center mb-4"
          @click="logout()"
        >
          <v-icon icon="mdi-logout" size="25"></v-icon>
          <span>Cerrar sesión</span>
        </RouterLink>
      </template>
      <template v-else>
        <RouterLink to="/" class="d-flex flex-column align-center mb-4">
          <v-icon icon="mdi-login" size="25"></v-icon>
          <span>Iniciar sesión</span>
        </RouterLink>
        <!-- <RouterLink to="/register" class="d-flex flex-column align-center mb-4">
          <v-icon icon="mdi-account-plus" size="25"></v-icon>
          <span>Registrarse</span>
        </RouterLink> -->
      </template>
    </div>
  </div>
</template>

<script setup>
import { RouterLink } from "vue-router";
import useMenu from "@/composables/useMenu";
import useAuth from "../composables/useAuth";

const { stateSideBar } = useMenu();
const { isLoggedIn, logout } = useAuth();
</script>

<style lang="scss">
@import "@/assets/styles/variables.scss";

.menu-sidebar {
  width: 6rem;
  height: 100vh;
  position: fixed;
  top: 0;
  z-index: 1;
  background: $menu-color;
  font-size: 16px;
}

.menu-sidebar a {
  color: white;
}

.inactive {
  left: -6rem;
  animation: linear;
  animation-name: hideMenu;
  animation-duration: 0.4s;
}

@keyframes hideMenu {
  0% {
    left: 0;
    transform: translateX(0);
  }
  100% {
    left: -6rem;
    transform: translateX(-6rem);
  }
}

.active {
  left: 0;
  animation: linear;
  animation-name: showMenu;
  animation-duration: 0.4s;
}

@keyframes showMenu {
  0% {
    left: -6rem;
    transform: translateX(-6rem);
  }
  100% {
    left: 0;
    transform: translateX(0);
  }
}
</style>