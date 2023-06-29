<template>
  <div
    class="menu-sidebar d-flex flex-column align-center"
    :class="stateSideBar"
  >
    <div class="menu-button mt-5">
      <a href="#" @click="stateSideBar = 'inactive'">
        <v-icon icon="mdi-menu mt-1" :size="50" class="pl-16 ml-6"></v-icon>
      </a>
    </div>
    <div class="menu-options">
      <template v-if="isLoggedIn">
        <v-expansion-panels class="my-1">
          <v-expansion-panel :elevation="0">
            <v-expansion-panel-title id="panel" :elevation="0">
              <v-icon
                icon="mdi-security"
                size="15"
                color="white"
                class="mr-2"
              ></v-icon
              >Administrador
            </v-expansion-panel-title>
            <v-expansion-panel-text id="panel">
              <v-btn
                v-for="(item, index) in admin"
                :key="index"
                :value="index"
                :append-icon="item.icon"
                :href="item.url"
                :text="item.title"
                id="btn-menu"
                :elevation="0"
                class="justify-content-start"
              ></v-btn>
            </v-expansion-panel-text>
          </v-expansion-panel>

          <v-expansion-panel :elevation="0">
            <v-expansion-panel-title id="panel">
              <v-icon
                icon="mdi-school"
                size="15"
                color="white"
                class="mr-2"
              ></v-icon>
              Administración Académica
            </v-expansion-panel-title>
            <v-expansion-panel-text id="panel">
              <v-btn
                v-for="(item, index) in academic"
                :key="index"
                :value="index"
                :append-icon="item.icon"
                :href="item.url"
                :text="item.title"
                id="btn-menu"
                :elevation="0"
                class="justify-content-start"
              ></v-btn>
            </v-expansion-panel-text>
          </v-expansion-panel>

          <v-expansion-panel :elevation="0">
            <v-expansion-panel-title id="panel">
              <v-icon
                icon="mdi-account-school"
                size="15"
                color="white"
                class="mr-2"
              ></v-icon>
              Estudiante
            </v-expansion-panel-title>
            <v-expansion-panel-text id="panel">
              <v-btn
                v-for="(item, index) in student"
                :key="index"
                :value="index"
                :append-icon="item.icon"
                :href="item.url"
                :text="item.title"
                id="btn-menu"
                :elevation="0"
                class="justify-content-start"
              ></v-btn>
            </v-expansion-panel-text>
          </v-expansion-panel>

          <v-expansion-panel :elevation="0">
            <v-expansion-panel-title id="panel">
              <v-icon
                icon="mdi-cogs"
                size="15"
                color="white"
                class="mr-2"
              ></v-icon>
              Ajustes
            </v-expansion-panel-title>
            <v-expansion-panel-text id="panel">
              <v-btn
                v-for="(item, index) in settings"
                :key="index"
                :value="index"
                :append-icon="item.icon"
                :href="item.url"
                :text="item.title"
                id="btn-menu"
                :elevation="0"
                class="justify-content-start"
              ></v-btn>
            </v-expansion-panel-text>
          </v-expansion-panel>
        </v-expansion-panels>
        <RouterLink
          to="/"
          @click="logout()"
          class="d-flex justify-content-end align-items-end mr-6 mt-4"
        >
          <span>
            <v-icon icon="mdi-logout" size="15" class="mr-2"></v-icon>Cerrar
            sesión</span
          >
        </RouterLink>
      </template>
      <template v-else>
        <RouterLink to="/" class="mt-4">
          <span>
            <v-icon icon="mdi-login" size="15" class="mr-2"></v-icon>
            Iniciar sesión</span
          >
        </RouterLink>
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
    student: [
      {
        title: "Pensum",
        url: "/pensumSubjectDetail",
        icon: "mdi-details",
      },
      {
        title: "Materias inscritas",
        url: "/inscriptionRecord",
        icon: "mdi-check-all",
      },
      {
        title: "Consulta de notas",
        url: "/grades",
        icon: "mdi-checkbook",
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

span {
  font-size: 14px;
}

#btn-menu {
  width: 8rem;
  background-color: $menu-color !important;
  color: white;
  font-size: 12px;
}

#panel {
  background-color: $menu-color !important;
  color: white;
}

.menu-sidebar {
  width: 12.5rem;
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
  left: -12.5rem;
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
    left: -12.5rem;
    transform: translateX(-12.5rem);
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
    left: -12.5rem;
    transform: translateX(-12.5rem);
  }
  100% {
    left: 0;
    transform: translateX(0);
  }
}
</style>