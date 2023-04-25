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
              <!-- name  -->
              <v-col cols="12" sm="12" md="6">
                <base-input
                  label="Nombres del estudiante"
                  v-model="v$.editedItem.name.$model"
                  :rules="v$.editedItem.name"
                />
              </v-col>
              <!-- name  -->
              <!-- last_name  -->
              <v-col cols="12" sm="12" md="6">
                <base-input
                  label="Apellidos del estudiante"
                  v-model="v$.editedItem.last_name.$model"
                  :rules="v$.editedItem.last_name"
                />
              </v-col>
              <!-- last_name  -->
              <!-- age  -->
              <v-col cols="12" sm="12" md="6">
                <base-input
                  label="Edad"
                  v-model="v$.editedItem.age.$model"
                  :rules="v$.editedItem.age"
                  type="number"
                />
              </v-col>
              <!-- age  -->
              <!-- card  -->
              <v-col cols="12" sm="12" md="6">
                <base-input
                  label="Carnet"
                  v-model="v$.editedItem.card.$model"
                  :rules="v$.editedItem.card"
                />
              </v-col>
              <!-- card  -->
              <!-- nie  -->
              <v-col cols="12" sm="12" md="6">
                <base-input
                  label="NIE"
                  v-model="v$.editedItem.nie.$model"
                  :rules="v$.editedItem.nie"
                />
              </v-col>
              <!-- nie  -->
              <!-- phone_number  -->
              <v-col cols="12" sm="12" md="6">
                <base-input
                  label="Número de teléfono"
                  v-model="v$.editedItem.phone_number.$model"
                  :rules="v$.editedItem.phone_number"
                  type="number"
                />
              </v-col>
              <!-- phone_number  -->
              <!-- mail  -->
              <v-col cols="12" sm="12" md="6">
                <base-input
                  label="Correo"
                  v-model="v$.editedItem.mail.$model"
                  :rules="v$.editedItem.mail"
                />
              </v-col>
              <!-- mail  -->
              <!-- admission_date  -->
              <v-col cols="12" sm="12" md="6">
                <base-input
                  label="Fecha de ingreso"
                  v-model="v$.editedItem.admission_date.$model"
                  :rules="v$.editedItem.admission_date"
                  type="date"
                />
              </v-col>
              <!-- admission_date  -->
              <!-- municipality_name  -->
              <v-col cols="12" sm="12" md="6">
                <base-select
                  label="Municipio de residencia"
                  :items="municipalities"
                  item-title="municipality_name"
                  item-value="municipality_name"
                  v-model="v$.editedItem.municipality_name.$model"
                  :rules="v$.editedItem.municipality_name"
                />
              </v-col>
              <!-- municipality_name  -->
              <!-- relative_name  -->
              <v-col cols="12" sm="12" md="6">
                <base-select
                  label="Nombre del pariente"
                  :items="relatives"
                  item-title="name"
                  item-value="name"
                  v-model="v$.editedItem.relative_name.$model"
                  :rules="v$.editedItem.relative_name"
                />
              </v-col>
              <!-- relative_name  -->
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

import studentApi from "@/services/studentApi";
import relativeApi from "@/services/relativeApi";
import municipalityApi from "@/services/municipalityApi";
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
      title: "ESTUDIANTE",
      headers: [
        { title: "NOMBRES", key: "name" },
        { title: "APELLIDOS", key: "last_name" },
        { title: "EDAD ", key: "age" },
        { title: "CARNET", key: "card" },
        { title: "NIE", key: "nie" },
        { title: "TELÉFONO ", key: "phone_number" },
        { title: "CORREO", key: "mail" },
        { title: "FECHA INGRESO", key: "admission_date" },
        { title: "MUNICIPIO ", key: "municipality_name" },
        { title: "PARIENTE ", key: "relative_name" },
        { title: "ACCIONES", key: "actions", sortable: false },
      ],
      total: 0,
      records: [],
      municipalities: [],
      relatives: [],
      loading: false,
      debounce: 0,
      options: {},
      editedItem: {
        name: "",
        last_name: "",
        age: "",
        card: "",
        nie: "",
        phone_number: "",
        mail: "",
        admission_date: "",
        municipality_name: "",
        relative_name: "",
      },
      defaultItem: {
        name: "",
        last_name: "",
        age: "",
        card: "",
        nie: "",
        phone_number: "",
        mail: "",
        admission_date: "",
        municipality_name: "",
        relative_name: "",
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
            minLength(3)
          ),
        },
        last_name: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(3)
          ),
        },
        age: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(1)
          ),
          maxLength: (({ $params }) => maxLength($params), maxLength(2)),
        },
        card: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(4)
          ),
          maxLength: (({ $params }) => maxLength($params), maxLength(4)),
        },
        nie: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(7)
          ),
          maxLength: (({ $params }) => maxLength($params), maxLength(7)),
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
        admission_date: {
          required: helpers.withMessage(langMessages.required, required),
        },
        municipality_name: {
          required: helpers.withMessage(langMessages.required, required),
        },
        relative_name: {
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
        municipalityApi.get(null, {
          params: {
            itemsPerPage: -1,
          },
        }),
        relativeApi.get(null, {
          params: {
            itemsPerPage: -1,
          },
        }),
      ];
      const responses = await Promise.all(requests).catch((error) => {
        alert.error("No fue posible obtener el registro.");
      });

      if (responses) {
        this.municipalities = responses[1].data.data;
        this.relatives = responses[2].data.data;
      }

      this.loading = false;
    },

    getDataFromApi(options) {
      this.loading = false;
      this.records = [];

      clearTimeout(this.debounce);
      this.debounce = setTimeout(async () => {
        try {
          const { data } = await studentApi.get(null, {
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
          const { data } = await studentApi.put(`/${edited.id}`, edited);
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
        const { data } = await studentApi.post(null, this.editedItem);
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
        const { data } = await studentApi.delete(`/${this.editedItem.id}`, {
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