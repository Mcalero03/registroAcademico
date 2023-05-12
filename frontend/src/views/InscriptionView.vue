<template>
  <div data-app>
    <v-card class="p-3 mt-3">
      <v-container>
        <h2>
          {{ title }}
        </h2>
        <div class="options-table">
          <base-button
            type="primary"
            title="Agregar"
            @click="addRecord()"
          ></base-button>
          <v-col cols="12" sm="4" lg="4" xl="4" class="pl-0 pb-0 pr-0">
            <v-text-field
              class="mt-3"
              variant="outlined"
              label="Buscar"
              type="text"
              v-model="search"
            >
            </v-text-field>
          </v-col>
        </div>
      </v-container>
      <v-data-table-server
        :headers="headers"
        :items-length="total"
        :items="records"
        :loading="loading"
        items-title="id"
        item-value="id"
        @update:options="getDataFromApi"
      >
        <template v-slot:[`item.actions`]="{ item }">
          <v-icon
            size="20"
            class="mr-2"
            @click="editItem(item.raw)"
            icon="mdi-pencil"
          />
          <v-icon
            size="20"
            class="mr-2"
            @click="deleteItem(item.raw)"
            icon="mdi-delete"
          />
        </template>
        <template v-slot:no-data>
          <v-icon @click="initialize" icon="mdi-refresh" />
        </template>
      </v-data-table-server>
    </v-card>

    <v-dialog v-model="dialog" max-width="800px" persistent>
      <v-card>
        <v-card-title>
          <h2 class="mx-auto pt-3 mb-3 text-center black-secondary">
            {{ formTitle }}
          </h2>
        </v-card-title>
        <v-card-text class="pt-0">
          <v-container>
            <!-- Form -->
            <v-row class="pt-0">
              <!-- inscription_date  -->
              <v-col cols="6" sm="4" md="4">
                <base-input
                  label="Fecha de inscripción"
                  v-model="v$.editedItem.inscription_date.$model"
                  :rules="v$.editedItem.inscription_date"
                  type="date"
                />
              </v-col>
              <!-- inscription_date  -->
              <!-- subject_average  -->
              <v-col cols="6" sm="4" md="4">
                <base-input
                  label="Promedio de la materia"
                  v-model="v$.editedItem.subject_average.$model"
                  :rules="v$.editedItem.subject_average"
                />
              </v-col>
              <!-- subject_average  -->
              <!-- attendance_quantity  -->
              <v-col cols="6" sm="4" md="4">
                <base-input
                  label="Cantidad de asistencias"
                  v-model="v$.editedItem.attendance_quantity.$model"
                  :rules="v$.editedItem.attendance_quantity"
                  type="number"
                />
              </v-col>
              <!-- attendance_quantity  -->
              <!-- status  -->
              <v-col cols="6" sm="4" md="4">
                <base-input
                  label="Estado"
                  v-model="v$.editedItem.status.$model"
                  :rules="v$.editedItem.status"
                  value="Inscrito"
                />
              </v-col>
              <!-- status  -->
              <!-- cycle_number  -->
              <v-col cols="6" sm="4" md="4">
                <base-select
                  label="Número de ciclo"
                  :items="cycles"
                  item-title="cycle_number"
                  item-value="cycle_number"
                  v-model="v$.editedItem.cycle_number.$model"
                  :rules="v$.editedItem.cycle_number"
                />
              </v-col>
              <!-- cycle_number  -->
              <!-- group_name  -->
              <v-col cols="6" sm="4" md="4">
                <base-select
                  label="Grupo"
                  :items="groups"
                  item-title="group_name"
                  item-value="group_name"
                  v-model="v$.editedItem.group_name.$model"
                  :rules="v$.editedItem.group_name"
                />
              </v-col>
              <!-- group_name  -->
              <!-- student_name  -->
              <v-col cols="6" sm="7" md="7">
                <base-select
                  label="Estudiante"
                  :items="students"
                  item-title="full_name"
                  item-value="full_name"
                  v-model="v$.editedItem.full_name.$model"
                  :rules="v$.editedItem.full_name"
                />
              </v-col>
              <!-- student_name  -->
              <!-- subject_name  -->
              <v-col cols="6" sm="5" md="5">
                <base-select
                  label="Materia"
                  :items="subjects"
                  item-title="subject_name"
                  item-value="subject_name"
                  v-model="v$.editedItem.subject_name.$model"
                  :rules="v$.editedItem.subject_name"
                />
              </v-col>
              <!-- subject_name  -->
              <!-- relative -->
              <!-- <v-col class="pt-5 pb-5 mt-2">
                <base-button
                  type="primary"
                  title="Agregar nota"
                  @click="addGrade()"
                />
              </v-col> -->
            </v-row>
            <!-- relative -->
            <!-- Grade Table -->
            <!--<v-row>
              <v-col align="center" cols="12" md="12" sm="12" class="pt-4">
                <div class="table-responsive-md">
                  <v-table>
                    <thead>
                      <tr>
                        <th>NOTA</th>
                        <th>FECHA</th>
                        <th>ESTADO</th>
                        <th>EVALUACIÓN</th>
                        <th class="text-center">ACCIÓN</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(grade, index) in editedItem.grades"
                        v-bind:index="index"
                        :key="index"
                      >
                        <td v-text="grade.score"></td>
                        <td v-text="grade.score_date"></td>
                        <td v-text="grade.status"></td>
                        <td v-text="grade.evaluation_name"></td>
                        <td class="text-center">
                          <v-icon
                            size="20"
                            class="mr-2"
                            @click="deleteGrade(index)"
                            icon="mdi-delete"
                          />
                        </td>
                      </tr>
                      <tr v-if="editedItem.grades.length == 0">
                        <td colspan="5" class="text-center pt-3">
                          <p>No se ha ingresado ninguna nota</p>
                        </td>
                      </tr>
                    </tbody>
                  </v-table>
                </div>-->
            <!-- Modal -->
            <!-- <v-dialog v-model="dialogGrade" max-width="600px" persistent>
                  <v-card height="100%">
                    <v-container>
                      <h2 class="black-secondary text-center mt-4 mb-4">
                        Agregar nota
                      </h2> -->
            <!-- <v-row> -->
            <!-- score  -->
            <!-- <v-col cols="6" sm="6" md="6">
                          <base-input
                            label="Nota"
                            v-model="v$.grade.score.$model"
                            :rules="v$.grade.score"
                          />
                        </v-col> -->
            <!-- score  -->
            <!-- score_date  -->
            <!-- <v-col cols="6" sm="6" md="6">
                          <base-input
                            label="Fecha"
                            v-model="v$.grade.score_date.$model"
                            :rules="v$.grade.score_date"
                            type="date"
                          />
                        </v-col> -->
            <!-- score_date  -->
            <!-- status  -->
            <!-- <v-col cols="6" sm="6" md="6">
                          <base-input
                            label="Estado"
                            v-model="v$.grade.status.$model"
                            :rules="v$.grade.status"
                          />
                        </v-col> -->
            <!-- status  -->
            <!-- evaluation_name  -->
            <!-- <v-col cols="6" sm="6" md="6">
                          <base-select
                            label="Evaluación"
                            :items="evaluations"
                            item-title="evaluation_name"
                            item-value="evaluation_name"
                            v-model="v$.grade.evaluation_name.$model"
                            :rules="v$.grade.evaluation_name"
                          />
                        </v-col> -->
            <!-- evaluation_name  -->
            <!-- </v-row>
                      <v-row>
                        <v-col align="center">
                          <base-button
                            type="primary"
                            title="Agregar"
                            @click="addNewGrade()"
                          />
                          <base-button
                            class="ms-1"
                            type="secondary"
                            title="Cancelar"
                            @click="closeGradeDialog()"
                          />
                        </v-col>
                      </v-row>
                    </v-container>
                  </v-card>
                </v-dialog> -->
            <!-- Modal -->
            <!-- </v-col>
            </v-row> -->
            <!-- Grade Table -->
            <!-- Form -->
            <v-row>
              <v-col align="center">
                <base-button type="primary" title="Guardar" @click="save" />
                <base-button
                  class="ms-1"
                  type="secondary"
                  title="Cancelar"
                  @click="close"
                />
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>

    <v-dialog v-model="dialogDelete" max-width="400px">
      <v-card class="h-100">
        <v-container>
          <h1 class="black-secondary text-center mt-3 mb-3">
            Eliminar registro
          </h1>
          <v-row>
            <v-col align="center">
              <base-button
                type="primary"
                title="Confirmar"
                @click="deleteItemConfirm"
              />
              <base-button
                class="ms-1"
                type="secondary"
                title="Cancelar"
                @click="closeDelete"
              />
            </v-col>
          </v-row>
        </v-container>
      </v-card>
    </v-dialog>
  </div>
</template> 



<script>
import { useVuelidate } from "@vuelidate/core";
import { messages } from "@/utils/validators/i18n-validators";
import { helpers, minLength, required, maxLength } from "@vuelidate/validators";

import inscriptionApi from "@/services/inscriptionApi";
import cycleApi from "@/services/cycleApi";
import studentApi from "@/services/studentApi";
import groupApi from "@/services/groupApi";
import subjectApi from "@/services/subjectApi";
import evaluationApi from "@/services/evaluationApi";
import BaseButton from "../components/base-components/BaseButton.vue";
import BaseInput from "../components/base-components/BaseInput.vue";
import BaseSelect from "../components/base-components/BaseSelect.vue";

import useAlert from "../composables/useAlert";

const { alert } = useAlert();
const langMessages = messages["es"].validations;

export default {
  components: { BaseButton, BaseInput, BaseSelect },

  setup() {
    return { v$: useVuelidate() };
  },

  data() {
    return {
      search: "",
      dialog: false,
      dialogDelete: false,
      dialogGrade: false,
      editedIndex: -1,
      editedGrade: -1,
      title: "INSCRIPCIÓN",
      headers: [
        { title: "INSCRIPCIÓN", key: "inscription_date" },
        { title: "PROMEDIO", key: "subject_average" },
        { title: "ASISTENCIAS", key: "attendance_quantity" },
        { title: "ESTADO", key: "status" },
        { title: "CICLO", key: "cycle_number" },
        { title: "ESTUDIANTE", key: "full_name" },
        { title: "GRUPO", key: "group_name" },
        { title: "MATERIA", key: "subject_name" },
        { title: "ACCIONES", key: "actions", sortable: false },
      ],
      total: 0,
      records: [],
      cycles: [],
      students: [],
      groups: [],
      subjects: [],
      // evaluations: [],
      loading: false,
      debounce: 0,
      options: {},
      editedItem: {
        inscription_date: "",
        subject_average: "",
        attendance_quantity: "",
        status: "",
        cycle_number: "",
        full_name: "",
        group_name: "",
        subject_name: "",
        // grades: [],
      },
      defaultItem: {
        inscription_date: "",
        subject_average: "",
        attendance_quantity: "",
        status: "",
        cycle_number: "",
        full_name: "",
        group_name: "",
        subject_name: "",
        // grades: [],
      },
      // grade: {
      //   score: "",
      //   score_date: "",
      //   status: "",
      //   evaluation_name: "",
      // },
    };
  },
  mounted() {
    this.initialize();
  },

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Nuevo registro" : "Editar registro";
    },
  },

  watch: {
    search(val) {
      this.getDataFromApi();
    },
    dialog(val) {
      val || this.close();
    },
    dialogDelete(val) {
      val || this.closeDelete();
    },
    dialogGrade(val) {
      val || this.closeGradeDialog();
    },
  },

  validations() {
    return {
      editedItem: {
        inscription_date: {
          required: helpers.withMessage(langMessages.required, required),
        },
        subject_average: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(1)
          ),
          maxLength: (({ $params }) => maxLength($params), maxLength(2)),
        },
        attendance_quantity: {
          required: helpers.withMessage(langMessages.required, required),
        },
        status: {
          required: helpers.withMessage(langMessages.required, required),
        },
        cycle_number: {
          required: helpers.withMessage(langMessages.required, required),
        },
        full_name: {
          required: helpers.withMessage(langMessages.required, required),
        },
        group_name: {
          required: helpers.withMessage(langMessages.required, required),
        },
        subject_name: {
          required: helpers.withMessage(langMessages.required, required),
        },
      },
      // grade: {
      //   score: {
      //     required: helpers.withMessage(langMessages.required, required),
      //   },
      //   score_date: {
      //     required: helpers.withMessage(langMessages.required, required),
      //   },
      //   status: {
      //     required: helpers.withMessage(langMessages.required, required),
      //   },
      //   evaluation_name: {
      //     required: helpers.withMessage(langMessages.required, required),
      //   },
      // },
    };
  },

  methods: {
    async initialize() {
      this.loading = true;
      this.records = [];

      let requests = [
        this.getDataFromApi(),
        cycleApi.get(null, {
          params: {
            itemsPerPage: -1,
          },
        }),
        studentApi.get(null, {
          params: {
            itemsPerPage: -1,
          },
        }),
        groupApi.get(null, {
          params: {
            itemsPerPage: -1,
          },
        }),
        subjectApi.get(null, {
          params: {
            itemsPerPage: -1,
          },
        }),
        evaluationApi.get(null, {
          params: {
            itemsPerPage: -1,
          },
        }),
      ];
      const responses = await Promise.all(requests).catch((error) => {
        alert.error("No fue posible obtener el registro.");
      });

      if (responses) {
        this.cycles = responses[1].data.data;
        this.students = responses[2].data.data;
        this.groups = responses[3].data.data;
        this.subjects = responses[4].data.data;
        this.evaluations = responses[5].data.data;
      }

      this.loading = false;
    },

    getDataFromApi(options) {
      this.loading = false;
      this.records = [];

      clearTimeout(this.debounce);
      this.debounce = setTimeout(async () => {
        try {
          const { data } = await inscriptionApi.get(null, {
            params: { ...options, search: this.search },
          });

          this.records = data.data;
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

    addGrade() {
      this.dialogGrade = true;
      this.editedGrade = -1;
      this.v$.grade.score.$model = "";
      this.v$.grade.score_date.$model = "";
      this.v$.grade.status.$model = "";
      this.v$.grade.evaluation_name.$model = "";
      this.v$.grade.$reset();
    },

    async addNewGrade() {
      this.v$.grade.$validate();
      if (this.v$.grade.$invalid) {
        alert.error("Campo obligatorio");
        return;
      }

      // Creating record
      try {
        this.editedItem.grades.push({ ...this.grade });
        console.log(this.grade);
      } catch (error) {
        alert.error("No fue posible crear el registro.");
      }

      this.closeGradeDialog();
      this.initialize();
      this.loading = false;
      return;
    },

    closeGradeDialog() {
      this.v$.grade.$reset();
      this.dialogGrade = false;
      this.editedGrade = -1;
    },

    async deleteGrade(index) {
      this.editedItem.grades.splice(index, 1);
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },

    addRecord() {
      this.dialog = true;
      this.editedIndex = -1;
      this.editedItem = Object.assign({}, this.defaultItem);
      this.v$.$reset();
    },

    editItem(item) {
      this.editedIndex = this.records.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    async save() {
      this.v$.$validate();
      if (this.v$.$invalid) {
        alert.error("Campo obligatorio");
        return;
      }

      // Updating record
      if (this.editedIndex > -1) {
        const edited = Object.assign(
          this.records[this.editedIndex],
          this.editedItem
        );

        try {
          const { data } = await inscriptionApi.put(`/${edited.id}`, edited);
          alert.success(data.message);
        } catch (error) {
          alert.error("No fue posible actualizar el registro.");
        }

        this.close();
        this.initialize();
        return;
      }

      // Creating record
      try {
        const { data } = await inscriptionApi.post(null, this.editedItem);
        alert.success(data.message);
      } catch (error) {
        alert.error("No fue posible crear el registro.");
      }

      this.close();
      this.initialize();
      return;
    },

    deleteItem(item) {
      this.editedIndex = this.records.indexOf(item);
      this.editedItem = Object.assign({}, item);

      this.dialogDelete = true;
    },

    async deleteItemConfirm() {
      try {
        const { data } = await inscriptionApi.delete(`/${this.editedItem.id}`, {
          params: { id: this.editedItem.id },
        });

        alert.success(data.message);
      } catch (error) {
        this.close();
      }
      this.initialize();
      this.closeDelete();
    },

    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },
  },
};
</script>