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
              <!-- program_name  -->
              <v-col cols="12" sm="6" md="8">
                <base-input
                  label="Nombre del programa"
                  v-model="v$.editedItem.program_name.$model"
                  :rules="v$.editedItem.program_name"
                />
              </v-col>
              <!-- program_name  -->
              <!-- cycle_quantity  -->
              <v-col cols="4" sm="3" md="2">
                <base-input
                  label="Ciclos"
                  v-model="v$.editedItem.cycle_quantity.$model"
                  :rules="v$.editedItem.cycle_quantity"
                  type="number"
                  max="10"
                  min="1"
                />
              </v-col>
              <!-- cycle_quantity  -->
              <!-- study_plan_year  -->
              <v-col cols="4" sm="3" md="2">
                <base-select
                  label="Plan de estudio"
                  :items="years"
                  item-title="index"
                  item-value="option"
                  v-model="v$.editedItem.study_plan_year.$model"
                  :rules="v$.editedItem.study_plan_year"
                />
              </v-col>
              <!-- study_plan_year  -->
              <!-- uv_total  -->
              <v-col cols="4" sm="4" md="4">
                <base-input
                  label="Total U.V"
                  v-model="v$.editedItem.uv_total.$model"
                  :rules="v$.editedItem.uv_total"
                  type="number"
                  min="1"
                  max="10"
                />
              </v-col>
              <!-- uv_total  -->
              <!-- required_subject  -->
              <v-col cols="6" sm="4" md="4">
                <base-input
                  label="Materias requeridas"
                  v-model="v$.editedItem.required_subject.$model"
                  :rules="v$.editedItem.required_subject"
                  type="number"
                  min="1"
                  max="50"
                />
              </v-col>
              <!-- required_subject  -->
              <!-- optional_subject  -->
              <v-col cols="6" sm="4" md="4">
                <base-input
                  label="Materias opcionales"
                  v-model="v$.editedItem.optional_subject.$model"
                  :rules="v$.editedItem.optional_subject"
                  type="number"
                  min="0"
                  max="50"
                />
              </v-col>
              <!-- optional_subject  -->
              <!-- study_plan_year  -->
              <!-- school  -->
              <v-col cols="12" sm="12" md="12">
                <v-label>Escuela</v-label>
                <base-select
                  :items="schools"
                  item-title="school_name"
                  item-value="school_name"
                  v-model="v$.editedItem.school_name.$model"
                  :rules="v$.editedItem.school_name"
                  @blur="showSubSchools"
                  v-if="editedIndex == -1"
                />
                <base-select
                  :items="schools"
                  item-title="school_name"
                  item-value="school_name"
                  v-model="v$.editedItem.school_name.$model"
                  :rules="v$.editedItem.school_name"
                  @blur="showSubSchools"
                  v-if="editedIndex != -1"
                  readonly
                />
              </v-col>
              <!-- school  -->
              <!-- sub_school_name  -->
              <v-col cols="12" sm="6" md="6">
                <v-label>Sub-escuela</v-label>
                <base-select
                  :items="subSchools"
                  item-title="sub_school_name"
                  item-value="sub_school_name"
                  v-model="v$.editedItem.sub_school_name.$model"
                  :rules="v$.editedItem.sub_school_name"
                  v-if="editedIndex == -1"
                />
                <base-select
                  :items="subSchools"
                  item-title="sub_school_name"
                  item-value="sub_school_name"
                  v-model="v$.editedItem.sub_school_name.$model"
                  :rules="v$.editedItem.sub_school_name"
                  v-if="editedIndex != -1"
                  readonly
                />
              </v-col>
              <!-- sub_school_name  -->
              <!-- pensum_type_name  -->
              <v-col cols="12" sm="6" md="6">
                <v-label>Tipo de pensum</v-label>
                <base-select
                  :items="pensumTypes"
                  item-title="pensum_type_name"
                  item-value="pensum_type_name"
                  v-model="v$.editedItem.pensum_type_name.$model"
                  :rules="v$.editedItem.pensum_type_name"
                />
              </v-col>
              <!-- pensum_type_name  -->
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

import pensumApi from "@/services/pensumApi";
import schoolApi from "@/services/schoolApi";
import pensumTypeApi from "@/services/pensumTypeApi";
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
      title: "PENSUMS",
      headers: [
        { title: "PROGRAMA", key: "program_name" },
        { title: "TOTAL U.V", key: "uv_total" },
        { title: "CICLOS", key: "cycle_quantity" },
        { title: "PLAN DE ESTUDIO", key: "study_plan_year" },
        { title: "SUB-ESCUELA", key: "sub_school_name" },
        { title: "TIPO DE PENSUM", key: "pensum_type_name" },
        { title: "ACCIONES", key: "actions", sortable: false },
      ],
      total: 0,
      records: [],
      schools: [],
      years: [],
      subSchools: [],
      pensumTypes: [],
      loading: false,
      debounce: 0,
      options: {},
      editedItem: {
        program_name: "",
        uv_total: "",
        required_subject: "",
        optional_subject: "",
        cycle_quantity: "",
        study_plan_year: "",
        sub_school_name: "",
        pensum_type_name: "",
        school_name: "",
      },
      defaultItem: {
        program_name: "",
        uv_total: "",
        required_subject: "",
        optional_subject: "",
        cycle_quantity: "",
        study_plan_year: "",
        sub_school_name: "",
        pensum_type_name: "",
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
        program_name: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(4)
          ),
        },
        uv_total: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(1)
          ),
          maxLength: (({ $params }) => maxLength($params), maxLength(2)),
        },
        required_subject: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(1)
          ),
          maxLength: (({ $params }) => maxLength($params), maxLength(2)),
        },
        optional_subject: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(1)
          ),
          maxLength: (({ $params }) => maxLength($params), maxLength(2)),
        },
        cycle_quantity: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(1)
          ),
          maxLength: helpers.withMessage(
            ({ $params }) => langMessages.maxLength($params),
            maxLength(2)
          ),
        },
        study_plan_year: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(4)
          ),
          maxLength: helpers.withMessage(
            ({ $params }) => langMessages.maxLength($params),
            maxLength(4)
          ),
        },
        school_name: {
          required: helpers.withMessage(langMessages.required, required),
        },
        sub_school_name: {
          required: helpers.withMessage(langMessages.required, required),
        },
        pensum_type_name: {
          required: helpers.withMessage(langMessages.required, required),
        },
      },
    };
  },

  methods: {
    async showSubSchools() {
      if (this.editedItem.school_name != "") {
        const { data } = await pensumApi
          .get("/showSubSchools/" + this.editedItem.school_name)
          .catch((error) => {
            alert.error(
              true,
              "No fue posible obtener la información de los espacios.",
              "fail"
            );
          });

        this.subSchools = data.sub_schools;
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
        pensumTypeApi.get(null, {
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
        this.pensumTypes = responses[2].data.data;
      }

      this.loading = false;
    },

    getDataFromApi(options) {
      this.loading = false;
      this.records = [];

      clearTimeout(this.debounce);
      this.debounce = setTimeout(async () => {
        try {
          const { data } = await pensumApi.get(null, {
            params: { ...options, search: this.search },
          });

          this.records = data.data;
          this.total = data.total;
          this.loading = false;
          this.years = data.years;
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
          const { data } = await pensumApi.put(`/${edited.id}`, edited);
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
        const { data } = await pensumApi.post(null, this.editedItem);
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
        const { data } = await pensumApi.delete(`/${this.editedItem.id}`, {
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