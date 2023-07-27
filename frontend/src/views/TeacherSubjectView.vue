<template>
  <div data-app>
    <v-container fluid>
      <v-card class="p-3 mt-3">
        <h2 class="black-secondary text-uppercase text-center my-4">
          Grupos asignados
        </h2>

        <v-row class="pb-4">
          <!-- student -->
          <v-col cols="8" sm="4" md="6">
            <v-label>Buscar profesor por carnet</v-label>
            <v-text-field
              class="mt-3"
              variant="outlined"
              type="text"
              v-model="searchTeacher"
            >
            </v-text-field>
          </v-col>
          <!-- student -->
          <v-col cols="4" sm="2" md="3" class="pt-12">
            <base-button
              type="primary"
              title="Buscar"
              @click="searchTeacherCard() && showGroups()"
            />
          </v-col>
          <!-- full_name  -->
          <v-col cols="12" sm="12" md="6">
            <base-input
              label="Profesor"
              v-model="v$.editedItem.full_name.$model"
              :rules="v$.editedItem.full_name"
              readonly
            />
          </v-col>
          <!-- full_name  -->
        </v-row>

        <v-row
          dense
          class="p-3 mt-3"
          v-if="groups.length == 0 && this.editedItem.full_name != ''"
        >
          <v-col>
            <v-card
              id="message-card"
              class="mx-auto"
              max-width="440px"
              min-width="200px"
              theme="dark"
              height="auto"
              :elevation="2"
            >
              <v-card-text>
                <p class="text-center p-4">No hay grupos asignados</p>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <!-- TABS -->
        <v-tabs
          v-model="tab"
          color="deep-purple-accent-4"
          align-tabs="center"
          show-arrows
          fixed-tabs
          slider-color="deep-purple-accent-4"
        >
          <v-tab
            v-for="(group, index) in this.groups"
            :key="index"
            :value="index"
            >{{ index }}</v-tab
          >
        </v-tabs>

        <!-- CONTENIDO POR TAB -->
        <v-window v-model="tab">
          <v-window-item
            v-for="(subjects, index) in this.groups"
            v-bind:index="index"
            :key="index"
            :value="index"
          >
            <v-container fluid>
              <v-row dense>
                <v-col>
                  <v-card
                    id="card"
                    class="mx-auto"
                    max-width="440px"
                    min-width="200"
                    height="auto"
                    theme="dark"
                    :elevation="2"
                  >
                    <v-card-text>
                      <div class="mx-auto">
                        <v-table
                          style="
                            background-color: white;
                            color: black;
                            border-radius: 0.4rem;
                          "
                          class="text-center"
                        >
                          <thead>
                            <tr>
                              <th>Grupo</th>
                              <th>Cantidad de estudiantes</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr
                              v-for="(subject, index) in subjects"
                              v-bind:index="index"
                              :key="index"
                            >
                              <td>{{ subject.group_code }}</td>
                              <td>{{ subject.students_quantity }}</td>
                            </tr>
                          </tbody>
                        </v-table>
                      </div>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>
            </v-container>
          </v-window-item>
        </v-window>
        <!-- TABS -->
      </v-card>
    </v-container>
  </div>
</template> 

<style lang="scss">
@import "@/assets/styles/variables.scss";
// @import "@/assets/styles/schedule.scss";

#card,
p {
  font-size: 1rem;
  font-weight: bolder;
}

th {
  color: black !important;
  font-weight: bold !important;
}

#card,
#message-card {
  background-color: $menu-color;
}
</style>

<script>
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import { useVuelidate } from "@vuelidate/core";
import { messages } from "@/utils/validators/i18n-validators";
import BaseSelect from "../components/base-components/BaseSelect.vue";
import BaseInput from "../components/base-components/BaseInput.vue";
import BaseButton from "../components/base-components/BaseButton.vue";
import { helpers, required } from "@vuelidate/validators";
import Loader from "@/components/Loader.vue";

import teacherApi from "@/services/teacherApi";

import useAlert from "../composables/useAlert";

const { alert } = useAlert();
const langMessages = messages["es"].validations;

export default {
  components: { BaseSelect, Loader, BaseButton, BaseInput },
  setup() {
    return { v$: useVuelidate() };
  },

  data() {
    return {
      search: "",
      searchTeacher: "",
      total: 0,
      tab: this.groups,
      pensums: [],
      groups: [],
      time: [],
      days: [],
      loading: false,
      debounce: 0,
      options: {},
      editedItem: {
        full_name: "",
        program_name: "",
      },
      defaultItem: {
        full_name: "",
        program_name: "",
      },
    };
  },

  mounted() {
    this.initialize();
  },

  computed: {},

  watch: {
    search(val) {
      this.getDataFromApi();
    },
  },

  validations() {
    return {
      editedItem: {
        full_name: {
          required: helpers.withMessage(langMessages.required, required),
        },
        program_name: {
          required: helpers.withMessage(langMessages.required, required),
        },
      },
    };
  },

  methods: {
    async searchTeacherCard() {
      try {
        if (this.searchTeacher != "") {
          const { data } = await teacherApi.get(null, {
            params: {
              searchTeacher: this.searchTeacher,
            },
          });
          this.teachers = data.teacher;
          this.editedItem.full_name = this.teachers[0].full_name;
        } else {
          toast.error("Ingrese el carnet del profesor.", {
            autoClose: 2000,
            position: toast.POSITION.TOP_CENTER,
            multiple: false,
          });
        }
      } catch (error) {
        toast.error("No fue posible obtener el profesor.", {
          autoClose: 2000,
          position: toast.POSITION.TOP_CENTER,
          multiple: false,
        });
      }
    },

    async showGroups() {
      const { data } = await teacherApi
        .get("/showGroups/" + this.searchTeacher)
        .catch((error) => {
          toast.error("No fue posible obtener la información.", {
            autoClose: 2000,
            position: toast.POSITION.TOP_CENTER,
            multiple: false,
          });
        });

      this.groups = data.group;
    },

    async initialize() {
      this.loading = true;
      this.records = [];

      let requests = [this.getDataFromApi()];

      const responses = await Promise.all(requests).catch((error) => {
        alert.error("No fue posible obtener el registro.");
      });

      this.loading = false;
    },

    getDataFromApi(options) {
      this.loading = false;
      this.records = [];

      clearTimeout(this.debounce);
      this.debounce = setTimeout(async () => {
        try {
          const { data } = await teacherApi.get(null, {
            params: { ...options, search: this.search },
          });

          this.total = data.total;
          this.loading = false;
        } catch (error) {
          alert.error("No fue posible obtener los registros.");
        }
      });
    },

    created() {
      this.initialize();
    },

    beforeMount() {
      this.getDataFromApi({
        page: 1,
        itemsPerPage: 10,
        sortBy: [],
        search: "",
      });
    },
  },
};
</script> 
