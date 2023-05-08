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
              <v-col cols="12" sm="6" md="6">
                <base-input
                  label="Nombres del estudiante"
                  v-model="v$.editedItem.name.$model"
                  :rules="v$.editedItem.name"
                />
              </v-col>
              <!-- name  -->
              <!-- last_name  -->
              <v-col cols="12" sm="6" md="6">
                <base-input
                  label="Apellidos del estudiante"
                  v-model="v$.editedItem.last_name.$model"
                  :rules="v$.editedItem.last_name"
                />
              </v-col>
              <!-- last_name  -->
              <!-- age  -->
              <v-col cols="6" sm="3" md="3">
                <base-input
                  label="Edad"
                  v-model="v$.editedItem.age.$model"
                  :rules="v$.editedItem.age"
                  type="number"
                  min="1"
                />
              </v-col>
              <!-- age  -->
              <!-- card  -->
              <v-col cols="6" sm="3" md="3">
                <base-input
                  label="Carnet"
                  v-model="v$.editedItem.card.$model"
                  :rules="v$.editedItem.card"
                  type="number"
                  min="0"
                />
              </v-col>
              <!-- card  -->
              <!-- nie  -->
              <v-col cols="6" sm="3" md="3">
                <base-input
                  label="NIE"
                  v-model="v$.editedItem.nie.$model"
                  :rules="v$.editedItem.nie"
                  type="number"
                  min="0"
                />
              </v-col>
              <!-- nie  -->
              <!-- phone_number  -->
              <v-col cols="6" sm="3" md="3">
                <base-input
                  label="Teléfono"
                  v-model="v$.editedItem.phone_number.$model"
                  :rules="v$.editedItem.phone_number"
                  type="number"
                />
              </v-col>
              <!-- phone_number  -->
              <!-- mail  -->
              <v-col cols="12" sm="6" md="6">
                <base-input
                  label="Correo"
                  v-model="v$.editedItem.mail.$model"
                  :rules="v$.editedItem.mail"
                />
              </v-col>
              <!-- mail  -->
              <!-- admission_date  -->
              <v-col cols="12" sm="6" md="6">
                <base-input
                  label="Fecha de ingreso"
                  v-model="v$.editedItem.admission_date.$model"
                  :rules="v$.editedItem.admission_date"
                  type="date"
                >
                </base-input>
              </v-col>
              <!-- admission_date  -->
              <!-- municipality_name  -->
              <v-col cols="12" sm="6" md="6">
                <base-select
                  label="Municipio de residencia"
                  :items="municipalities"
                  item-title="municipality_name"
                  item-value="municipality_name"
                  v-model="v$.editedItem.municipality_name.$model"
                  :rules="v$.editedItem.municipality_name"
                >
                </base-select>
              </v-col>
              <!-- municipality_name  -->
              <!-- relative -->
              <v-col class="pt-5 pb-5 mt-2">
                <base-button
                  type="primary"
                  title="Agregar pariente"
                  @click="addRelative()"
                />
              </v-col>
            </v-row>
            <!-- relative -->
            <!-- Relative Table -->
            <v-row>
              <v-col align="center" cols="12" md="12" sm="12" class="pt-4">
                <div class="table-responsive-md">
                  <v-table>
                    <thead>
                      <tr>
                        <th>NOMBRE</th>
                        <th>APELLIDO</th>
                        <th>DUI</th>
                        <th>TELÉFONO</th>
                        <!-- <th>CORREO</th> -->
                        <th>PARENTESCO</th>
                        <th class="text-center">ACCIÓN</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(relative, index) in editedItem.relatives"
                        v-bind:index="index"
                        :key="index"
                      >
                        <td v-text="relative.name"></td>
                        <td v-text="relative.last_name"></td>
                        <td v-text="relative.dui"></td>
                        <td v-text="relative.phone_number"></td>
                        <td v-text="relative.kinship"></td>
                        <td class="text-center">
                          <v-icon
                            size="20"
                            class="mr-2"
                            @click="deleteRelative(index)"
                            icon="mdi-delete"
                          />
                          <!-- @click="deleteRelative(relative.id, index)" -->
                        </td>
                      </tr>
                      <tr v-if="editedItem.relatives.length == 0">
                        <td colspan="5" class="text-center pt-3">
                          <p>No se ha ingresado ningún pariente</p>
                        </td>
                      </tr>
                    </tbody>
                  </v-table>
                </div>
                <!-- Modal -->
                <v-dialog v-model="dialogRelative" max-width="600px" persistent>
                  <v-card height="100%">
                    <v-container>
                      <h2 class="black-secondary text-center mt-4 mb-4">
                        Agregar pariente
                      </h2>
                      <v-row>
                        <!-- name  -->
                        <v-col cols="6" sm="6" md="6">
                          <base-input
                            label="Nombres"
                            v-model="v$.relative.name.$model"
                            :rules="v$.relative.name"
                          />
                        </v-col>
                        <!-- name  -->
                        <!-- last_name  -->
                        <v-col cols="6" sm="6" md="6">
                          <base-input
                            label="Apellidos"
                            v-model="v$.relative.last_name.$model"
                            :rules="v$.relative.last_name"
                          />
                        </v-col>
                        <!-- last_name  -->
                        <!-- dui  -->
                        <v-col cols="6" sm="6" md="6">
                          <base-input
                            label="DUI"
                            v-model="v$.relative.dui.$model"
                            :rules="v$.relative.dui"
                            type="number"
                          />
                        </v-col>
                        <!-- dui  -->
                        <!-- phone_number  -->
                        <v-col cols="6" sm="6" md="6">
                          <base-input
                            label="Teléfono"
                            v-model="v$.relative.phone_number.$model"
                            :rules="v$.relative.phone_number"
                            type="number"
                          />
                        </v-col>
                        <!-- phone_number  -->
                        <!-- mail  -->
                        <v-col cols="6" sm="6" md="6">
                          <base-input
                            label="Correo electrónico"
                            v-model="v$.relative.mail.$model"
                            :rules="v$.relative.mail"
                          />
                        </v-col>
                        <!-- mail  -->
                        <!-- kinship -->
                        <v-col cols="6" sm="6" md="6">
                          <base-select
                            label="Parentesco"
                            :items="kinship"
                            item-title="kinship"
                            item-value="kinship"
                            v-model="v$.relative.kinship.$model"
                            :rules="v$.relative.kinship"
                          >
                          </base-select>
                        </v-col>
                        <!-- kinship -->
                      </v-row>
                      <v-row>
                        <v-col align="center">
                          <base-button
                            type="primary"
                            title="Agregar"
                            @click="addNewRelative()"
                          />
                          <base-button
                            class="ms-1"
                            type="secondary"
                            title="Cancelar"
                            @click="closeRelativeDialog()"
                          />
                        </v-col>
                      </v-row>
                    </v-container>
                  </v-card>
                </v-dialog>
                <!-- Modal -->
              </v-col>
            </v-row>
            <!-- Relative Table -->

            <!-- Form -->
            <v-row>
              <v-col align="center">
                <base-button
                  type="primary"
                  title="Guardar"
                  @click="save"
                  :disable="loading != false"
                />
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
import municipalityApi from "@/services/municipalityApi";
import kinshipApi from "@/services/kinshipApi";
import BaseButton from "../components/base-components/BaseButton.vue";
import BaseInput from "../components/base-components/BaseInput.vue";
import BaseSelect from "../components/base-components/BaseSelect.vue";

import useAlert from "../composables/useAlert";

const { alert } = useAlert();
const langMessages = messages["en"].validations;

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
      dialogRelative: false,
      editedIndex: -1,
      editedRelative: -1,
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
        { title: "ACCIONES", key: "actions", sortable: false },
      ],
      total: 0,
      records: [],
      municipalities: [],
      kinship: [],
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
        relatives: [],
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
        relatives: [],
      },
      relative: {
        name: "",
        last_name: "",
        dui: "",
        phone_number: "",
        mail: "",
        kinship: "",
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
    dialogRelativeTitle() {
      return this.editedRelative === -1 ? "Nuevo registro" : "Editar registro";
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
    dialogRelative(val) {
      val || this.closeRelativeDialog();
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
          maxLength: helpers.withMessage(
            ({ $params }) => langMessages.maxLength($params),
            maxLength(2)
          ),
        },
        card: {
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
        nie: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(7)
          ),
          maxLength: helpers.withMessage(
            ({ $params }) => langMessages.maxLength($params),
            maxLength(7)
          ),
        },
        phone_number: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(8)
          ),
          maxLength: helpers.withMessage(
            ({ $params }) => langMessages.maxLength($params),
            maxLength(8)
          ),
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
      },
      relative: {
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
        dui: {
          required: helpers.withMessage(langMessages.required, required),
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(9)
          ),
          maxLength: helpers.withMessage(
            ({ $params }) => langMessages.maxLength($params),
            maxLength(9)
          ),
        },
        phone_number: {
          required: helpers.withMessage(langMessages.required, required),
          minLength: helpers.withMessage(
            ({ $params }) => langMessages.minLength($params),
            minLength(8)
          ),
          maxLength: helpers.withMessage(
            ({ $params }) => langMessages.maxLength($params),
            maxLength(8)
          ),
        },
        mail: {
          required: helpers.withMessage(langMessages.required, required),
          email: helpers.withMessage(langMessages.email, email),
        },
        kinship: {
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
        kinshipApi.get(null, {
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
        this.kinship = responses[2].data.data;
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

    addRelative() {
      this.dialogRelative = true;
      this.editedRelative = -1;
      this.v$.relative.name.$model = "";
      this.v$.relative.last_name.$model = "";
      this.v$.relative.dui.$model = "";
      this.v$.relative.phone_number.$model = "";
      this.v$.relative.mail.$model = "";
      this.v$.relative.kinship.$model = "";
      this.v$.relative.$reset();
    },

    async addNewRelative() {
      this.v$.relative.$validate();
      if (this.v$.relative.$invalid) {
        alert.error("Campo obligatorio");
        return;
      }

      // Creating record
      try {
        this.editedItem.relatives.push({ ...this.relative });
      } catch (error) {
        alert.error("No fue posible crear el registro.");
      }

      this.closeRelativeDialog();
      this.initialize();
      this.loading = false;
      return;
    },

    closeRelativeDialog() {
      this.v$.relative.$reset();
      this.dialogRelative = false;
      this.editedRelative = -1;
    },

    async deleteRelative(index) {
      this.editedItem.relatives.splice(index, 1);
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
        this.loading = false;
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