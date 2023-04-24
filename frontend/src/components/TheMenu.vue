<template>
  <!-- v-icon size= "25" -->
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
          <v-icon icon="mdi-home" size="15"></v-icon>
          <span>Bases de datos</span>
        </RouterLink> -->
        <v-menu>
          <template v-slot:activator="{ props }">
            <v-btn v-bind="props" class="d-flex flex-column align-center mb-4"
              ><v-icon icon="mdi-cogs" size="15"></v-icon>
              CRUD
            </v-btn>
          </template>
          <v-list>
            <v-list-item
              v-for="(item, index) in items"
              :key="index"
              :value="index"
              :prepend-icon="item.icon"
              :to="item.url"
            >
              <v-list-item-title size="15">{{ item.title }} </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
        <RouterLink
          to="/"
          class="d-flex flex-column align-center mb-4"
          @click="logout()"
        >
          <v-icon icon="mdi-logout" size="15"></v-icon>
          <span>Cerrar sesión</span>
        </RouterLink>
        <!-- <v-card
          class="d-flex flex-column align-center mx-auto"
          width="100%"
          position="absolute"
          append-icon="mdi-cogs"
        >
          <v-list
            v-model="open"
            class="d-flex flex-column align-center pl-4 mb-4"
          >
            <v-list-group value="CRUD">
              <template v-slot:activator="{ props }">
                <v-list-item v-bind="props" title="CRUD"></v-list-item>
              </template>

              <v-list-item
                v-for="([title, icon, url], i) in settings"
                :key="i"
                :title="title"
                :prepend-icon="icon"
                :value="title"
                :to="url"
              ></v-list-item>
            </v-list-group>
          </v-list>
        </v-card> -->
      </template>
      <template v-else>
        <RouterLink to="/" class="d-flex flex-column align-center mb-4">
          <v-icon icon="mdi-login" size="15"></v-icon>
          <span>Iniciar sesión</span>
        </RouterLink>
        <!-- <RouterLink to="/register" class="d-flex flex-column align-center mb-4">
          <v-icon icon="mdi-account-plus" size="15"></v-icon>
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
<script>
export default {
  data: () => ({
    items: [
      { title: "Direccion", url: "/direction", icon: "mdi-town-hall" },
      { title: "Tipo Pensum", url: "/pensumType", icon: "mdi-school" },
      { title: "Pensum", url: "/pensum", icon: "mdi-view-list" },
      {
        title: "Detalle Pensum Materia",
        url: "/pensumSubjectDetail",
        icon: "mdi-details",
      },
      {
        title: "Materia",
        url: "/subject",
        icon: "mdi-bookshelf",
      },
      {
        title: "Evaluación",
        url: "/evaluation",
        icon: "mdi-playlist-edit",
      },
      { title: "Grupo", url: "/group", icon: "mdi-account-group" },
      {
        title: "Pariente",
        url: "/relative",
        icon: "mdi-human-male-female-child",
      },
      {
        title: "Profesor",
        url: "/teacher",
        icon: "mdi-human-male-board",
      },
      {
        title: "Departamento",
        url: "/department",
        icon: "mdi-earth",
      },
      {
        title: "Escuela",
        url: "/college",
        icon: "mdi-account-school",
      },
      {
        title: "Ciclo",
        url: "/cycle",
        icon: "mdi-list-status",
      },
    ],
    // settings: [
    //   ["Escuela", "mdi-account-school", "/college"],
    //   ["Cycle", "mdi-list-status"],
    // ],
  }),
};
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