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
            <v-row>
              <!-- cycle  -->
              <v-col cols="5" sm="5" md="3" v-if="editedIndex == -1">
                <v-label>Ciclo</v-label>
                <base-select
                  :items="cycles"
                  item-title="cycle"
                  item-value="cycle"
                  v-model="v$.editedItem.cycle.$model"
                  :rules="v$.editedItem.cycle"
                />
              </v-col>
              <!-- cycle  -->
              <v-col
                cols="5"
                sm="5"
                md="3"
                class="p-0 m-0 pr-4 pb-4"
                v-if="editedIndex != -1"
              >
                <v-label>Ciclo</v-label>
                <base-select
                  :items="cycles"
                  v-model="v$.editedItem.cycle.$model"
                  :rules="v$.editedItem.cycle"
                  readonly
                  v-if="editedIndex != -1"
                />
              </v-col>
              <!-- cycle  -->
              <v-col
                cols="5"
                sm="5"
                md="3"
                v-if="editedIndex == -1"
                class="pt-4"
              >
                <v-label>Buscar por carnet</v-label>
                <v-text-field
                  class="mt-3"
                  variant="outlined"
                  type="text"
                  v-model="searchStudent"
                >
                </v-text-field>
              </v-col>
              <v-col
                cols="5"
                sm="2"
                md="3"
                class="pt-12"
                v-if="editedIndex == -1"
              >
                <base-button
                  type="primary"
                  title="Buscar"
                  @click="searchStudentCard()"
                />
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" sm="6" md="6" v-if="editedIndex == -1">
                <v-label>Estudiante</v-label>
                <base-input
                  :items="students"
                  item-value="full_name"
                  item-title="full_name"
                  v-model="v$.editedItem.full_name.$model"
                  :rules="v$.editedItem.full_name"
                  readonly
                />
              </v-col>
              <!-- student_name  -->
              <!-- student_name  -->
              <v-col
                cols="12"
                sm="6"
                md="6"
                class="p-0 m-0 pr-4"
                v-if="editedIndex != -1"
              >
                <v-label>Estudiante</v-label>
                <base-select
                  :items="students"
                  v-model="v$.editedItem.full_name.$model"
                  :rules="v$.editedItem.full_name"
                  readonly
                  v-if="editedIndex != -1"
                />
              </v-col>
              <!-- student_name  -->
              <!-- program_name  -->
              <v-col cols="12" sm="6" md="6" v-if="editedIndex == -1">
                <v-label>Carrera</v-label>
                <base-select
                  :items="pensums"
                  item-title="program_name"
                  item-value="program_name"
                  v-model="v$.editedItem.program_name.$model"
                  :rules="v$.editedItem.program_name"
                  @click="showCareers"
                  @blur="availableSubjects()"
                />
              </v-col>
              <!-- program_name  -->
              <!-- program_name  -->
              <v-col
                cols="12"
                sm="6"
                md="6"
                class="p-0 m-0 pr-4"
                v-if="editedIndex != -1"
              >
                <v-label>Carrera</v-label>
                <base-select
                  :items="pensums"
                  v-model="v$.editedItem.program_name.$model"
                  :rules="v$.editedItem.program_name"
                  readonly
                  v-if="editedIndex != -1"
                />
              </v-col>
              <!-- program_name  -->
            </v-row>
            <v-row justify="center"
              ><!-- subject -->
              <v-col cols="auto" class="mt-2" v-if="editedIndex == -1">
                <base-button
                  type="secondary"
                  title="Agregar materia"
                  @click="addSubject()"
                />
              </v-col>
              <!-- subject -->
            </v-row>
            <!-- Inscription Table -->
            <v-row>
              <v-col
                align="center"
                cols="12"
                md="12"
                sm="12"
                class="pt-4"
                v-if="editedIndex == -1"
              >
                <div class="table-responsive-md">
                  <v-table>
                    <thead>
                      <tr>
                        <th>GRUPO</th>
                        <th class="text-center">ACCIÓN</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(inscription, index) in editedItem.inscriptions"
                        v-bind:index="index"
                        :key="index"
                      >
                        <td v-text="inscription.group_code"></td>
                        <td class="text-center">
                          <v-tooltip text="Eliminar" location="end">
                            <template v-slot:activator="{ props }">
                              <v-icon
                                size="20"
                                class="mr-2"
                                @click="deleteGroup(index)"
                                icon="mdi-delete"
                                v-bind="props"
                              />
                            </template>
                          </v-tooltip>
                        </td>
                      </tr>
                      <tr v-if="editedItem.inscriptions.length == 0">
                        <td colspan="5" class="text-center pt-3">
                          <p>No se ha inscrito ningún grupo</p>
                        </td>
                      </tr>
                    </tbody>
                  </v-table>
                </div>
              </v-col>
              <v-col
                align="center"
                cols="12"
                md="12"
                sm="12"
                class="pt-4"
                v-if="editedIndex != -1"
              >
                <div class="table-responsive-md">
                  <v-table>
                    <thead>
                      <tr>
                        <th>GRUPO</th>
                        <th>MATERIA</th>
                        <th>ESTADO</th>
                        <th class="text-center">ACCIÓN</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr
                        v-for="(inscription, index) in editedItem.inscriptions"
                        v-bind:index="index"
                        :key="index"
                      >
                        <td v-text="inscription.group_code"></td>
                        <td v-text="inscription.subject_name"></td>
                        <td v-text="inscription.status"></td>
                        <td class="text-center">
                          <v-tooltip text="Ver horarios" location="start">
                            <template v-slot:activator="{ props }">
                              <v-icon
                                size="20"
                                class="mr-2"
                                @click="viewSchedules(index)"
                                icon="mdi-clock "
                                v-bind="props"
                              />
                            </template>
                          </v-tooltip>
                          <v-tooltip text="Editar" location="end">
                            <template v-slot:activator="{ props }">
                              <v-icon
                                size="20"
                                class="mr-2"
                                @click="editItemStatus(index)"
                                icon="mdi-pencil"
                                v-bind="props"
                              />
                            </template>
                          </v-tooltip>
                        </td>
                      </tr>
                      <tr v-if="editedItem.inscriptions.length == 0">
                        <td colspan="5" class="text-center pt-3">
                          <p>No se ha inscrito ningún grupo</p>
                        </td>
                      </tr>
                    </tbody>
                  </v-table>
                </div>
              </v-col>
              <!-- Modal -->
              <v-dialog v-model="dialogSubject" max-width="700px" persistent>
                <v-card height="100%">
                  <v-container>
                    <h2 class="black-secondary text-center mt-4 mb-4">
                      Agregar materia
                    </h2>
                    <v-row>
                      <!-- group_code  -->
                      <v-col cols="12" sm="4" md="4">
                        <v-label>Seleccione un grupo</v-label>
                        <base-select
                          :items="selectGroups"
                          item-title="group_code"
                          item-value="group_code"
                          v-model="v$.group.group_code.$model"
                          :rules="v$.group.group_code"
                        />
                      </v-col>
                      <!-- group_code  -->
                    </v-row>
                    <v-row>
                      <v-col
                        align="center"
                        cols="12"
                        md="12"
                        sm="12"
                        v-if="inscriptions != null"
                      >
                        <h4 class="pb-2" align="left">MATERIAS CURSADAS</h4>
                        <v-data-table
                          :headers="headerSubjectsTaken"
                          :items="inscriptions"
                          item-title="subject_name"
                          item-value="subject_name"
                          density="compact"
                          class="elevation-1"
                          items-per-page="5"
                        ></v-data-table
                      ></v-col>
                      <v-col align="center" cols="12" md="12" sm="12">
                        <h4 class="pb-2" align="left">
                          GRUPOS Y HORARIOS DISPONIBLES
                        </h4>
                        <v-data-table
                          :headers="headerGroups"
                          :items="groups"
                          item-title="group_code"
                          item-value="group_code"
                          density="compact"
                          class="elevation-1"
                          items-per-page="5"
                        ></v-data-table
                      ></v-col>
                    </v-row>
                    <v-row>
                      <v-col align="center">
                        <base-button
                          type="primary"
                          title="Agregar"
                          @click="addNewSubject()"
                        />
                        <base-button
                          class="ms-1"
                          type="secondary"
                          title="Cancelar"
                          @click="closeSubjectDialog()"
                        />
                      </v-col>
                    </v-row>
                  </v-container>
                </v-card>
              </v-dialog>
              <!-- Modal -->
              <!-- Inscription Table -->
            </v-row>
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

    <!-- Modal -->
    <v-dialog v-model="dialogSchedules" max-width="600px" persistent>
      <v-card height="100%">
        <v-container>
          <h2 class="black-secondary text-center mt-4 mb-4">Horarios</h2>
          <v-row
            ><v-col align="center" cols="12" md="12" sm="12" class="pt-4">
              <div class="table-responsive-md">
                <v-table>
                  <thead>
                    <tr>
                      <th>DÍA</th>
                      <th>HORA INICIO</th>
                      <th>HORA FIN</th>
                      <th>AULA</th>
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
                      <td v-text="schedule.classroom_name"></td>
                    </tr>
                    <tr v-if="editedItem.schedules.length == 0">
                      <td colspan="5" class="text-center pt-3">
                        <p>No hay horario definido</p>
                      </td>
                    </tr>
                  </tbody>
                </v-table>
              </div>
            </v-col>
          </v-row>
          <v-row>
            <v-col align="center">
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

    <!-- Modal -->
    <v-dialog v-model="dialogEditStatus" max-width="600px" persistent>
      <v-card height="100%">
        <v-container>
          <h2 class="black-secondary text-center mt-4 mb-4">Editar estado</h2>
          <v-row>
            <!-- group_code  -->
            <v-col cols="12" sm="6" md="6">
              <base-input
                label="Grupo"
                v-model="v$.editStatus.group_code.$model"
                :rules="v$.editStatus.group_code"
                readonly
              />
            </v-col>
            <!-- group_code  -->
            <!-- subject_name  -->
            <v-col cols="12" sm="6" md="6">
              <base-input
                label="Materia"
                v-model="v$.editStatus.subject_name.$model"
                :rules="v$.editStatus.subject_name"
                readonly
              />
            </v-col>
            <!-- subject_name  -->
            <!-- status  -->
            <v-col cols="12" sm="6" md="6">
              <v-label>Estado</v-label>
              <base-select
                :items="status"
                item-title="status"
                item-value="status"
                v-model="v$.editStatus.status.$model"
                :rules="v$.editStatus.status"
              />
            </v-col>
            <!-- status  -->
          </v-row>
          <v-row>
            <v-col align="center">
              <base-button
                type="primary"
                title="Actualizar"
                @click="addEditItemStatus()"
              />
            </v-col>
          </v-row>
        </v-container>
      </v-card>
    </v-dialog>
    <!-- Modal -->

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
import { toast } from "../../node_modules/vue3-toastify";
import "vue3-toastify/dist/index.css";
import { useVuelidate } from "@vuelidate/core";
import { messages } from "@/utils/validators/i18n-validators";
import { helpers, required } from "@vuelidate/validators";

import inscriptionApi from "@/services/inscriptionApi";
import cycleApi from "@/services/cycleApi";
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
      searchStudent: "",
      dialog: false,
      dialogDelete: false,
      dialogSchedules: false,
      dialogSubject: false,
      dialogEditStatus: false,
      editedIndex: -1,
      editedGroup: -1,
      title: "INSCRIPCIONES",
      headers: [
        { title: "ESTUDIANTE", key: "full_name" },
        { title: "CICLO", key: "cycle" },
        { title: "ESTADO", key: "status" },
        { title: "CARRERA", key: "program_name" },
        { title: "INSCRIPCIÓN", key: "inscription_date" },
        { title: "ACCIONES", key: "actions", sortable: false },
      ],
      headerSubjectsTaken: [
        { title: "Materia", key: "subject_name" },
        { title: "Estado", key: "status" },
      ],
      headerGroups: [
        { title: "Materia", key: "subject_name" },
        { title: "Grupo", key: "group_code" },
        { title: "Día", key: "week_day" },
        { title: "Hora Inicio", key: "start_time" },
        { title: "Hora Fin", key: "end_time" },
      ],

      total: 0,
      records: [],
      cycles: [],
      status: ["Inscrito", "Retirado", "Reprobado", "Aprobado"],
      students: [],
      inscriptions: [],
      pensums: [],
      selectGroups: [],
      groups: [],
      subjects: [],
      loading: false,
      debounce: 0,
      options: {},
      editedItem: {
        inscription_date: this.getDate(),
        cycle: "",
        full_name: "",
        inscriptions: [],
        schedules: [],
        program_name: "",
        status: "",
      },
      defaultItem: {
        inscription_date: this.getDate(),
        cycle: "",
        full_name: "",
        inscriptions: [],
        schedules: [],
        program_name: "",
        status: "",
      },
      group: {
        group_code: "",
      },
      editStatus: {
        group_code: "",
        subject_name: "",
        status: "",
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
        cycle: {
          required: helpers.withMessage(langMessages.required, required),
        },
        full_name: {
          required: helpers.withMessage(langMessages.required, required),
        },
        schedules: {},
        inscriptions: {
          required: helpers.withMessage(langMessages.required, required),
        },
        pensums: {},
        program_name: {
          required: helpers.withMessage(langMessages.required, required),
        },
        status: {},
      },
      group: {
        group_code: {},
      },
      editStatus: {
        group_code: {},
        subject_name: {},
        status: {},
      },
    };
  },

  methods: {
    async showSchedules(index) {
      if (this.editedItem.id != "") {
        this.editedGroup = this.editedItem.inscriptions[index];
        console.log(this.editedGroup["id"]);
        const { data } = await inscriptionApi
          // .get("/showSchedules/" + this.editedItem.id )
          .get("/showSchedules/" + this.editedGroup["id"])

          .catch((error) => {
            toast.error("No fue posible obtener la información.", {
              autoClose: 2000,
              position: toast.POSITION.TOP_CENTER,
            });
          });
        this.editedItem.schedules = data.schedules;
        // console.log(this.editedItem.schedules);
      }
    },

    async showCareers() {
      if (this.editedItem.full_name != "") {
        const { data } = await inscriptionApi
          .get("/showCareers/" + this.editedItem.full_name)
          .catch((error) => {
            toast.error("No fue posible obtener la información.", {
              autoClose: 2000,
              position: toast.POSITION.TOP_CENTER,
            });
          });
        this.pensums = data.careers;
      }
    },

    async searchStudentCard() {
      try {
        if (this.searchStudent != "") {
          const { data } = await inscriptionApi.get(null, {
            params: {
              searchStudent: this.searchStudent,
            },
          });
          this.students = data.student;
          this.editedItem.full_name = this.students[0].full_name;
        } else {
          toast.error("Ingrese el carnet del estudiante.", {
            autoClose: 2000,
            position: toast.POSITION.TOP_CENTER,
            multiple: false,
          });
        }
      } catch (error) {
        toast.error("No fue posible obtener el estudiante.", {
          autoClose: 2000,
          position: toast.POSITION.TOP_CENTER,
          multiple: false,
        });
      }
    },

    async availableSubjects() {
      if (
        this.editedItem.full_name != "" &&
        this.editedItem.program_name != "" &&
        this.editedItem.cycle != ""
      ) {
        const { data } = await inscriptionApi
          .get(
            "/availableSubjects/" +
              this.editedItem.full_name +
              "/" +
              this.editedItem.program_name +
              "/" +
              this.editedItem.cycle
          )
          .catch((error) => {
            toast.error("No fue posible obtener la información.", {
              autoClose: 2000,
              position: toast.POSITION.TOP_CENTER,
            });
          });
        this.inscriptions = data.inscriptions;
        this.groups = data.groups;
        this.selectGroups = data.selectGroup;
      }
    },

    async initialize() {
      this.loading = true;
      this.records = [];

      let requests = [
        this.getDataFromApi(),
        cycleApi.get(null, {
          params: {
            itemsPerPage: -1,
          },
        }),
      ];
      const responses = await Promise.all(requests).catch((error) => {
        alert.error("No fue posible obtener el registro.");
      });

      if (responses) {
        this.cycles = responses[1].data.cycles;
      }

      this.loading = false;
    },

    getDataFromApi(options) {
      this.loading = false;
      this.records = [];

      clearTimeout(this.debounce);
      this.debounce = setTimeout(async () => {
        try {
          const { data } = await inscriptionApi.get(null, {
            params: {
              ...options,
              search: this.search,
            },
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

    getDate() {
      const datetime = new Date().toISOString().substring(0, 10);
      return datetime;
    },

    viewSchedules(index) {
      this.v$.editedItem.schedules.$model = [];
      this.showSchedules(index);
      this.dialogSchedules = true;
    },

    closeScheduleDialog() {
      this.v$.editedItem.schedules.$reset();
      this.dialogSchedules = false;
    },

    //EDIT STATUS OF GROUPS INSCRIPTION
    editItemStatus(index) {
      this.editedGroup = this.editedItem.inscriptions[index];
      this.editedItem.inscriptions.splice(index, 1);
      this.editStatus = Object.assign({}, this.editedGroup);
      this.dialogEditStatus = true;
    },

    async addEditItemStatus() {
      this.v$.editStatus.$validate();
      if (this.v$.editStatus.$invalid) {
        alert.error("Campo obligatorio");
        return;
      }

      // Creating record
      try {
        this.editedItem.inscriptions.push({ ...this.editStatus });
        toast.success("Datos actualizados. Guarde los cambios", {
          autoClose: 2000,
          position: toast.POSITION.TOP_CENTER,
          multiple: false,
        });
      } catch (error) {
        toast.error("No fue posible actualizar el registro.", {
          autoClose: 2000,
          position: toast.POSITION.TOP_CENTER,
          multiple: false,
        });
      }

      this.closeEditStatus();
      this.initialize();
      this.loading = false;
      return;
    },

    closeEditStatus() {
      this.v$.editStatus.$reset();
      this.dialogEditStatus = false;
    },

    //ADD NEW GROUP INSCRIPTION
    addSubject() {
      if (
        this.editedItem.full_name != "" &&
        this.editedItem.program_name != "" &&
        this.editedItem.cycle != ""
      ) {
        if (this.groups != "Registrado") {
          this.dialogSubject = true;
          this.v$.group.group_code.$model = "";
          this.v$.group.$reset();
        } else if (this.groups == "Registrado") {
          toast.error("Ya inscribió materias para este ciclo.", {
            autoClose: 2000,
            position: toast.POSITION.TOP_CENTER,
            multiple: false,
          });
        }
      } else if (
        this.editedItem.full_name == "" ||
        this.editedItem.program_name == "" ||
        this.editedItem.cycle == ""
      ) {
        toast.error("Complete la información de inscripción.", {
          autoClose: 2000,
          position: toast.POSITION.TOP_CENTER,
          multiple: false,
        });
      }
    },

    closeSubjectDialog() {
      this.v$.group.$reset();
      this.dialogSubject = false;
    },

    async addNewSubject() {
      this.v$.group.$validate();
      if (this.v$.group.$invalid) {
        alert.error("Campo obligatorio");
      }

      // Creating record
      try {
        this.editedItem.inscriptions.push({ ...this.group });
      } catch (error) {
        alert.error("No fue posible crear el registro.");
      }

      this.closeSubjectDialog();
      this.initialize();
      this.loading = false;
      return;
    },

    async deleteGroup(index) {
      this.editedItem.inscriptions.splice(index, 1);
    },

    close() {
      this.dialog = false;
      this.$nextTick(() => {
        this.editedItem = Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
      this.searchStudent = "";
      this.students = [];
      this.editedItem.inscriptions = [];
    },

    addRecord() {
      this.dialog = true;
      this.editedIndex = -1;
      this.editedItem = Object.assign({}, this.defaultItem);
      this.v$.$reset();
      this.searchStudent = "";
      this.students = [];
      this.editedItem.inscriptions = [];
    },

    editItem(item) {
      this.editedIndex = this.records.indexOf(item);
      this.editedItem = Object.assign({}, item);
      this.dialog = true;
    },

    async save() {
      this.v$.$validate();
      if (this.v$.$invalid) {
        toast.warn("Verifique el ingreso de los grupos a inscribir.", {
          autoClose: 4000,
          position: toast.POSITION.TOP_CENTER,
          multiple: false,
        });
        return;
      }

      // Updating record
      if (this.editedIndex > -1) {
        const edited = Object.assign(
          this.records[this.editedIndex],
          this.editedItem
        );

        try {
          const { data } = await inscriptionApi.put(`/${edited.id}`, edited);
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
        const { data } = await inscriptionApi.post(null, this.editedItem);
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
        const { data } = await inscriptionApi.delete(`/${this.editedItem.id}`, {
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