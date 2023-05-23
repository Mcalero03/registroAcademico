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
              <!-- subject_name  -->
              <v-col cols="12" sm="6" md="6">
                <v-label>Materia</v-label>
                <select
                  v-model="v$.editedItem.subject_name.$model"
                  @change="change"
                  class="form-select"
                >
                  <option
                    v-for="(option, index) in subjects"
                    :key="index"
                    :value="option.subject_name"
                  >
                    {{ option.subject_name }}
                  </option>
                </select>
              </v-col>
              <!-- subject_name  -->
              <!-- subject_code  -->
              <v-col cols="12" sm="6" md="6">
                <v-label>Código de materia</v-label>
                <select
                  v-model="v$.editedItem.subject_code.$model"
                  @change="change"
                  class="form-select"
                >
                  <option
                    v-for="(option, index) in codes"
                    :key="index"
                    :value="option.subject_code"
                  >
                    {{ option.subject_code }}
                  </option>
                </select>
              </v-col>
              <!-- subject_code  -->
              <!-- teacher  -->
              <v-col cols="12" sm="6" md="6">
                <v-label>Profesor</v-label>
                <select
                  v-model="v$.editedItem.teacher_full_name.$model"
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
              <!-- group_code  -->
              <v-col cols="12" sm="6" md="3" class="mt-2">
                <base-input
                  label="Código de grupo"
                  v-model="v$.editedItem.group_code.$model"
                  :rules="v$.editedItem.group_code"
                />
              </v-col>
              <!-- group_code  -->
              <!-- student_quantity  -->
              <v-col cols="12" sm="6" md="3" class="mt-2">
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

import groupApi from "@/services/groupApi";
import teacherApi from "@/services/teacherApi";
import subjectApi from "@/services/subjectApi";
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
      title: "GRUPO",
      headers: [
        { title: "GRUPO", key: "group_code" },
        { title: "CANTIDAD", key: "students_quantity" },
        { title: "PROFESOR", key: "teacher_full_name" },
        { title: "MATERIA", key: "subject_name" },
        { title: "CÓDIGO", key: "subject_code" },
        { title: "ACCIONES", key: "actions", sortable: false },
      ],
      total: 0,
      records: [],
      subjects: [],
      codes: [],
      teachers: [],
      loading: false,
      debounce: 0,
      options: {},
      editedItem: {
        group_code: "",
        students_quantity: "",
        teacher_full_name: "",
        subject_name: "",
        subject_code: "",
      },
      defaultItem: {
        group_code: "",
        students_quantity: "",
        teacher_full_name: "",
        subject_name: "",
        subject_code: "",
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
      },
    };
  },

  methods: {
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

    async initialize() {
      this.loading = true;
      this.records = [];

      let requests = [
        this.getDataFromApi(),
        teacherApi.get(null, { params: { itemsPerPage: -1 } }),
        subjectApi.get(null, { params: { itemsPerPage: -1 } }),
      ];
      const responses = await Promise.all(requests).catch((error) => {
        alert.error("No fue posible obtener el registro.");
      });

      if (responses) {
        this.teachers = responses[1].data.data;
        this.subjects = responses[2].data.data;
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
        this.initialize();
        return;
      }

      // Creating record
      try {
        const { data } = await groupApi.post(null, this.editedItem);
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