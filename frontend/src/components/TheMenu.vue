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
            <v-btn v-bind="props" class="align-center mb-4"
              ><v-icon icon="mdi-security" size="15" color="#313945"></v-icon>
            </v-btn>
          </template>
          <v-list>
            <v-list-item
              v-for="(item, index) in admin"
              :key="index"
              :value="index"
              :prepend-icon="item.icon"
              :to="item.url"
            >
              <v-list-item-title size="15">{{ item.title }} </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
        <v-menu>
          <template v-slot:activator="{ props }">
            <v-btn v-bind="props" class="align-center mb-4"
              ><v-icon icon="mdi-school" size="15" color="#313945"></v-icon>
            </v-btn>
          </template>
          <v-list>
            <v-list-item
              v-for="(item, index) in academic"
              :key="index"
              :value="index"
              :prepend-icon="item.icon"
              :to="item.url"
            >
              <v-list-item-title size="15">{{ item.title }} </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>

        <v-menu>
          <template v-slot:activator="{ props }">
            <v-btn v-bind="props" class="align-center mb-4"
              ><v-icon icon="mdi-cogs" size="15" color="#313945"></v-icon>
            </v-btn>
          </template>
          <v-list>
            <v-list-item
              v-for="(item, index) in settings"
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
    admin: [
      {
        title: "Escuela",
        url: "/school",
        icon: "mdi-town-hall",
      },
      {
        title: "Sub-Escuela",
        url: "/subSchool",
        icon: "mdi-account-school",
      },
      {
        title: "Tipo Pensum",
        url: "/pensumType",
        icon: "mdi-school",
      },
      {
        title: "Pensum",
        url: "/pensum",
        icon: "mdi-view-list",
      },
      {
        title: "Materia",
        url: "/subject",
        icon: "mdi-bookshelf",
      },

      {
        title: "Ciclo",
        url: "/cycle",
        icon: "mdi-list-status",
      },
      {
        title: "Profesor",
        url: "/teacher",
        icon: "mdi-human-male-board",
      },
      {
        title: "Aulas",
        url: "/classroom",
        icon: "mdi-desk",
      },
      // {
      //   title: "Detalle Prerequisitos",
      //   url: "/pensumSubjectDetail",
      //   icon: "mdi-details",
      // },

      // {
      //   title: "Detalle Profesor Materia",
      //   url: "/teacherSubjectDetail",
      //   icon: "mdi-details",
      // },
    ],
    academic: [
      {
        title: "Grupo",
        url: "/group",
        icon: "mdi-account-group",
      },
      {
        title: "Estudiante",
        url: "/student",
        icon: "mdi-account",
      },
      {
        title: "Inscripción",
        url: "/inscription",
        icon: "mdi-file",
      },
      {
        title: "Evaluación",
        url: "/evaluation",
        icon: "mdi-playlist-edit",
      },
      {
        title: "Asistencia",
        url: "/attendance",
        icon: "mdi-checkbox-multiple-outline",
      },
    ],
    settings: [
      {
        title: "Departamento",
        url: "/department",
        icon: "mdi-earth",
      },
      {
        title: "Municipio",
        url: "/municipality",
        icon: "mdi-home-city-outline",
      },
      {
        title: "Parentesco",
        url: "/kinship",
        icon: "mdi-family-tree",
      },
      {
        title: "Horario",
        url: "/schedule",
        icon: "mdi-calendar-clock",
      },
    ],
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