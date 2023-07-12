<template>
  <div data-app>
    <v-container fluid>
      <v-card class="p-3 mt-3">
        <h2 class="black-secondary text-uppercase text-center my-4">Pensum</h2>
        <v-row class="pb-4">
          <!-- school_name -->
          <v-col cols="12" sm="6" md="6">
            <base-select
              label="Escuelas"
              :items="schools"
              item-title="school_name"
              item-value="school_name"
              v-model="v$.filter.school_name.$model"
              :rules="v$.filter.school_name"
              @blur="showSubSchool"
            >
            </base-select>
          </v-col>
          <!-- school_name -->
          <!-- sub_school_name -->
          <v-col cols="12" sm="6" md="6">
            <base-select
              label="Sub-Escuelas"
              :items="sub_schools"
              item-title="sub_school_name"
              item-value="sub_school_name"
              v-model="v$.filter.sub_school_name.$model"
              :rules="v$.filter.sub_school_name"
              @blur="showPensums"
            >
            </base-select>
          </v-col>
          <!-- sub_school_name -->
        </v-row>

        <div v-if="pensums.length == 0 && this.filter.sub_school_name != ''">
          <loader v-if="loading" />
          <p class="text-center mt-6">No hay pensums por mostrar</p>
        </div>

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
            v-for="program in pensums"
            :key="program.id"
            :value="program.program_name"
            @click="change()"
            >{{ program.program_name }}</v-tab
          >
        </v-tabs>

        <!-- CONTENIDO POR TAB -->
        <v-window v-model="tab">
          <v-window-item
            v-for="program in pensums"
            v-bind:index="program.program_name"
            :key="program.program_name"
            :value="program.program_name"
          >
            <v-container fluid>
              <v-row dense class="p-3 mt-3">
                <v-col v-for="subject in subjects" :key="subject.subject_id">
                  <v-card
                    id="card"
                    class="mx-auto"
                    max-width="200px"
                    min-width="200px"
                    height="170px"
                    theme="dark"
                    :elevation="2"
                  >
                    <v-card-actions>
                      <!-- id -->
                      <div class="mx-auto">
                        {{ subject.index }}
                      </div>
                      <!-- id -->
                      <!-- subject_code -->
                      <div class="mx-auto">
                        {{ subject.subject_code }}
                      </div>
                      <!-- subject_code -->
                    </v-card-actions>
                    <v-card-text>
                      <!-- subject_name -->
                      <p class="text-primary text-center">
                        {{ subject.subject_name }}
                      </p>
                      <!-- subject_name -->
                    </v-card-text>
                    <v-card-actions>
                      <!-- prerequisites -->
                      <div v-if="subject.status == 1">
                        <!-- subject with one prerequisite -->
                        <div v-if="subject.count == 1">
                          <div
                            class="d-inline ml-8 mx-auto"
                            v-for="prerequisite in subject.prerequisites"
                            :key="prerequisite.id"
                          >
                            {{ prerequisite.subject_id }}
                          </div>
                        </div>
                        <!-- subject with one prerequisite -->
                        <!-- subject with more than one prerequisite -->
                        <div class="ml-6" v-else-if="subject.count >= 1">
                          <div
                            class="d-inline"
                            v-for="prerequisite in subject.prerequisites"
                            :key="prerequisite.id"
                          >
                            {{ prerequisite.subject_id }},
                          </div>
                        </div>
                        <!-- subject with more than one prerequisite -->
                      </div>
                      <!-- subject without prerequisite -->
                      <div class="ml-8 mr-auto" v-if="subject.status == 0">
                        N/A
                      </div>
                      <!-- subject without prerequisite -->
                      <!-- prerequisites -->
                      <!-- units_value -->
                      <div class="mr-8 ml-auto">
                        {{ subject.units_value }}
                      </div>
                      <!-- units_value -->
                    </v-card-actions>
                  </v-card>
                </v-col>
              </v-row>
              <v-row dense class="p-3 mt-3">
                <v-col>
                  <v-card
                    class="mr-auto"
                    max-width="200px"
                    min-width="200px"
                    height="170px"
                    variant="tonal"
                    :elevation="2"
                    id="example-card"
                  >
                    <v-card-actions>
                      <v-tooltip
                        text="Correlativo de la asignatura"
                        location="start"
                      >
                        <template v-slot:activator="{ props }">
                          <div class="mx-auto" v-bind="props">N°</div>
                        </template>
                      </v-tooltip>
                      <v-tooltip text="Código de materia" location="end">
                        <template v-slot:activator="{ props }">
                          <div class="mx-auto" v-bind="props">Cód</div>
                        </template>
                      </v-tooltip>
                    </v-card-actions>
                    <v-card-text class="">
                      <v-tooltip
                        text="Nombre de la materia/curso"
                        location="end"
                      >
                        <template v-slot:activator="{ props }">
                          <p v-bind="props" class="text-primary text-center">
                            Materia
                          </p>
                        </template>
                      </v-tooltip>
                    </v-card-text>
                    <v-card-actions>
                      <v-tooltip text="Prerrequisito(s)" location="start">
                        <template v-slot:activator="{ props }">
                          <div v-bind="props" class="d-inline ml-2 mx-auto">
                            Prerrequisito
                          </div>
                        </template>
                      </v-tooltip>
                      <v-tooltip text="Unidades valorativas" location="end">
                        <template v-slot:activator="{ props }">
                          <div v-bind="props" class="mr-8 ml-auto">U.V</div>
                        </template>
                      </v-tooltip>
                    </v-card-actions>
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
import { useVuelidate } from "@vuelidate/core";
import { messages } from "@/utils/validators/i18n-validators";
import BaseSelect from "../components/base-components/BaseSelect.vue";
import { helpers, required } from "@vuelidate/validators";
import Loader from "@/components/Loader.vue";

import pensumSubjectDetailApi from "@/services/pensumSubjectDetailApi";
import schoolApi from "@/services/schoolApi";

import useAlert from "../composables/useAlert";

const { alert } = useAlert();
const langMessages = messages["es"].validations;

export default {
  components: { BaseSelect, Loader },
  setup() {
    return { v$: useVuelidate() };
  },

  data() {
    return {
      tab: 0,
      search: "",
      schools: [],
      total: 0,
      sub_schools: [],
      pensums: [],
      subjects: [],
      pensumSubject: [],
      loading: false,
      debounce: 0,
      options: {},
      filter: {
        school_name: "",
        sub_school_name: "",
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
      filter: {
        school_name: {
          required: helpers.withMessage(langMessages.required, required),
        },
        sub_school_name: {
          required: helpers.withMessage(langMessages.required, required),
        },
      },
    };
  },

  methods: {
    async change() {
      const { data } = await pensumSubjectDetailApi
        .get("/pensumSubject/" + this.tab)
        .catch((error) => {
          alert.error(true, "No fue posible obtener la información.", "fail");
        });

      this.subjects = data.subject;
    },

    async showSubSchool() {
      if (this.filter.school_name != "") {
        const { data } = await pensumSubjectDetailApi
          .get("/showSubSchool/" + this.filter.school_name)
          .catch((error) => {
            alert.error(true, "No fue posible obtener la información.", "fail");
          });

        this.sub_schools = data.sub_school;
      }
    },

    async showPensums() {
      if (this.filter.sub_school_name != "") {
        this.loading = true;

        const { data } = await pensumSubjectDetailApi
          .get("/showPensums/" + this.filter.sub_school_name)
          .catch((error) => {
            alert.error(true, "No fue posible obtener la información.", "fail");
          });

        this.pensums = data.pensum;
        this.loading = false;
      }
    },

    async initialize() {
      this.loading = true;
      this.records = [];

      let requests = [
        this.getDataFromApi(),
        schoolApi.get(null, {
          params: {
            itemsPerPage: -1,
          },
        }),
      ];

      const responses = await Promise.all(requests).catch((error) => {
        alert.error("No fue posible obtener el registro.");
      });

      if (responses) {
        this.schools = responses[1].data.data;
      }

      // this.change();
      this.loading = false;
    },

    getDataFromApi(options) {
      this.loading = false;
      this.records = [];

      clearTimeout(this.debounce);
      this.debounce = setTimeout(async () => {
        try {
          const { data } = await pensumSubjectDetailApi.get(null, {
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
