<template>
  <div data-app>
    <v-container fluid>
      <v-card class="p-3 mt-3">
        <h2 class="black-secondary text-uppercase text-center my-4">
          Comprobante de inscripción
        </h2>
        <v-row class="pb-4">
          <!-- student -->
          <v-col cols="8" sm="4" md="6">
            <v-label>Buscar estudiante por carnet</v-label>
            <v-text-field
              class="mt-3"
              variant="outlined"
              type="text"
              v-model="searchStudent"
            >
            </v-text-field>
          </v-col>
          <!-- student -->
          <v-col cols="4" sm="2" md="3" class="pt-12">
            <base-button
              type="primary"
              title="Buscar"
              @click="searchStudentCard() && showPrograms()"
            />
          </v-col>
          <!-- full_name  -->
          <v-col cols="12" sm="12" md="6">
            <base-input
              label="Estudiante"
              v-model="v$.editedItem.full_name.$model"
              :rules="v$.editedItem.full_name"
              readonly
            />
          </v-col>
          <!-- full_name  -->
          <!-- program_name -->
          <v-col cols="12" sm="12" md="6">
            <base-select
              label="Carreras"
              :items="pensums"
              item-title="program_name"
              item-value="program_name"
              v-model="v$.editedItem.program_name.$model"
              :rules="v$.editedItem.program_name"
              @blur="showInscriptions"
            >
            </base-select>
          </v-col>
          <!-- program_name -->
        </v-row>

        <!-- TABS -->
        <v-tabs
          v-model="tab"
          color="deep-purple-accent-4"
          align-tabs="center"
          show-arrows
          fixed-tabs
          slider-color="deep-purple-accent-4"
          v-if="this.editedItem.program_name != ''"
        >
          <v-tab :value="this.editedItem.program_name">{{
            this.editedItem.program_name
          }}</v-tab>
        </v-tabs>

        <!-- CONTENIDO POR TAB -->
        <v-window v-model="tab">
          <v-window-item :value="this.editedItem.program_name">
            <v-container fluid>
              <v-row
                dense
                class="p-3 mt-3"
                v-if="
                  this.inscriptions == '' && this.editedItem.program_name != ''
                "
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
                      <p class="text-center p-4">
                        No hay materias inscritas para este ciclo
                      </p>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>
              <v-row dense class="p-3 mt-3" v-if="this.inscriptions != ''">
                <v-col
                  v-for="(subject, index) in inscriptions"
                  v-bind:index="index"
                  :key="index"
                >
                  <v-card
                    id="card"
                    class="mx-auto"
                    max-width="800"
                    min-width="200"
                    height="auto"
                    theme="dark"
                    :elevation="2"
                  >
                    <v-card-text class="pb-0">
                      <p>Ciclo: {{ subject.cycle }}</p>
                      <p class="text-primary">
                        Estado: {{ subject.inscription_status }}
                      </p>
                    </v-card-text>
                    <v-card-text>
                      <div class="mx-auto">
                        <v-table
                          style="
                            background-color: white;
                            color: black;
                            border-radius: 0.4rem;
                          "
                        >
                          <thead class="text-primary text-center">
                            <tr>
                              <th>Materia</th>
                              <th>Grupo</th>
                              <th>Profesor</th>
                              <th>Correo</th>
                              <th>Estado</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr
                              v-for="(inscription, index) in subject.subjects"
                              v-bind:index="index"
                              :key="index"
                            >
                              <td v-text="inscription.subject_name"></td>
                              <td v-text="inscription.group_code"></td>
                              <td v-text="inscription.full_name"></td>
                              <td v-text="inscription.mail"></td>
                              <td
                                v-text="inscription.inscription_detail_status"
                              ></td>
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
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import { useVuelidate } from "@vuelidate/core";
import { messages } from "@/utils/validators/i18n-validators";
import BaseSelect from "../components/base-components/BaseSelect.vue";
import BaseInput from "../components/base-components/BaseInput.vue";
import BaseButton from "../components/base-components/BaseButton.vue";
import { helpers, required } from "@vuelidate/validators";
import Loader from "@/components/Loader.vue";

import evaluationApi from "@/services/evaluationApi";
import inscriptionApi from "@/services/inscriptionApi";

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
      tab: 0,
      search: "",
      searchStudent: "",
      total: 0,
      pensums: [],
      inscriptions: [],
      califications: [],
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
    async showPrograms() {
      const { data } = await evaluationApi
        .get("/showPrograms/" + this.searchStudent)
        .catch((error) => {
          toast.error("No fue posible obtener la información.", {
            autoClose: 2000,
            position: toast.POSITION.TOP_CENTER,
            multiple: false,
          });
        });

      this.pensums = data.program;
    },

    async showInscriptions() {
      if (this.searchStudent != "" && this.editedItem.program_name != "") {
        const { data } = await inscriptionApi
          .get(
            "/showInscriptions/" +
              this.searchStudent +
              "/" +
              this.editedItem.program_name
          )
          .catch((error) => {
            toast.error("No fue posible obtener la información.", {
              autoClose: 2000,
              position: toast.POSITION.TOP_CENTER,
              multiple: false,
            });
          });

        this.inscriptions = data.inscription;
      }
    },

    async searchStudentCard() {
      try {
        if (this.searchStudent != "") {
          const { data } = await inscriptionApi.get(null, {
            params: {
              searchStudent: this.searchStudent,
            },
          });
          this.students = data.student;
          this.editedItem.full_name = this.students[0].full_name;
        } else {
          toast.error("Ingrese el carnet del estudiante.", {
            autoClose: 2000,
            position: toast.POSITION.TOP_CENTER,
            multiple: false,
          });
        }
      } catch (error) {
        toast.error("No fue posible obtener el estudiante.", {
          autoClose: 2000,
          position: toast.POSITION.TOP_CENTER,
          multiple: false,
        });
      }
    },

    async initialize() {
      this.loading = true;
      this.records = [];

      let requests = [
        this.getDataFromApi(),
        // schoolApi.get(null, {
        //   params: {
        //     itemsPerPage: -1,
        //   },
        // }),
      ];

      const responses = await Promise.all(requests).catch((error) => {
        alert.error("No fue posible obtener el registro.");
      });

      // if (responses) {
      //   this.schools = responses[1].data.data;
      // }

      this.loading = false;
    },

    getDataFromApi(options) {
      this.loading = false;
      this.records = [];

      clearTimeout(this.debounce);
      this.debounce = setTimeout(async () => {
        try {
          const { data } = await evaluationApi.get(null, {
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
