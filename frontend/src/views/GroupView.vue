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
            icon="mdi-clock-edit-outline"
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
              <!-- schools  -->
              <v-col cols="12" sm="4" md="4">
                <v-label>Escuela</v-label>
                <base-select
                  :items="schools"
                  item-title="school_name"
                  item-value="school_name"
                  v-model="v$.editedItem.school_name.$model"
                  :rules="v$.editedItem.school_name"
                  @blur="changeSubject"
                  v-if="editedIndex == -1"
                />
                <base-select
                  :items="schools"
                  item-title="school_name"
                  item-value="school_name"
                  v-model="v$.editedItem.school_name.$model"
                  :rules="v$.editedItem.school_name"
                  @blur="changeSubject"
                  v-if="editedIndex != -1"
                  readonly
                />
              </v-col>
              <!-- schools  -->
              <!-- subject_name  -->
              <v-col cols="12" sm="4" md="4">
                <v-label>Materia</v-label>
                <base-select
                  :items="subjects"
                  item-title="subject_name"
                  item-value="subject_name"
                  v-model="v$.editedItem.subject_name.$model"
                  :rules="v$.editedItem.subject_name"
                  @blur="change"
                  v-if="editedIndex == -1"
                />
                <base-select
                  :items="subjects"
                  item-title="subject_name"
                  item-value="subject_name"
                  v-model="v$.editedItem.subject_name.$model"
                  :rules="v$.editedItem.subject_name"
                  @blur="change"
                  v-if="editedIndex != -1"
                  readonly
                />
              </v-col>
              <!-- subject_name  -->
              <!-- subject_code  -->
              <v-col cols="12" sm="4" md="4">
                <v-label>Código de materia</v-label>
                <base-select
                  :items="codes"
                  item-title="subject_code"
                  item-value="subject_code"
                  v-model="v$.editedItem.subject_code.$model"
                  :rules="v$.editedItem.subject_code"
                  @blur="change"
                  v-if="editedIndex == -1"
                />
                <base-select
                  :items="codes"
                  item-title="subject_code"
                  item-value="subject_code"
                  v-model="v$.editedItem.subject_code.$model"
                  :rules="v$.editedItem.subject_code"
                  @blur="change"
                  v-if="editedIndex != -1"
                  readonly
                />
              </v-col>
              <!-- subject_code  -->
              <!-- teacher  -->
              <v-col cols="12" sm="4" md="4" class="m-0 pr-4 pb-5 pt-0">
                <v-label>Profesor</v-label>
                <base-select
                  :items="teachers"
                  item-title="full_name"
                  item-value="full_name"
                  v-model="v$.editedItem.teacher_full_name.$model"
                  :rules="v$.editedItem.teacher_full_name"
                  v-if="editedIndex == -1"
                />
                <base-select
                  :items="teachers"
                  item-title="full_name"
                  item-value="full_name"
                  v-model="v$.editedItem.teacher_full_name.$model"
                  :rules="v$.editedItem.teacher_full_name"
                  v-if="editedIndex != -1"
                  readonly
                />
              </v-col>
              <!-- teacher  -->
              <!-- group_code  -->
              <v-col cols="12" sm="4" md="4" class="mt-2">
                <base-input
                  label="Código de grupo"
                  v-model="v$.editedItem.group_code.$model"
                  :rules="v$.editedItem.group_code"
                />
              </v-col>
              <!-- group_code  -->
              <!-- student_quantity  -->
              <v-col cols="12" sm="4" md="4" class="mt-2">
                <base-input
                  label="Cantidad de estudiantes"
                  v-model="v$.editedItem.students_quantity.$model"
                  :rules="v$.editedItem.students_quantity"
                  type="number"
                  min="1"
                  max="100"
                />
              </v-col>
              <!-- student_quantity  -->
              <!-- career -->
              <v-col
                cols="auto"
                class="pt-5 pb-5 mt-2"
                v-if="editedIndex != -1"
              >
                <base-button
                  type="secondary"
                  title="Agregar horario"
                  @click="addSchedule()"
                />
              </v-col>
              <!-- career -->
            </v-row>
            <!-- Schedule Table -->
            <v-row>
              <v-col
                align="center"
                cols="12"
                md="12"
                sm="12"
                class="pt-4"
                v-if="editedIndex != -1"
              >
                <h4>HORARIOS ASIGNADOS</h4>
                <div class="table-responsive-md">
                  <v-table>
                    <thead>
                      <tr>
                        <th>DÍA</th>
                        <th>HORA INICIO</th>
                        <th>HORA FIN</th>
                        <th>AULA</th>
                        <th class="text-center">ACCIÓN</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(schedule, index) in editedItem.selectedSchedule"
                        v-bind:index="index"
                        :key="index"
                      >
                        <td v-text="schedule.week_day"></td>
                        <td v-text="schedule.start_time"></td>
                        <td v-text="schedule.end_time"></td>
                        <td v-text="schedule.classroom_name"></td>
                        <td class="text-center">
                          <v-icon
                            size="20"
                            class="mr-2"
                            @click="deleteSchedule(index)"
                            icon="mdi-delete"
                          />
                        </td>
                      </tr>
                      <tr v-if="editedItem.selectedSchedule.length == 0">
                        <td colspan="5" class="text-center pt-3">
                          <p>No hay horarios asignados</p>
                        </td>
                      </tr>
                    </tbody>
                  </v-table>
                </div>
              </v-col>

              <!-- Modal -->
              <v-dialog v-model="dialogSchedule" max-width="700px" persistent>
                <v-card height="100%">
                  <v-container>
                    <h2 class="black-secondary text-center mt-4 mb-4">
                      Agregar horario
                    </h2>
                    <v-row>
                      <!-- classroom_name  -->
                      <v-col cols="12" sm="4" md="4">
                        <v-label>Aulas</v-label>
                        <base-select
                          :items="classrooms"
                          item-title="classroom_name"
                          item-value="classroom_name"
                          v-model="v$.schedule.classroom_name.$model"
                          :rules="v$.schedule.classroom_name"
                          @blur="changeSchedules"
                        />
                      </v-col>
                      <!-- classroom_name  -->
                    </v-row>
                    <v-row>
                      <!-- week_day  -->
                      <v-col cols="12" sm="4" md="4">
                        <v-label>Día</v-label>
                        <base-select
                          :items="weekdays"
                          item-title="weekdays"
                          item-value="weekdays"
                          v-model="v$.schedule.week_day.$model"
                          :rules="v$.schedule.week_day"
                          @blur="changestarttime"
                        />
                      </v-col>
                      <!-- week_day  -->
                      <!-- start_time  -->
                      <v-col cols="12" sm="4" md="4">
                        <v-label>Hora Inicio</v-label>
                        <base-select
                          :items="start_time"
                          item-title="start_time"
                          item-value="start_time"
                          v-model="v$.schedule.start_time.$model"
                          :rules="v$.schedule.start_time"
                          @blur="changeendtime"
                        />
                      </v-col>
                      <!-- start_time  -->
                      <!-- end_time  -->
                      <v-col cols="12" sm="4" md="4">
                        <v-label>Hora Fin</v-label>
                        <base-select
                          :items="end_time"
                          item-title="end_time"
                          item-value="end_time"
                          v-model="v$.schedule.end_time.$model"
                          :rules="v$.schedule.end_time"
                        />
                      </v-col>
                      <!-- end_time  -->
                      <v-col align="center" cols="12" md="12" sm="12">
                        <h4 class="pb-2">HORARIOS DISPONIBLES</h4>
                        <v-data-table
                          v-model="v$.schedule.selectedSchedule.$model"
                          :headers="headerSchedule"
                          :items="schedules"
                          item-title="schedules"
                          item-value="schedules"
                          density="compact"
                          class="elevation-1"
                          items-per-page="5"
                        ></v-data-table
                      ></v-col>
                    </v-row>
                    <v-row>
                      <v-col align="center">
                        <base-button
                          type="primary"
                          title="Agregar"
                          @click="addNewSchedule()"
                        />
                        <base-button
                          class="ms-1"
                          type="secondary"
                          title="Cancelar"
                          @click="closeScheduleDialog()"
                        />
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card>
              </v-dialog>
              <!-- Modal -->
            </v-row>
            <!-- Schedule Table -->

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

import groupApi from "@/services/groupApi";
import schoolApi from "@/services/schoolApi";
import subjectApi from "@/services/subjectApi";
import teacherApi from "@/services/teacherApi";
import classroomApi from "@/services/classroomApi";
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
      dialogSchedule: false,
      editedIndex: -1,
      title: "GRUPOS",
      headers: [
        { title: "GRUPO", key: "group_code" },
        { title: "CANTIDAD", key: "students_quantity" },
        { title: "PROFESOR", key: "teacher_full_name" },
        { title: "MATERIA", key: "subject_name" },
        { title: "CÓDIGO", key: "subject_code" },
        { title: "ACCIONES", key: "actions", sortable: false },
      ],
      headerSchedule: [
        { title: "DÍA", key: "week_day" },
        { title: "HORA INICIO", key: "start_time" },
        { title: "HORA FIN", key: "end_time" },
      ],
      headerClassroom: [{ title: "AULA", key: "classroom_name" }],

      total: 0,
      weekdays: ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes"],
      records: [],
      subjects: [],
      schools: [],
      start_time: [],
      end_time: [],
      teachers: [],
      codes: [],
      schedules: [],
      classrooms: [],
      loading: false,
      debounce: 0,
      options: {},
      editedItem: {
        group_code: "",
        students_quantity: "",
        teacher_full_name: "",
        subject_name: "",
        subject_code: "",
        school_name: "",
        schedule: [],
        selectedSchedule: [],
      },
      defaultItem: {
        group_code: "",
        students_quantity: "",
        teacher_full_name: "",
        subject_name: "",
        subject_code: "",
        school_name: "",
        schedule: [],
        selectedSchedule: [],
      },
      schedule: {
        classroom_name: "",
        week_day: "",
        start_time: "",
        end_time: "",
        selectedSchedule: [],
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
    dialogSchedule(val) {
      val || this.closeScheduleDialog();
    },
  },

  validations() {
    return {
      editedItem: {
        group_code: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(4)
          ),
        },
        students_quantity: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(1)
          ),
          maxLength: helpers.withMessage(
            ({ $params }) => langMessages.maxLength($params),
            maxLength(3)
          ),
        },
        teacher_full_name: {
          required: helpers.withMessage(langMessages.required, required),
        },
        subject_name: {
          required: helpers.withMessage(langMessages.required, required),
        },
        subject_code: {
          required: helpers.withMessage(langMessages.required, required),
        },
        school_name: {
          required: helpers.withMessage(langMessages.required, required),
        },
        schedule: {},
        selectedSchedule: {},
      },
      schedule: {
        classroom_name: {},
        week_day: {},
        start_time: {},
        end_time: {},
        selectedSchedule: {},
      },
    };
  },

  methods: {
    //METHODS TO CHANGE
    async changeSubject() {
      const { data } = await subjectApi
        .get("/subjectByCycle/" + this.v$.editedItem.school_name.$model)
        .catch((error) => {
          alert.error(
            true,
            "No fue posible obtener la información de los espacios.",
            "fail"
          );
        });

      this.subjects = data.subject;
    },

    async change() {
      const { data } = await groupApi
        .get("/bySubject/" + this.v$.editedItem.subject_name.$model)
        .catch((error) => {
          alert.error(
            true,
            "No fue posible obtener la información de los espacios.",
            "fail"
          );
        });

      this.codes = data.subject;
    },

    async changeSchedules() {
      const { data } = await groupApi
        .get(
          "/byTeacher/" +
            this.v$.editedItem.teacher_full_name.$model +
            "/" +
            this.v$.schedule.classroom_name.$model
        )
        .catch((error) => {
          alert.error(
            true,
            "No fue posible obtener la información de los espacios.",
            "fail"
          );
        });

      this.schedules = data.schedule;
    },

    async changestarttime() {
      const { data } = await groupApi
        .get(
          "/byDay/" +
            this.v$.schedule.week_day.$model +
            "/" +
            this.v$.editedItem.teacher_full_name.$model
        )
        .catch((error) => {
          alert.error(
            true,
            "No fue posible obtener la información de los espacios.",
            "fail"
          );
        });

      this.start_time = data.start_time;
    },

    async changeendtime() {
      const { data } = await groupApi
        .get(
          "/byStartTime/" +
            this.v$.schedule.start_time.$model +
            "/" +
            this.v$.schedule.week_day.$model
        )
        .catch((error) => {
          alert.error(
            true,
            "No fue posible obtener la información de los espacios.",
            "fail"
          );
        });

      this.end_time = data.end_time;
    },

    async deleteSchedule(index) {
      this.editedItem.selectedSchedule.splice(index, 1);
    },

    async initialize() {
      this.loading = true;
      this.records = [];

      let requests = [
        this.getDataFromApi(),
        teacherApi.get(null, { params: { itemsPerPage: -1 } }),
        schoolApi.get(null, { params: { itemsPerPage: -1 } }),
        classroomApi.get(null, { params: { itemsPerPage: -1 } }),
      ];
      const responses = await Promise.all(requests).catch((error) => {
        alert.error("No fue posible obtener el registro.");
      });

      if (responses) {
        this.teachers = responses[1].data.data;
        this.schools = responses[2].data.data;
        this.classrooms = responses[3].data.available;
      }

      this.loading = false;
    },

    getDataFromApi(options) {
      this.loading = false;
      this.records = [];

      clearTimeout(this.debounce);
      this.debounce = setTimeout(async () => {
        try {
          const { data } = await groupApi.get(null, {
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
        this.selectedSchedule = [];
      });
    },

    //RELATIVE

    addSchedule() {
      this.dialogSchedule = true;
      this.v$.schedule.classroom_name.$model = "";
      this.v$.schedule.week_day.$model = "";
      this.v$.schedule.start_time.$model = "";
      this.v$.schedule.end_time.$model = "";
      this.v$.schedule.selectedSchedule.$model = [];
      this.v$.schedule.$reset();
    },

    async addNewSchedule() {
      this.v$.schedule.$validate();
      if (this.v$.schedule.$invalid) {
        alert.error("Campo obligatorio");
        return;
      }

      // Creating record
      try {
        this.editedItem.selectedSchedule.push({ ...this.schedule });
      } catch (error) {
        alert.error("No fue posible crear el registro.");
      }

      this.closeScheduleDialog();
      this.initialize();
      this.loading = false;
      return;
    },

    closeScheduleDialog() {
      this.v$.schedule.$reset();
      this.dialogSchedule = false;
      // this.editedRelative = -1;
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

      this.changeSubject();
      this.change();
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
          const { data } = await groupApi.put(`/${edited.id}`, edited);
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
        const { data } = await groupApi.post(null, this.editedItem);
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
        const { data } = await groupApi.delete(`/${this.editedItem.id}`, {
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