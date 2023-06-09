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
              <!-- cycle_number  -->
              <v-col cols="6" sm="3" md="3">
                <base-input
                  label="Número de ciclo"
                  v-model="v$.editedItem.cycle_number.$model"
                  :rules="v$.editedItem.cycle_number"
                  type="number"
                  min="1"
                />
              </v-col>
              <!-- cycle_number  -->
              <!-- year  -->
              <v-col cols="6" sm="3" md="3">
                <base-input
                  label="Año lectivo"
                  v-model="v$.editedItem.year.$model"
                  :rules="v$.editedItem.year"
                  type="number"
                  min="1900"
                  max="2099"
                />
              </v-col>
              <!-- year  -->
              <!-- start_date  -->
              <v-col cols="12" sm="6" md="6">
                <base-input
                  label="Fecha de inicio"
                  v-model="v$.editedItem.start_date.$model"
                  :rules="v$.editedItem.start_date"
                  type="date"
                />
              </v-col>
              <!-- start_date  -->
              <!-- end_date  -->
              <v-col cols="12" sm="6" md="6">
                <base-input
                  label="Fecha de finalización"
                  v-model="v$.editedItem.end_date.$model"
                  :rules="v$.editedItem.end_date"
                  type="date"
                />
              </v-col>
              <!-- end_date  -->
              <!-- status  -->
              <v-col cols="12" sm="6" md="6">
                <base-select
                  label="Estado del ciclo"
                  :items="status"
                  item-title="status"
                  item-value="status"
                  v-model="v$.editedItem.status.$model"
                  :rules="v$.editedItem.status"
                />
              </v-col>
              <!-- status  -->
              <!-- school  -->
              <v-col cols="12" sm="6" md="6" v-if="editedIndex == -1">
                <v-label>Escuela</v-label>
                <base-select
                  :items="schools"
                  item-title="school_name"
                  item-value="school_name"
                  v-model="v$.editedItem.school.$model"
                  :rules="v$.editedItem.school"
                  @blur="changePensum"
                />
              </v-col>
              <!-- school  -->
              <!-- pensum  -->
              <v-col cols="12" sm="6" md="6" v-if="editedIndex == -1">
                <v-label>Pensum</v-label>
                <base-select
                  :items="pensums"
                  item-title="program_name"
                  item-value="program_name"
                  v-model="v$.editedItem.pensum.$model"
                  :rules="v$.editedItem.pensum"
                  @blur="changeSubject"
                />
              </v-col>
              <!-- pensum  -->
            </v-row>
            <!-- Subject table -->
            <v-row>
              <v-col align="center" cols="6" md="6" sm="6"
                ><v-data-table
                  v-if="editedIndex == -1"
                  v-model="this.editedItem.subjects"
                  :headers="headersSubject"
                  :items="subjects"
                  items-title="subject_name"
                  item-value="subject_name"
                  show-select
                  density="compact"
                  class="elevation-1"
                  items-per-page="3"
                ></v-data-table
              ></v-col>
              <v-col
                align="center"
                cols="12"
                md="12"
                sm="12"
                v-if="editedIndex != -1"
              >
                <v-data-table
                  :headers="headersSubjectSelected"
                  v-model="this.editedItem.subjects"
                  :items="editedItem.subjects"
                  items-title="subject_name"
                  item-value="subject_name"
                  density="compact"
                  class="elevation-1"
                  items-per-page="10"
                >
                  <template v-slot:[`item.actions`]="{ index }">
                    <v-icon
                      size="20"
                      class="mr-2"
                      @click="deleteSubjetct(index)"
                      icon="mdi-delete"
                    />
                  </template>
                </v-data-table>
              </v-col>
              <v-col
                align="center"
                cols="6"
                md="6"
                sm="6"
                v-if="editedIndex == -1"
              >
                <v-data-table
                  :headers="headerSelected"
                  :items="selected"
                  items-title="subject_name"
                  item-value="subject_name"
                  density="compact"
                  class="elevation-1"
                  items-per-page="4"
                >
                </v-data-table>
              </v-col>
            </v-row>
            <!-- Subject table -->
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

import cycleApi from "@/services/cycleApi";
import schoolApi from "@/services/schoolApi";
import subjectApi from "@/services/subjectApi";
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
      title: "CICLOS",
      headers: [
        { title: "CICLO", key: "cycle_number" },
        { title: "AÑO", key: "year" },
        { title: "INICIO", key: "start_date" },
        { title: "FIN", key: "end_date" },
        { title: "ESTADO", key: "status" },
        { title: "ACCIONES", key: "actions", sortable: false },
      ],
      headersSubject: [{ title: "SELECCIONE MATERIAS", key: "subject_name" }],
      headerSelected: [
        { title: "MATERIAS SELECCIONADAS", key: "subject_name" },
      ],
      headersSubjectSelected: [
        { title: "MATERIAS SELECCIONADAS", key: "subject_name" },
        { title: "ACCIONES", key: "actions", sortable: false },
      ],
      total: 0,
      records: [],
      schools: [],
      pensums: [],
      selected: [],
      subjects: [],
      loading: false,
      debounce: 0,
      status: ["Activo", "Inactivo", "Finalizado"],
      options: {},
      editedItem: {
        cycle_number: "",
        year: "",
        start_date: "",
        end_date: "",
        status: "",
        school: "",
        pensum: "",
        subjects: [],
      },
      defaultItem: {
        cycle_number: "",
        year: "",
        start_date: "",
        end_date: "",
        status: "",
        school: "",
        pensum: "",
        subjects: [],
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

    "editedItem.subjects"(newSubjects) {
      const selectedSubjects = newSubjects.map((subject) => {
        return { ["subject_name"]: subject };
      });

      this.selected = selectedSubjects;
    },
  },

  validations() {
    return {
      editedItem: {
        cycle_number: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(1)
          ),
          maxLength: (({ $params }) => maxLength($params), maxLength(1)),
        },
        year: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(4)
          ),
          maxLength: (({ $params }) => maxLength($params), maxLength(4)),
        },
        start_date: {
          required: helpers.withMessage(langMessages.required, required),
        },
        end_date: {
          required: helpers.withMessage(langMessages.required, required),
        },
        status: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(4)
          ),
        },
        school: {
          // required: helpers.withMessage(langMessages.required, required),
        },
        pensum: {
          // required: helpers.withMessage(langMessages.required, required),
        },
      },
    };
  },

  methods: {
    //METHODS TO CHANGE
    async changePensum() {
      const { data } = await cycleApi
        .get("/bySchool/" + this.v$.editedItem.school.$model)
        .catch((error) => {
          alert.error(
            true,
            "No fue posible obtener la información de los espacios.",
            "fail"
          );
        });

      this.pensums = data.pensum;
    },
    async changeSubject() {
      const { data } = await cycleApi
        .get("/byPensum/" + this.v$.editedItem.pensum.$model)
        .catch((error) => {
          alert.error(
            true,
            "No fue posible obtener la información de los espacios.",
            "fail"
          );
        });

      this.subjects = data.subject;
    },

    async deleteSubjetct(index) {
      this.editedItem.subjects.splice(index, 1);
    },

    async initialize() {
      this.loading = true;
      this.records = [];

      let requests = [
        this.getDataFromApi(),
        subjectApi.get(null, {
          params: {
            itemsPerPage: -1,
          },
        }),
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
        this.schools = responses[2].data.data;
      }

      this.loading = false;
    },

    getDataFromApi(options) {
      this.loading = false;
      this.records = [];

      clearTimeout(this.debounce);
      this.debounce = setTimeout(async () => {
        try {
          const { data } = await cycleApi.get(null, {
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
        this.subjects = [];
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
          const { data } = await cycleApi.put(`/${edited.id}`, edited);
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
        const { data } = await cycleApi.post(null, this.editedItem);
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
        const { data } = await cycleApi.delete(`/${this.editedItem.id}`, {
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