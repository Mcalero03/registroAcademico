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
              <!-- name  -->
              <v-col cols="12" sm="6" md="6">
                <base-input
                  label="Nombres del profesor"
                  v-model="v$.editedItem.name.$model"
                  :rules="v$.editedItem.name"
                />
              </v-col>
              <!-- name  -->
              <!-- last_name  -->
              <v-col cols="12" sm="6" md="6">
                <base-input
                  label="Apellidos del profesor"
                  v-model="v$.editedItem.last_name.$model"
                  :rules="v$.editedItem.last_name"
                />
              </v-col>
              <!-- last_name  -->
              <!-- teacher_card  -->
              <v-col cols="4" sm="3" md="2">
                <base-input
                  label="Carnet"
                  v-model="v$.editedItem.teacher_card.$model"
                  :rules="v$.editedItem.teacher_card"
                  type="number"
                />
              </v-col>
              <!-- teacher_card  -->
              <!-- nit  -->
              <v-col cols="7" sm="5" md="4">
                <base-input
                  label="NIT"
                  v-model="v$.editedItem.nit.$model"
                  :rules="v$.editedItem.nit"
                  type="number"
                />
              </v-col>
              <!-- nit  -->
              <!-- dui  -->
              <v-col cols="8" sm="4" md="3">
                <base-input
                  label="DUI"
                  v-model="v$.editedItem.dui.$model"
                  :rules="v$.editedItem.dui"
                  type="number"
                />
              </v-col>
              <!-- dui  -->
              <!-- phone_number  -->
              <v-col cols="5" sm="3" md="3">
                <base-input
                  label="Teléfono"
                  v-model="v$.editedItem.phone_number.$model"
                  :rules="v$.editedItem.phone_number"
                  type="number"
                />
              </v-col>
              <!-- phone_number  -->
              <!-- mails  -->
              <v-col cols="12" sm="5" md="6">
                <base-input
                  label="Correo electrónico"
                  v-model="v$.editedItem.mail.$model"
                  :rules="v$.editedItem.mail"
                />
              </v-col>
              <!-- mail  -->
              <!-- school  -->
              <v-col cols="12" sm="4" md="6">
                <base-select
                  label="Escuela"
                  :items="school"
                  item-title="school_name"
                  item-value="school_name"
                  v-model="v$.editedItem.school_name.$model"
                  :rules="v$.editedItem.school_name"
                >
                </base-select>
              </v-col>
              <!-- school  -->
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
import {
  helpers,
  minLength,
  required,
  email,
  maxLength,
} from "@vuelidate/validators";

import teacherApi from "@/services/teacherApi";
import schoolApi from "@/services/schoolApi";
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
      editedIndex: -1,
      title: "PROFESORES",
      headers: [
        { title: "NOMBRE", key: "name" },
        { title: "APELLIDO", key: "last_name" },
        { title: "CARNET", key: "teacher_card" },
        { title: "DUI", key: "dui" },
        { title: "CORREO", key: "mail" },
        { title: "ESCUELA", key: "school_name" },
        { title: "ACCIONES", key: "actions", sortable: false },
      ],
      total: 0,
      records: [],
      school: [],
      loading: false,
      debounce: 0,
      options: {},
      editedItem: {
        name: "",
        last_name: "",
        teacher_card: "",
        dui: "",
        nit: "",
        phone_number: "",
        mail: "",
        school_name: "",
      },
      defaultItem: {
        name: "",
        last_name: "",
        teacher_card: "",
        dui: "",
        nit: "",
        phone_number: "",
        mail: "",
        school_name: "",
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
        name: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(4)
          ),
        },
        last_name: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(4)
          ),
        },
        teacher_card: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(6)
          ),
          maxLength: (({ $params }) => maxLength($params), maxLength(6)),
        },
        dui: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(9)
          ),
          maxLength: (({ $params }) => maxLength($params), maxLength(9)),
        },
        nit: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(14)
          ),
          maxLength: (({ $params }) => maxLength($params), maxLength(14)),
        },
        phone_number: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(8)
          ),
          maxLength: (({ $params }) => maxLength($params), maxLength(8)),
        },
        mail: {
          required: helpers.withMessage(langMessages.required, required),
          email: helpers.withMessage(langMessages.email, email),
        },
        school_name: {
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
        this.school = responses[1].data.data;
      }

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
          const { data } = await teacherApi.put(`/${edited.id}`, edited);
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
        const { data } = await teacherApi.post(null, this.editedItem);
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
        const { data } = await teacherApi.delete(`/${this.editedItem.id}`, {
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