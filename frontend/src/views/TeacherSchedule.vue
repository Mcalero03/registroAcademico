<template>
  <div data-app>
    <v-container fluid>
      <v-card class="p-3 mt-3">
        <h2 class="black-secondary text-uppercase text-center my-4">
          Horarios asignados
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
              @click="searchTeacherCard() && showSchedules()"
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
          v-if="schedules.length == 0 && this.editedItem.full_name != ''"
        >
          <v-col>
            <v-card
              id="card"
              class="mx-auto"
              max-width="440"
              min-width="200"
              height="auto"
              theme="dark"
              :elevation="2"
            >
              <v-card-text>
                <p class="text-center p-4">No hay horarios asignados</p>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>

        <!-- schedules -->
        <v-timeline
          side="end"
          align="center"
          direction="horizontal"
          class="pt-10 px-6 overflow-y-auto"
          truncate-line="start"
        >
          <v-timeline-item
            max-width="120px"
            min-width="120px"
            max-height="100%"
            min-height="100%"
            dot-color="deep-purple-accent-4"
            size="small"
            v-for="(schedule, index) in this.schedules"
            v-bind:index="index"
            :key="index"
          >
            <template v-slot:opposite>
              <p>{{ index }}</p>
            </template>

            <v-container fluid class="p-0">
              <v-row>
                <v-col
                  class="py-1 px-1"
                  v-for="(weekday, index) in schedule"
                  :key="index"
                >
                  <v-card
                    theme="dark"
                    class="elevation-4"
                    max-width="130px"
                    min-width="130px"
                    max-height="140px"
                    min-height="150px"
                    align="center"
                    id="card"
                  >
                    <v-card-title
                      style="font-size: 1rem; font-weight: bold"
                      class="pb-0 px-0"
                    >
                      {{ weekday.group_code }}</v-card-title
                    >
                    <v-card-text>
                      <strong class="me-4 text-primary">{{
                        weekday.start_time + " - " + weekday.end_time
                      }}</strong>
                      <div>
                        {{ weekday.classroom_name }}
                      </div>
                      <div>
                        {{ weekday.subject_name }}
                      </div>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>
            </v-container>
          </v-timeline-item>
        </v-timeline>
        <!-- schedules -->
      </v-card>
    </v-container>
  </div>
</template> 

<style lang="scss">
@import "@/assets/styles/variables.scss";

#card,
p {
  font-size: 1rem;
  font-weight: bolder;
}

th {
  color: black !important;
  font-weight: bold !important;
}

#card {
  background-color: $menu-color;
}

#example-card,
#example-card p {
  font-size: 1rem;
  font-weight: bolder;
}
</style>

<script>
import { toast } from "../../node_modules/vue3-toastify";
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
      pensums: [],
      schedules: [],
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

    async showSchedules() {
      const { data } = await teacherApi
        .get("/showSchedules/" + this.searchTeacher)
        .catch((error) => {
          toast.error("No fue posible obtener la información.", {
            autoClose: 2000,
            position: toast.POSITION.TOP_CENTER,
            multiple: false,
          });
        });

      this.schedules = data.schedule;
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
