<template>
  <div data-app>
    <v-card class="p-3 mt-3">
      <v-container>
        <h2>
          {{ title }}
        </h2>
        <div class="options-table">
          <!-- <base-button
            type="primary"
            title="Agregar"
            @click="addRecord()"
          ></base-button> -->
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
      <v-container>
        <!-- Form -->
        <v-row class="pt-0">
          <!-- attendance_date  -->
          <v-col cols="12" sm="12" md="6">
            <base-input
              label="Fecha de asistencia"
              v-model="v$.editedItem.attendance_date.$model"
              :rules="v$.editedItem.attendance_date"
              type="date"
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
          <!-- inscription_id  -->
          <v-col cols="12" sm="12" md="6">
            <base-select
              label="Inscripción"
              :items="inscriptions"
              item-title="full_name"
              item-value="full_name"
              v-model="v$.editedItem.inscription_id.$model"
              :rules="v$.editedItem.inscription_id"
            />
          </v-col>
          <!-- inscription_id  -->
          <!-- groups  -->
          <!-- <v-col cols="12" sm="12" md="6">
            <base-select
              label="Grupos"
              :items="groups"
              item-title="group_name"
              item-value="group_name"
              v-model="v$.editedItem.group_name.$model"
              :rules="v$.editedItem.group_name"
            />
          </v-col> -->
          <!-- groups  -->
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
    <!-- 
    <v-dialog v-model="dialog" max-width="800px" persistent>
      <v-card>
        <v-card-title>
          <h2 class="mx-auto pt-3 mb-3 text-center black-secondary">
            {{ formTitle }}
          </h2>
        </v-card-title>
        <v-card-text class="pt-0">
         
        </v-card-text>
      </v-card>
    </v-dialog> -->

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
      loading: false,
      debounce: 0,
      options: {},
      editedItem: {
        attendance_date: "",
        attendance_time: "",
        status: "",
        inscription_id: "",
        student_card: "",
        // group_name: "",
      },
      defaultItem: {
        attendance_date: "",
        attendance_time: "",
        status: "",
        inscription_id: "",
        student_card: "",
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
      },
    };
  },

  methods: {
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
      ];
      const responses = await Promise.all(requests).catch((error) => {
        alert.error("No fue posible obtener el registro.");
      });

      if (responses) {
        this.inscriptions = responses[1].data.data;
        this.groups = responses[2].data.data;
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

    // addRecord() {
    //   this.dialog = true;
    //   this.editedIndex = -1;
    //   this.editedItem = Object.assign({}, this.defaultItem);
    //   this.v$.$reset();
    // },

    // editItem(item) {
    //   this.editedIndex = this.records.indexOf(item);
    //   this.editedItem = Object.assign({}, item);
    //   this.dialog = true;
    // },

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