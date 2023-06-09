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
              <!-- municipality_name  -->
              <v-col cols="12" sm="6" md="6">
                <base-input
                  label="Nombre del municipio"
                  v-model="v$.editedItem.municipality.$model"
                  :rules="v$.editedItem.municipality"
                />
              </v-col>
              <!-- municipality_name  -->
              <!-- mun_min  -->
              <v-col cols="12" sm="6" md="6">
                <base-input
                  label="Municipio en minúsculas"
                  v-model="v$.editedItem.mun_min.$model"
                  :rules="v$.editedItem.mun_min"
                />
              </v-col>
              <!-- mun_min  -->
              <!-- mun_may  -->
              <v-col cols="12" sm="6" md="6">
                <base-input
                  label="Municipio en mayúsculas"
                  v-model="v$.editedItem.mun_may.$model"
                  :rules="v$.editedItem.mun_may"
                />
              </v-col>
              <!-- mun_may  -->
              <!-- dm_cod  -->
              <v-col cols="12" sm="6" md="6">
                <base-input
                  label="Código departamento y municipio"
                  v-model="v$.editedItem.dm_cod.$model"
                  :rules="v$.editedItem.dm_cod"
                />
              </v-col>
              <!-- dm_cod  -->
              <!-- cod_mun  -->
              <v-col cols="12" sm="6" md="6">
                <base-input
                  label="Código de municipio"
                  v-model="v$.editedItem.cod_mun.$model"
                  :rules="v$.editedItem.cod_mun"
                />
              </v-col>
              <!-- cod_mun  -->
              <!-- department_name  -->
              <v-col cols="12" sm="6" md="6">
                <base-select
                  label="Departamento"
                  :items="depas"
                  item-title="department_name"
                  item-value="department_name"
                  v-model="v$.editedItem.department_name.$model"
                  :rules="v$.editedItem.department_name"
                />
              </v-col>
              <!-- department_name  -->
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
import { helpers, minLength, required, maxLength } from "@vuelidate/validators";

import departmentApi from "@/services/departmentApi";
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
      editedIndex: -1,
      title: "MUNICIPIOS",
      headers: [
        { title: "MUNICIPIO", key: "municipality" },
        // { title: "MIN MUN", key: "mun_min" },
        // { title: "MAY MUN", key: "mun_may" },
        { title: "DM COD", key: "dm_cod" },
        { title: "COD MUN", key: "cod_mun" },
        { title: "DEPARTAMENTO", key: "department_name" },
        { title: "ACCIONES", key: "actions", sortable: false },
      ],
      total: 0,
      records: [],
      depas: [],
      loading: false,
      debounce: 0,
      options: {},
      editedItem: {
        municipality: "",
        mun_min: "",
        mun_may: "",
        dm_cod: "",
        cod_mun: "",
        department_name: "",
      },
      defaultItem: {
        municipality: "",
        mun_min: "",
        mun_may: "",
        dm_cod: "",
        cod_mun: "",
        department_name: "",
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
        municipality: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(4)
          ),
        },
        mun_min: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(4)
          ),
        },
        mun_may: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(4)
          ),
        },
        dm_cod: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(4)
          ),
          maxLength: (({ $params }) => maxLength($params), maxLength(4)),
        },
        cod_mun: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(2)
          ),
          maxLength: (({ $params }) => maxLength($params), maxLength(2)),
        },
        department_name: {
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
        departmentApi.get(null, {
          params: {
            itemsPerPage: -1,
          },
        }),
      ];
      const responses = await Promise.all(requests).catch((error) => {
        alert.error("No fue posible obtener el registro.");
      });

      if (responses) {
        this.depas = responses[1].data.data;
      }

      this.loading = false;
    },

    getDataFromApi(options) {
      this.loading = false;
      this.records = [];

      clearTimeout(this.debounce);
      this.debounce = setTimeout(async () => {
        try {
          const { data } = await municipalityApi.get(null, {
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
          const { data } = await municipalityApi.put(`/${edited.id}`, edited);
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
        const { data } = await municipalityApi.post(null, this.editedItem);
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
        const { data } = await municipalityApi.delete(
          `/${this.editedItem.id}`,
          {
            params: { id: this.editedItem.id },
          }
        );

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