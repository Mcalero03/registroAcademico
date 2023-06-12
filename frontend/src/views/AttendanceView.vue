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
            class="ml-6"
            @click="editItem(item.raw)"
            icon="mdi-eye"
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
            <v-row class="pt-0" v-if="editedIndex == -1">
              <!-- school  -->
              <v-col cols="12" sm="6" md="6">
                <v-label>Escuela</v-label>
                <base-select
                  :items="schools"
                  item-title="school_name"
                  item-value="school_name"
                  v-model="v$.editedItem.school.$model"
                  :rules="v$.editedItem.school"
                  @blur="changeTeacher"
                />
              </v-col>
              <!-- school  -->
              <!-- teacher  -->
              <v-col cols="12" sm="6" md="6">
                <v-label>Maestro</v-label>
                <base-select
                  :items="teachers"
                  item-title="full_name"
                  item-value="full_name"
                  v-model="v$.editedItem.teacher.$model"
                  :rules="v$.editedItem.teacher"
                  @blur="changeSubject"
                />
              </v-col>
              <!-- teacher  -->
              <!-- subject  -->
              <v-col cols="12" sm="6" md="6">
                <v-label>Materia</v-label>
                <base-select
                  :items="teacherSubject"
                  item-title="subject_name"
                  item-value="subject_name"
                  v-model="v$.editedItem.subject.$model"
                  :rules="v$.editedItem.subject"
                  @blur="changeGroup"
                />
              </v-col>
              <!-- subject  -->
              <!-- group  -->
              <v-col cols="12" sm="6" md="6">
                <v-label>Grupo</v-label>
                <base-select
                  :items="teacherStudentGroup"
                  item-title="group"
                  item-value="group"
                  v-model="v$.editedItem.group.$model"
                  :rules="v$.editedItem.group"
                  @blur="changeStudents"
                />
              </v-col>
              <!-- group  -->
            </v-row>
            <v-row>
              <v-col align="center" cols="12" md="12" sm="12" class="pt-4">
                <div class="table-responsive-md">
                  <v-table>
                    <thead>
                      <tr>
                        <th>NOMBRE</th>
                        <th>ASISTENCIA</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(student, index) in editedItem.attendances"
                        v-bind:index="index"
                        :key="index"
                      >
                        <td v-text="student.full_name"></td>
                        <td>
                          <v-checkbox
                            v-model="student.attendance_status"
                            true-value="1"
                            false-value="0"
                            class="ml-6"
                            color="info"
                            disabled
                            v-if="editedIndex != -1"
                          />
                          <v-checkbox
                            v-model="student.attendance_status"
                            true-value="1"
                            false-value="0"
                            class="ml-6"
                            color="info"
                            v-else-if="editedIndex == -1"
                          />
                        </td>
                      </tr>
                      <tr v-if="editedItem.attendances.length == 0">
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
            <!-- Form -->
            <v-row>
              <v-col align="center">
                <base-button
                  type="primary"
                  title="Guardar"
                  @click="save"
                  v-if="editedIndex == -1"
                />

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
import { helpers, required } from "@vuelidate/validators";
import attendanceApi from "@/services/attendanceApi";
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
      editedIndex: -1,
      title: "ASISTENCIAS",
      headers: [
        { title: "FECHA", key: "attendance_date" },
        { title: "HORA", key: "attendance_time" },
        { title: "MATERIA", key: "subject_name" },
        { title: "GRUPO", key: "group_code" },
        { title: "ACCIONES", key: "actions", sortable: false },
      ],
      total: 0,
      records: [],
      schools: [],
      teachers: [],
      teacherSubject: [],
      teacherStudentGroup: [],
      loading: false,
      debounce: 0,
      options: {},
      editedItem: {
        teacher: "",
        school: "",
        subject: "",
        group: "",
        attendance_date: this.getDate(),
        attendance_time: this.getTime(),
        attendances: [],
      },
      defaultItem: {
        teacher: "",
        subject: "",
        group: "",
        school: "",
        attendance_date: this.getDate(),
        attendance_time: this.getTime(),
        attendances: [],
      },
    };
  },

  mounted() {
    this.initialize();
    this.getTime();

    setInterval(() => {
      this.getTime();
    }, 1000);
  },

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Nuevo registro" : "Visualizar registro";
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
        teacher: {
          required: helpers.withMessage(langMessages.required, required),
        },
        subject: {
          required: helpers.withMessage(langMessages.required, required),
        },
        group: {
          required: helpers.withMessage(langMessages.required, required),
        },
        school: {
          required: helpers.withMessage(langMessages.required, required),
        },
      },
    };
  },

  methods: {
    //METHODS TO CHANGE
    async changeTeacher() {
      const { data } = await attendanceApi
        .get("/bySchool/" + this.editedItem.school)
        .catch((error) => {
          alert.error(
            true,
            "No fue posible obtener la información de los espacios.",
            "fail"
          );
        });

      this.teachers = data.teachers;
    },

    async changeSubject() {
      const teacher = this.v$.editedItem.teacher.$model;
      var arr = teacher.split(", ");
      const name = arr[0];
      const last_name = arr[1];

      const { data } = await attendanceApi
        .get("/byTeacher/" + name + "/" + last_name)
        .catch((error) => {
          alert.error(
            true,
            "No fue posible obtener la información de los espacios.",
            "fail"
          );
        });

      this.teacherSubject = data.subject;
    },

    async changeGroup() {
      const teacher = this.v$.editedItem.teacher.$model;
      const subject = this.v$.editedItem.subject.$model;

      var arr = teacher.split(", ");
      const name = arr[0];
      const last_name = arr[1];

      const { data } = await attendanceApi
        .get("/bySubject/" + name + "/" + last_name + "/" + subject)
        .catch((error) => {
          alert.error(
            true,
            "No fue posible obtener la información de los espacios.",
            "fail"
          );
        });

      this.teacherStudentGroup = data.group;
    },

    async changeStudents() {
      this.loading = true;
      const subject = this.v$.editedItem.subject.$model;
      const group = this.v$.editedItem.group.$model;

      const { data } = await attendanceApi
        .get("/byGroup/" + group + "/" + subject)
        .catch((error) => {
          alert.error(
            true,
            "No fue posible obtener la información de los espacios.",
            "fail"
          );
        });

      this.editedItem.attendances = data.student;
      this.loading = false;
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

    getDate() {
      const datetime = new Date().toISOString().substring(0, 10);
      return datetime;
    },

    getTime() {
      const datetime = new Date().toLocaleTimeString();
      return datetime;
    },

    getDataFromApi(options) {
      this.loading = false;
      this.records = [];

      clearTimeout(this.debounce);
      this.debounce = setTimeout(async () => {
        try {
          const { data } = await attendanceApi.get(null, {
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
          const { data } = await attendanceApi.put(`/${edited.id}`, edited);
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
        const { data } = await attendanceApi.post(null, this.editedItem);
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
        const { data } = await attendanceApi.delete(`/${this.editedItem.id}`, {
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