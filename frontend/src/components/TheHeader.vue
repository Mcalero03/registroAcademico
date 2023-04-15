<template>
  <header class="m-0">
    <v-container fluid>
      <div class="d-flex justify-space-between mt-2">
        <div class="ms-2 me-2 my-auto d-flex">
          <div>
            <a href="#" class="menu" @click="stateSideBar = 'active'"
              ><v-icon icon="mdi-menu" size="39"
            /></a>
          </div>
          <div class="ms-5 ps-3 me-2 d-none d-xs-flex d-sm-flex">
            <h1>Name app</h1>
          </div>
        </div>
        <div
          class="ms-2 me-2 my-auto d-flex flex-column text-right"
          v-if="isLoggedIn"
        >
          <span
            >{{ user.name }} <v-icon icon="mdi-account" color="#2D52A8"
          /></span>
          <a href="#" @click="logout()"><b>Cerrar sesión</b></a>
        </div>
      </div>
    </v-container>
  </header>
</template>

<script setup>
import useAuth from "../composables/useAuth";
import useMenu from "@/composables/useMenu";

const { isLoggedIn, user, logout } = useAuth();

const { stateSideBar } = useMenu();
</script>

<style scoped>
a.menu {
  color: black;
}
</style>