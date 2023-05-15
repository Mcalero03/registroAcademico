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
              <!-- group_name  -->
              <v-col cols="12" sm="6" md="6">
                <base-input
                  label="Nombre de grupo"
                  v-model="v$.editedItem.group_name.$model"
                  :rules="v$.editedItem.group_name"
                />
              </v-col>
              <!-- group_name  -->
              <!-- student_quantity  -->
              <v-col cols="12" sm="6" md="6">
                <base-input
                  label="Cantidad de estudiantes"
                  v-model="v$.editedItem.students_quantity.$model"
                  :rules="v$.editedItem.students_quantity"
                  type="number"
                  min="1"
                />
              </v-col>
              <!-- student_quantity  -->
            <!-- Schedule -->
            <v-col class="pt-5 pb-5 mt-2">
                <base-button
                  type="primary"
                  title="Agregar horario"
                  @click="addSchedule()"
                />
              </v-col>
            </v-row>
            <!-- Schedule -->
            <!-- Schedule Table -->
            <v-row>
              <v-col align="center" cols="12" md="12" sm="12" class="pt-4">
                <div class="table-responsive-md">
                  <v-table>
                    <thead>
                      <tr>
                        <th>DÍA</th>
                        <th>INICIO</th>
                        <th>FIN</th>
                        <th class="text-center">ACCIÓN</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(schedule, index) in editedItem.schedules"
                        v-bind:index="index"
                        :key="index"
                      >
                        <td v-text="schedule.week_day"></td>
                        <td v-text="schedule.start_time"></td>
                        <td v-text="schedule.end_time"></td>
                        <td class="text-center">
                          <v-icon
                            size="20"
                            class="mr-2"
                            @click="deleteSchedule(index)"
                            icon="mdi-delete"
                          />
                        </td>
                      </tr>
                      <tr v-if="editedItem.schedules.length == 0">
                        <td colspan="5" class="text-center pt-3">
                          <p>No se ha ingresado ningún horario</p>
                        </td>
                      </tr>
                    </tbody>
                  </v-table>
                </div>
                <!-- Modal -->
                <v-dialog v-model="dialogSchedule" max-width="600px" persistent>
                  <v-card height="100%">
                    <v-container>
                      <h2 class="black-secondary text-center mt-4 mb-4">
                        Agregar horario
                      </h2>
                      <v-row>
              <!-- week_day  -->
              <v-col cols="12" sm="12" md="6">
                <base-select
                  label="Día de la semana"
                  :items="weekdays"
                  v-model="v$.schedule.week_day.$model"
                  :rules="v$.schedule.week_day"
                />
              </v-col>
              <!-- week_day  -->
              <!-- start_time  -->
              <v-col cols="12" sm="12" md="6">
                <base-input
                  label="Hora de inicio"
                  v-model="v$.schedule.start_time.$model"
                  :rules="v$.schedule.start_time"
                  type="time"
                />
              </v-col>
              <!-- start_time  -->
              <!-- end_time  -->
              <v-col cols="12" sm="12" md="6">
                <base-input
                  label="Hora de fin"
                  v-model="v$.schedule.end_time.$model"
                  :rules="v$.schedule.end_time"
                  type="time"
                />
              </v-col>
              <!-- end_time  -->
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
              </v-col>
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
import { helpers, minLength, required } from "@vuelidate/validators";

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
      dialogSchedule: false,
      editedIndex: -1,
      editedSchedule: -1,
      title: "GRUPO",
      weekdays: ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes"],
      headers: [
        { title: "GRUPO", key: "group_name" },
        { title: "CANTIDAD", key: "students_quantity" },
        { title: "HORARIO", key: "Schedule" },
        { title: "ACCIONES", key: "actions", sortable: false },
      ],
      total: 0,
      records: [],
      loading: false,
      debounce: 0,
      options: {},
      editedItem: {
        group_name: "",
        students_quantity: "",
        schedules: [],
      },
      defaultItem: {
        group_name: "",
        students_quantity: "",
        schedules: [],
      }, 
      schedule: {
        week_day: "",
        start_time: "",
        end_time: "",
      }
    };
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
        group_name: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(4)
          ),
        },
        students_quantity: {
          required: helpers.withMessage(langMessages.required, required),
        },
      }, 
      schedule: {
        week_day: {
          // required: helpers.withMessage(langMessages.required, required),
        },
        start_time : {
          // required: helpers.withMessage(langMessages.required, required),
        } ,
        end_time: {
          // required: helpers.withMessage(langMessages.required, required),
        }
      }
    };
  },

  methods: {
    async initialize() {
      this.loading = true;
      this.records = [];

      let requests = [this.getDataFromApi()];
      const responses = await Promise.all(requests).catch((error) => {
        alert.error("No fue posible obtener el registro.");
      });

      if (responses) {
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

    addSchedule() {
      this.dialogSchedule = true;
      this.editedSchedule = -1;
      this.v$.schedule.week_day.$model = "";
      this.v$.schedule.start_time.$model = "";
      this.v$.schedule.end_time.$model = "";
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
        this.editedItem.schedules.push({ ...this.schedule });
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
      this.editedSchedule = -1;
    },

    async deleteSchedule(index) {
      this.editedItem.schedules.splice(index, 1);
    },

    close() {
      this.dialog = false;
      this.editedItem.schedules.splice(
          0,
          this.editedItem.schedules.length
        );
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
          const { data } = await groupApi.put(`/${edited.id}`, edited);
          alert.success(data.message);
        } catch (error) {
          alert.error("No fue posible actualizar el registro.");
        }

        this.close();
        this.editedItem.schedules.splice(
          0,
          this.editedItem.schedules.length
        );
        this.initialize();
        return;
      }

      // Creating record
      try {
        const { data } = await groupApi.post(null, this.editedItem);
        console.log(data);
        alert.success(data.message);
      } catch (error) {
        alert.error("No fue posible crear el registro.");
      }

      this.close();
      this.editedItem.schedules.splice(
          0,
          this.editedItem.schedules.length
        );
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