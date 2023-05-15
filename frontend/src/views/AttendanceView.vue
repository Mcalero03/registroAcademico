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
          <!-- teacher  -->
          <v-col cols="12" sm="6" md="6">
            <v-label>Maestro</v-label>
                <select
                  v-model="v$.editedItem.teacher.$model"
                  @change="changeSubject"
                  class="form-select"
                >
                  <option
                    v-for="(option, index) in teachers"
                    :key="index"
                    :value="option.full_name"
                  >
                    {{ option.full_name }}
                  </option>
                </select>
              </v-col>
              <!-- teacher  -->
              <!-- subject  -->
              <v-col cols="12" sm="6" md="6">
                <v-label>Materia</v-label>
                <select
                  v-model="v$.editedItem.subject.$model"
                  @change="changeGroup"
                  class="form-select"
                  v-if="v$.editedItem.teacher.$model"
                >
                  <option
                    v-for="(option, index) in teacherSubject"
                    :key="index"
                    :value="option.subject_name"
                  >
                    {{ option.subject_name }}
                  </option>
                </select>
              </v-col>
              <!-- subject  --> 
               <!-- group  -->
               <v-col cols="12" sm="6" md="6">
                <v-label>Maestro</v-label>
                <select
                  v-model="v$.editedItem.group.$model"
                  @change="changeStudents"
                  label="Grupo"
                  class="form-select"
                  v-if="v$.editedItem.subject.$model" 
                >
                  <option
                    v-for="(option, index) in teacherStudentGroup"
                    :key="index"
                    :value="option.group"
                  >
                    {{ option.group }}
                  </option>
                </select>
              </v-col>
              <!-- group  -->
          <!-- attendance_date  -->
          <v-col cols="12" sm="12" md="6">
            <base-input
              label="Fecha de asistencia"
              v-model="v$.editedItem.attendance_date.$model"
              :rules="v$.editedItem.attendance_date"
              type="date"
              class="pt-1"
            />
          </v-col>
          <!-- attendance_date  -->
          <!-- attendance_time  -->
          <v-col cols="12" sm="12" md="6">
            <base-input
              label="Hora de asistencia"
              v-model="v$.editedItem.attendance_time.$model"
              :rules="v$.editedItem.attendance_time"
              type="time"
            />
          </v-col>
          <!-- attendance_time  -->
          <!-- status  -->
          <v-col cols="12" sm="12" md="6">
            <base-input
              label="Estado"
              v-model="v$.editedItem.status.$model"
              :rules="v$.editedItem.status"
            />
          </v-col>
          <!-- status  -->
          <!-- Student Table -->
          <v-row>
              <v-col align="center" cols="12" md="12" sm="12" class="pt-4">
                <div class="table-responsive-md">
                  <v-table>
                    <thead>
                      <tr>
                        <th>NOMBRE</th>
                        <th>ASISTENCIA 
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(student, index) in studentsGroup"
                        v-bind:index="index"
                        :key="index"
                      >
                        <td v-text="student.full_name"></td>
                        <td>
                          <input type="checkbox" :value="index" v-model="student.attendance_quantity">
                        </td>
                      </tr>
                      <tr v-if="studentsGroup.length == 0">
                        <td colspan="5" class="text-center pt-3">
                          <p>No se ha encontrado ningún estudiante</p>
                        </td>
                      </tr>
                    </tbody>
                  </v-table>
                </div>
              </v-col>
            </v-row>
            <!-- Student Table -->
        </v-row>
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
import { helpers, required } from "@vuelidate/validators";

import attendanceApi from "@/services/attendanceApi";
import inscriptionApi from "@/services/inscriptionApi";
import teacherApi from "@/services/teacherApi"; 
// import subjectApi from "@/services/subjectApi";
import groupApi from "@/services/groupApi";
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
      title: "ASISTENCIA",
      headers: [
        { title: "FECHA", key: "attendance_date" },
        { title: "HORA", key: "attendance_time" },
        { title: "ESTADO", key: "status" },
        // { title: "INSCRIPCIÓN", key: "inscription_id" },
        { title: "CARNET", key: "student_card" },
        { title: "MATERIA", key: "subject_name" },
        // { title: "GROUP", key: "group_name" },
        { title: "ACCIONES", key: "actions", sortable: false },
      ],
      total: 0,
      records: [],
      inscriptions: [],
      groups: [],
      teachers: [],
      teacherSubject: [],
      teacherStudentGroup: [],
      studentsGroup: [this.changeStudents], 
      loading: false,
      debounce: 0,
      options: {},
      editedItem: {
        attendance_date: "",
        attendance_time: "",
        status: "",
        inscription_id: "",
        student_card: "",
        teacher: "", 
        subject: "",
        group: "",
        students: [],
        // group_name: "",
      },
      defaultItem: {
        attendance_date: "",
        attendance_time: "",
        status: "",
        inscription_id: "",
        student_card: "",
        teacher: "", 
        subject: "",
        group: "", 
        students: [],
        // group_name: "",
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
        attendance_date: {
          required: helpers.withMessage(langMessages.required, required),
        },
        attendance_time: {
          required: helpers.withMessage(langMessages.required, required),
        },
        status: {
          required: helpers.withMessage(langMessages.required, required),
        },
        inscription_id: {
          required: helpers.withMessage(langMessages.required, required),
        },
        teacher: {
          required: helpers.withMessage(langMessages.required, required),
        }, 
        subject: {
          required: helpers.withMessage(langMessages.required, required),
        }, 
        group: {
          required: helpers.withMessage(langMessages.required, required),
        },
      },
    };
  },

  methods: { 

    //METHODS TO CHANGE 
    async changeSubject() {
      console.log(this.v$.editedItem.teacher.$model); 
      const teacher = this.v$.editedItem.teacher.$model;
      var arr = teacher.split(", ");
      const name = arr[0];
      const last_name = arr[1];

      const { data } = await attendanceApi
        .get(
          "/byTeacher/" +
          name + "/" + last_name
        )
        .catch((error) => {
          alert.error(
            true,
            "No fue posible obtener la información de los espacios.",
            "fail"
          );
        });

      this.teacherSubject = data.subject;
      console.log(this.teacherSubject);
    }, 

    async changeGroup() {
      const teacher = this.v$.editedItem.teacher.$model;
      const subject = this.v$.editedItem.subject.$model

      var arr = teacher.split(", ");
      const name = arr[0];
      const last_name = arr[1];

      console.log(subject); 

      const { data } = await attendanceApi
        .get(
          "/bySubject/" +
            name + "/" + last_name + "/" + subject
        )
        .catch((error) => {
          alert.error(
            true,
            "No fue posible obtener la información de los espacios.",
            "fail"
          );
        });

      this.teacherStudentGroup = data.group; 

      console.log(this.teacherStudentGroup );
    }, 

    async changeStudents() {
      const subject = this.v$.editedItem.subject.$model
      const group = this.v$.editedItem.group.$model 

      console.log(subject + " " + group);

      const { data } = await attendanceApi
        .get(
          "/byGroup/" +
             group + "/" + subject
        )
        .catch((error) => {
          alert.error(
            true,
            "No fue posible obtener la información de los espacios.",
            "fail"
          );
        });

      this.studentsGroup = data.student;
      console.log(this.studentsGroup);
    }, 

    async addNewStudent() {
      // Creating record
      try {
        this.studentsGroup.push({ ...this.student });
        console.log(this.student);
      } catch (error) {
        alert.error("No fue posible crear el registro.");
      }

      this.initialize();
      this.loading = false;
      return;
    }, 

    //

    async initialize() {
      this.loading = true;
      this.records = [];

      let requests = [
        this.getDataFromApi(),
        inscriptionApi.get(null, {
          params: {
            itemsPerPage: -1,
          },
        }),
        groupApi.get(null, {
          params: {
            itemsPerPage: -1,
          },
        }), 
        teacherApi.get(null, {
          params: {
            itemsPerPage: -1,
          },
        }),
      ];
      const responses = await Promise.all(requests).catch((error) => {
        alert.error("No fue posible obtener el registro.");
      });

      if (responses) {
        this.inscriptions = responses[1].data.data;
        this.groups = responses[2].data.data;
        this.teachers = responses[3].data.data;
      }

      this.loading = false;
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
      console.log(this.studentsGroup.attendance_quantity);
      // this.v$.$validate();
      // if (this.v$.$invalid) {
      //   alert.error("Campo obligatorio");
      //   return;
      // }

      // // Updating record
      // if (this.editedIndex > -1) {
      //   const edited = Object.assign(
      //     this.records[this.editedIndex],
      //     this.editedItem
      //   );

      //   try {
      //     const { data } = await attendanceApi.put(`/${edited.id}`, edited);
      //     alert.success(data.message);
      //   } catch (error) {
      //     alert.error("No fue posible actualizar el registro.");
      //   }

      //   this.close();
      //   this.initialize();
      //   return;
      // }

      // // Creating record
      // try {
      //   const { data } = await attendanceApi.post(null, this.editedItem);
      //   alert.success(data.message);
      // } catch (error) {
      //   alert.error("No fue posible crear el registro.");
      // }

      // this.close();
      // this.initialize();
      // return;
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