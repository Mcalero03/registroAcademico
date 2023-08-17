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
          <v-tooltip text="Editar" location="start">
            <template v-slot:activator="{ props }">
              <v-icon
                size="20"
                class="mr-2"
                @click="editItem(item.raw)"
                icon="mdi-pencil"
                v-bind="props"
              />
            </template>
          </v-tooltip>
          <v-tooltip text="Eliminar" location="end">
            <template v-slot:activator="{ props }">
              <v-icon
                size="20"
                class="mr-2"
                @click="deleteItem(item.raw)"
                icon="mdi-delete"
                v-bind="props"
              />
            </template>
          </v-tooltip>
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
              <!-- school  -->
              <v-col cols="12" sm="6" md="6">
                <v-label>Escuela</v-label>
                <base-select
                  :items="schools"
                  item-title="school_name"
                  item-value="school_name"
                  v-model="v$.editedItem.school_name.$model"
                  :rules="v$.editedItem.school_name"
                  @blur="showTeachers"
                  v-if="editedIndex == -1"
                />
                <base-select
                  :items="schools"
                  item-title="school_name"
                  item-value="school_name"
                  v-model="v$.editedItem.school_name.$model"
                  :rules="v$.editedItem.school_name"
                  v-if="editedIndex != -1"
                  readonly
                />
              </v-col>
              <!-- school  -->
              <!-- teacher_name  -->
              <v-col cols="12" sm="6" md="6">
                <v-label>Maestro</v-label>
                <base-select
                  :items="teachers"
                  item-title="full_name"
                  item-value="full_name"
                  v-model="v$.editedItem.teacher_name.$model"
                  :rules="v$.editedItem.teacher_name"
                  @blur="showSubjects"
                  v-if="editedIndex == -1"
                />
                <base-select
                  :items="teachers"
                  item-title="full_name"
                  item-value="full_name"
                  v-model="v$.editedItem.teacher_name.$model"
                  :rules="v$.editedItem.teacher_name"
                  v-if="editedIndex != -1"
                  readonly
                />
              </v-col>
              <!-- teacher_name  -->
              <!-- subject_name  -->
              <v-col cols="12" sm="6" md="6">
                <v-label>Materia</v-label>
                <base-select
                  :items="subjects"
                  item-title="subject_name"
                  item-value="subject_name"
                  v-model="v$.editedItem.subject_name.$model"
                  :rules="v$.editedItem.subject_name"
                  @blur="showGroups"
                  v-if="editedIndex == -1"
                />
                <base-select
                  :items="subjects"
                  item-title="subject_name"
                  item-value="subject_name"
                  v-model="v$.editedItem.subject_name.$model"
                  :rules="v$.editedItem.subject_name"
                  v-if="editedIndex != -1"
                  readonly
                />
              </v-col>
              <!-- subject_name  -->
              <!-- group_code  -->
              <v-col cols="12" sm="6" md="6">
                <v-label>Grupo</v-label>
                <base-select
                  :items="groups"
                  item-title="group_code"
                  item-value="group_code"
                  v-model="v$.editedItem.group_code.$model"
                  :rules="v$.editedItem.group_code"
                  @blur="showStudents"
                  v-if="editedIndex == -1"
                />
                <base-select
                  :items="groups"
                  item-title="group_code"
                  item-value="group_code"
                  v-model="v$.editedItem.group_code.$model"
                  :rules="v$.editedItem.group_code"
                  v-if="editedIndex != -1"
                  readonly
                />
              </v-col>
              <!-- group_code  -->
              <!-- evaluation_name  -->
              <v-col cols="12" sm="6" md="6">
                <base-input
                  label="Nombre de la evaluación"
                  v-model="v$.editedItem.evaluation_name.$model"
                  :rules="v$.editedItem.evaluation_name"
                />
              </v-col>
              <!-- evaluation_name  -->
            </v-row>
            <v-row>
              <!-- available_ponder  -->
              <v-col cols="12" sm="4" md="4" v-if="editedIndex == -1">
                <v-label>Disponible</v-label>
                <base-input
                  v-model="this.editedItem.available_ponder"
                  :value="'%' + this.editedItem.available_ponder"
                  readonly
                />
              </v-col>
              <v-col cols="12" sm="4" md="4" v-if="editedIndex != -1">
                <div
                  v-for="(ponder, index) in editedItem.available_ponder"
                  v-bind:index="index"
                  :key="index"
                >
                  <v-label>Disponible</v-label>
                  <base-input
                    v-model="this.editedItem.available_ponder"
                    :value="'%' + ponder.available_ponder"
                    readonly
                  />
                </div>
              </v-col>
              <!-- available_ponder  -->
              <!-- ponder  -->
              <v-col cols="12" sm="5" md="5">
                <v-label>Ponderación de evaluación</v-label>
                <base-input
                  v-model="v$.editedItem.ponder.$model"
                  :rules="v$.editedItem.ponder"
                  type="number"
                  :max="this.editedItem.available_ponder"
                  min="0"
                />
              </v-col>
              <!-- ponder  -->
            </v-row>
            <v-row>
              <v-col align="center" cols="12" md="12" sm="12" class="pt-4">
                <div class="table-responsive-md">
                  <v-table>
                    <thead>
                      <tr>
                        <th>NOMBRE</th>
                        <th>CALIFICACIÓN</th>
                        <th>ACCIÓN</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(student, index) in editedItem.califications"
                        v-bind:index="index"
                        :key="index"
                      >
                        <td v-text="student.full_name"></td>
                        <td v-text="student.score"></td>
                        <td>
                          <v-tooltip text="Editar calificación" location="end">
                            <template v-slot:activator="{ props }">
                              <v-icon
                                size="20"
                                class="mr-2"
                                @click="editItemStatus(index)"
                                icon="mdi-pencil"
                                v-bind="props"
                              />
                            </template>
                          </v-tooltip>
                        </td>
                      </tr>
                      <tr v-if="editedItem.califications.length == 0">
                        <td colspan="5" class="text-center pt-3">
                          <loader v-if="loading" />
                          <p>No se ha encontrado ningún estudiante</p>
                        </td>
                      </tr>
                    </tbody>
                  </v-table>
                </div>
              </v-col>
            </v-row>
            <!-- Student Table -->

            <!-- Modal -->
            <v-dialog v-model="dialogEditStatus" max-width="600px" persistent>
              <v-card height="100%">
                <v-container>
                  <h2 class="black-secondary text-center mt-4 mb-4">
                    Ingrese la calificación
                  </h2>
                  <v-row>
                    <!-- student_name  -->
                    <v-col cols="8" sm="8" md="8">
                      <base-input
                        label="Estudiante"
                        v-model="v$.calification.full_name.$model"
                        :rules="v$.calification.full_name"
                        readonly
                      />
                    </v-col>
                    <!-- student_name  -->
                    <!-- score  -->
                    <v-col cols="4" sm="4" md="4">
                      <base-input
                        label="Calificación"
                        v-model="v$.calification.score.$model"
                        :rules="v$.calification.score"
                        type="number"
                        step="0.1"
                        min="0"
                        max="10"
                      />
                    </v-col>
                    <!-- score  -->
                  </v-row>
                  <v-row>
                    <v-col align="center">
                      <base-button
                        type="primary"
                        title="Actualizar"
                        @click="addEditItemStatus()"
                      />
                    </v-col>
                  </v-row>
                </v-container>
              </v-card>
            </v-dialog>
            <!-- Modal -->
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
import { toast } from "../../node_modules/vue3-toastify";
import "vue3-toastify/dist/index.css";

import { useVuelidate } from "@vuelidate/core";
import { messages } from "@/utils/validators/i18n-validators";
import { helpers, minLength, required, maxLength } from "@vuelidate/validators";

import evaluationApi from "@/services/evaluationApi";
import schoolApi from "@/services/schoolApi";
import BaseButton from "../components/base-components/BaseButton.vue";
import BaseInput from "../components/base-components/BaseInput.vue";
import BaseSelect from "../components/base-components/BaseSelect.vue";
import Loader from "@/components/Loader.vue";

import useAlert from "../composables/useAlert";

const { alert } = useAlert();
const langMessages = messages["es"].validations;

export default {
  components: { BaseButton, BaseInput, BaseSelect, Loader },

  setup() {
    return { v$: useVuelidate() };
  },

  data() {
    return {
      search: "",
      dialog: false,
      dialogDelete: false,
      dialogEditStatus: false,
      editedIndex: -1,
      title: "EVALUACIONES",
      headers: [
        { title: "MATERIA", key: "subject_name" },
        { title: "GRUPO", key: "group_code" },
        { title: "EVALUACIÓN", key: "evaluation_name" },
        { title: "PONDERACIÓN", key: "ponder" },
        { title: "ACCIONES", key: "actions", sortable: false },
      ],
      headerStudent: [{ title: "NOMBRE", key: "full_name" }],
      total: 0,
      records: [],
      schools: [],
      teachers: [],
      subjects: [],
      students: [],
      groups: [],
      loading: false,
      debounce: 0,
      options: {},
      editedItem: {
        evaluation_name: "",
        available_ponder: "",
        ponder: "",
        subject_name: "",
        group_code: "",
        teacher_name: "",
        school_name: "",
        califications: [],
      },
      defaultItem: {
        evaluation_name: "",
        available_ponder: "",
        ponder: "",
        subject_name: "",
        group_code: "",
        teacher_name: "",
        school_name: "",
        califications: [],
      },
      calification: {
        full_name: "",
        score: "",
      },
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
  },

  validations() {
    return {
      editedItem: {
        evaluation_name: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(4)
          ),
        },
        ponder: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(1)
          ),
          maxLength: (({ $params }) => maxLength($params), maxLength(3)),
        },
        available_ponder: {
          required: helpers.withMessage(langMessages.required, required),
        },
        subject_name: {
          required: helpers.withMessage(langMessages.required, required),
        },
        group_code: {
          required: helpers.withMessage(langMessages.required, required),
        },
        school_name: {
          required: helpers.withMessage(langMessages.required, required),
        },
        teacher_name: {
          required: helpers.withMessage(langMessages.required, required),
        },
        califications: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(1)
          ),
          maxLength: (({ $params }) => maxLength($params), maxLength(2)),
        },
      },
      calification: {
        full_name: {
          required: helpers.withMessage(langMessages.required, required),
        },
        score: {
          required: helpers.withMessage(langMessages.required, required),
        },
      },
    };
  },

  methods: {
    //CHANGE METHODS
    async showTeachers() {
      if (this.editedItem.school_name != "") {
        const { data } = await evaluationApi
          .get("/showTeacher/" + this.editedItem.school_name)
          .catch((error) => {
            alert.error(
              true,
              "No fue posible obtener la información de los espacios.",
              "fail"
            );
          });

        this.teachers = data.teachers;
      }
    },

    async showSubjects() {
      if (this.v$.editedItem.teacher_name.$model != "") {
        const teacher = this.v$.editedItem.teacher_name.$model;
        var arr = teacher.split(", ");
        const name = arr[0];
        const last_name = arr[1];

        const { data } = await evaluationApi
          .get("/showSubjects/" + name + "/" + last_name)
          .catch((error) => {
            alert.error(
              true,
              "No fue posible obtener la información de los espacios.",
              "fail"
            );
          });

        this.subjects = data.subjects;
      }
    },

    async showGroups() {
      if (this.editedItem.subject_name != "") {
        const { data } = await evaluationApi
          .get("/showGroups/" + this.editedItem.subject_name)
          .catch((error) => {
            alert.error(
              true,
              "No fue posible obtener la información de los espacios.",
              "fail"
            );
          });

        this.groups = data.groups;
      }
    },

    async showStudents() {
      if (this.editedItem.group_code != "") {
        const { data } = await evaluationApi
          .get("/showStudents/" + this.editedItem.group_code)
          .catch((error) => {
            alert.error(
              true,
              "No fue posible obtener la información de los espacios.",
              "fail"
            );
          });

        console.log(data.ponder);
        this.editedItem.califications = data.students;
        this.editedItem.available_ponder =
          data.ponder[0].available_ponder[0].total_ponder;
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

    //EDIT SCORE OF STUDENT
    editItemStatus(index) {
      this.editedStudent = this.editedItem.califications[index];
      this.editedItem.califications.splice(index, 1);
      this.calification = Object.assign({}, this.editedStudent);
      this.dialogEditStatus = true;
    },

    async addEditItemStatus() {
      this.v$.calification.$validate();
      if (this.v$.calification.$invalid) {
        alert.error("Campo obligatorio");
        return;
      }

      // Creating record
      try {
        this.editedItem.califications.push({ ...this.calification });
        toast.success("Calificación guardada.", {
          autoClose: 2000,
          position: toast.POSITION.TOP_CENTER,
          multiple: false,
        });
      } catch (error) {
        alert.error("No fue posible crear el registro.");
      }

      this.closeEditStatus();
      this.initialize();
      this.loading = false;
      return;
    },

    closeEditStatus() {
      this.v$.calification.$reset();
      this.dialogEditStatus = false;
    },

    //EVALUATION
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
      this.v$.editedItem.$validate();
      if (this.v$.editedItem.$invalid) {
        toast.warn("Llene los campos obligatorios.", {
          autoClose: 2000,
          position: toast.POSITION.TOP_CENTER,
          multiple: false,
        });
        return;
      }

      // Updating record
      if (this.editedIndex > -1) {
        const edited = Object.assign(
          this.records[this.editedIndex],
          this.editedItem
        );

        try {
          const { data } = await evaluationApi.put(`/${edited.id}`, edited);
          if (data.message) {
            alert.success(data.message);
          } else {
            alert.error(data.error);
          }
        } catch (error) {
          alert.error("No fue posible actualizar el registro.");
        }

        this.close();
        this.initialize();
        return;
      }

      // Creating record
      try {
        const { data } = await evaluationApi.post(null, this.editedItem);
        if (data.message) {
          alert.success(data.message);
        } else {
          alert.error(data.error);
        }
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
        const { data } = await evaluationApi.delete(`/${this.editedItem.id}`, {
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