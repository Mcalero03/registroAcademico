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
            class="mr-1"
            @click="editItem(item.raw)"
            icon="mdi-pencil"
            v-if="item.raw.prerequisite != 'Con prerrequisito'"
          />
          <v-icon
            size="20"
            class="mr-1"
            @click="editItem(item.raw)"
            icon="mdi-format-list-bulleted"
            v-if="item.raw.prerequisite == 'Con prerrequisito'"
          />
          <v-icon
            size="20"
            class="mr-1"
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
                <base-input
                  label="Nombre de la materia"
                  v-model="v$.editedItem.subject_name.$model"
                  :rules="v$.editedItem.subject_name"
                />
              </v-col>
              <!-- subject_name  -->
              <!-- subject_code  -->
              <v-col cols="12" sm="6" md="6">
                <base-input
                  label="Código de la materia"
                  v-model="v$.editedItem.subject_code.$model"
                  :rules="v$.editedItem.subject_code"
                />
              </v-col>
              <!-- subject_code  -->
              <!-- average_approval  -->
              <v-col cols="6" sm="6" md="6">
                <base-input
                  label="Promedio de aprobación"
                  v-model="v$.editedItem.average_approval.$model"
                  :rules="v$.editedItem.average_approval"
                  type="number"
                  step=".10"
                  min="0"
                  max="10"
                />
              </v-col>
              <!-- average_approval  -->
              <!-- units_value  -->
              <v-col cols="6" sm="6" md="6">
                <base-input
                  label="Unidades valorativas"
                  v-model="v$.editedItem.units_value.$model"
                  :rules="v$.editedItem.units_value"
                  type="number"
                  min="1"
                  max="10"
                />
              </v-col>
              <!-- units_value  -->
              <!-- status  -->
              <v-col cols="6" sm="6" md="6">
                <base-select
                  label="Estado"
                  :items="status"
                  v-model="v$.editedItem.prerequisite.$model"
                  :rules="v$.editedItem.prerequisite"
                  readonly
                  v-if="editedIndex != -1"
                >
                </base-select>
                <base-select
                  label="Estado"
                  :items="status"
                  v-model="v$.editedItem.prerequisite.$model"
                  :rules="v$.editedItem.prerequisite"
                  v-if="editedIndex == -1"
                >
                </base-select>
              </v-col>
              <!-- status  -->
              <!-- program_name  -->
              <v-col cols="6" sm="6" md="6">
                <base-select
                  label="Programa"
                  :items="pensums"
                  item-title="program_name"
                  item-value="program_name"
                  v-model="v$.editedItem.program_name.$model"
                  :rules="v$.editedItem.program_name"
                  readonly
                  v-if="editedIndex != -1"
                >
                </base-select>
                <base-select
                  label="Programa"
                  :items="pensums"
                  item-title="program_name"
                  item-value="program_name"
                  v-model="v$.editedItem.program_name.$model"
                  :rules="v$.editedItem.program_name"
                  v-else-if="editedIndex == -1"
                  @change="change"
                >
                  >
                </base-select>
              </v-col>
              <!-- program_name  -->
              <!-- prerequisite -->
              <v-col
                class="pt-5 pb-5 mt-2"
                v-if="
                  editedIndex != -1 &&
                  editedItem.prerequisite == 'Con prerrequisito'
                "
              >
                <base-button
                  type="primary"
                  title="Agregar prerequisito"
                  @click="addPrerequisite()"
                />
              </v-col>
            </v-row>
            <!-- prerequisite -->
            <!-- Prerequisite Table -->
            <v-row
              v-if="
                editedIndex != -1 &&
                editedItem.prerequisite == 'Con prerrequisito'
              "
            >
              <v-col align="center" cols="12" md="12" sm="12" class="pt-4">
                <div class="table-responsive-md">
                  <v-table>
                    <thead>
                      <tr>
                        <th>MATERIA</th>
                        <th class="text-center">ACCIÓN</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(
                          prerequisite, index
                        ) in editedItem.prerequisites"
                        v-bind:index="index"
                        :key="index"
                      >
                        <td v-text="prerequisite.prerequisite"></td>
                        <td class="text-center">
                          <v-icon
                            size="20"
                            class="mr-2"
                            @click="deletePrerequisite(index)"
                            icon="mdi-delete"
                          />
                        </td>
                      </tr>
                      <tr v-if="editedItem.prerequisites.length == 0">
                        <td colspan="5" class="text-center pt-3">
                          <p>No se ha asignado ningún prerequisito</p>
                        </td>
                      </tr>
                    </tbody>
                  </v-table>
                </div>
                <!-- Modal -->
                <v-dialog
                  v-model="dialogPrerequisite"
                  max-width="600px"
                  persistent
                >
                  <v-card height="100%">
                    <!-- Container when there are no prerequisites available  -->
                    <v-container v-if="pensumSubject == 0">
                      <h2 class="black-secondary text-center mt-4 mb-4">
                        No hay materias para asignar como prerrequisito!
                      </h2>
                      <v-row>
                        <v-col align="center"
                          ><base-button
                            class="ms-1"
                            type="secondary"
                            title="Cancelar"
                            @click="closePrerequisiteDialog()"
                        /></v-col> </v-row
                    ></v-container>
                    <!-- Container when there are no prerequisites available  -->

                    <!-- Container when there are prerequisites available  -->
                    <v-container v-if="pensumSubject != 0">
                      <h2 class="black-secondary text-center mt-4 mb-4">
                        Agregar prerequisito
                      </h2>
                      <v-row>
                        <!-- subject_name -->
                        <v-col cols="12" sm="12" md="12">
                          <base-select
                            label="Materia prerequisito"
                            :items="pensumSubject"
                            item-title="subject_name"
                            item-value="subject_name"
                            v-model="v$.prerequisite.prerequisite.$model"
                            :rules="v$.prerequisite.prerequisite"
                          >
                          </base-select>
                        </v-col>
                        <!-- subject_name -->
                      </v-row>
                      <v-row>
                        <v-col align="center">
                          <base-button
                            type="primary"
                            title="Agregar"
                            @click="addNewPrerequisite()"
                          />
                          <base-button
                            class="ms-1"
                            type="secondary"
                            title="Cancelar"
                            @click="closePrerequisiteDialog()"
                          />
                        </v-col>
                      </v-row>
                    </v-container>
                    <!-- Container when there are prerequisites available  -->
                  </v-card>
                </v-dialog>
                <!-- Modal -->
              </v-col>
            </v-row>
            <!-- Prerequisite Table -->
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

import subjectApi from "@/services/subjectApi";
import pensumApi from "@/services/pensumApi";
import pensumSubjectDetailApi from "@/services/pensumSubjectDetailApi";
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
      dialogPrerequisite: false,
      editedIndex: -1,
      title: "MATERIA",
      headers: [
        { title: "MATERIA", key: "subject_name" },
        { title: "CÓDIGO", key: "subject_code" },
        { title: "PROMEDIO", key: "average_approval" },
        { title: "U.V", key: "units_value" },
        { title: "PRERREQUISITO", key: "prerequisite" },
        { title: "PENSUM", key: "program_name" },
        { title: "ACCIONES", key: "actions", sortable: false },
      ],
      total: 0,
      records: [],
      pensums: [],
      pensumSubject: [],
      status: ["Sin prerrequisito", "Con prerrequisito"],
      loading: false,
      debounce: 0,
      options: {},
      editedItem: {
        subject_name: "",
        subject_code: "",
        average_approval: "",
        units_value: "",
        program_name: "",
        prerequisite: "",
        prerequisites: [],
      },
      defaultItem: {
        subject_name: "",
        subject_code: "",
        average_approval: "",
        units_value: "",
        prerequisite: "",
        program_name: "",
        prerequisites: [],
      },
      // pensumSubjectDetail: {
      //   program_name: "",
      // },
      prerequisite: {
        prerequisite: "",
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
    dialogPrerequisite(val) {
      val || this.closePrerequisiteDialog();
    },
  },

  validations() {
    return {
      editedItem: {
        subject_name: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(4)
          ),
        },
        average_approval: {
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
        units_value: {
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
        subject_code: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(4)
          ),
          maxLength: helpers.withMessage(
            ({ $params }) => langMessages.maxLength($params),
            maxLength(8)
          ),
        },
        prerequisite: {
          required: helpers.withMessage(langMessages.required, required),
        },
        program_name: {
          required: helpers.withMessage(langMessages.required, required),
        },
      },
      prerequisite: {
        prerequisite: {},
      },
    };
  },

  methods: {
    async change() {
      const { data } = await pensumSubjectDetailApi
        .get(
          "/byPensum/" +
            this.v$.editedItem.program_name.$model +
            "/" +
            this.v$.editedItem.subject_name.$model
        )
        .catch((error) => {
          alert.error(true, "No fue posible obtener la información.", "fail");
        });

      this.pensumSubject = data.subject;
      console.log(this.pensumSubject);
      console.log(this.v$.editedItem.program_name.$model);
      console.log(this.v$.editedItem.subject_name.$model);
    },

    async initialize() {
      this.loading = true;
      this.records = [];

      let requests = [
        this.getDataFromApi(),
        pensumApi.get(null, {
          params: {
            itemsPerPage: -1,
          },
        }),
      ];
      const responses = await Promise.all(requests).catch((error) => {
        alert.error("No fue posible obtener el registro.");
      });

      if (responses) {
        this.pensums = responses[1].data.data;
      }

      this.loading = false;
    },

    getDataFromApi(options) {
      this.loading = false;
      this.records = [];

      clearTimeout(this.debounce);
      this.debounce = setTimeout(async () => {
        try {
          const { data } = await subjectApi.get(null, {
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

    addPrerequisite() {
      this.dialogPrerequisite = true;
      this.editedPrerequisite = -1;
      this.v$.prerequisite.prerequisite.$model = "";
      this.v$.prerequisite.$reset();
    },

    async addNewPrerequisite() {
      this.v$.prerequisite.$validate();
      if (this.v$.prerequisite.$invalid) {
        alert.error("Campo obligatorio");
        return;
      }

      // Creating record
      try {
        this.editedItem.prerequisites.push({ ...this.prerequisite });
      } catch (error) {
        alert.error("No fue posible crear el registro.");
      }

      this.closePrerequisiteDialog();
      this.initialize();
      this.loading = false;
      return;
    },

    closePrerequisiteDialog() {
      this.v$.prerequisite.$reset();
      this.dialogPrerequisite = false;
      this.editedPrerequisite = -1;
    },

    async deletePrerequisite(index) {
      this.editedItem.prerequisites.splice(index, 1);
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
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
          const { data } = await subjectApi.put(`/${edited.id}`, edited);
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
        const { data } = await subjectApi.post(null, this.editedItem);
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
        const { data } = await subjectApi.delete(`/${this.editedItem.id}`, {
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