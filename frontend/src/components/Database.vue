<script setup>
import { onMounted, ref } from "vue";
import gatewayApi from "../services/gatewayApi";
import Loader from "./Loader.vue";
import BaseButton from "./base-components/BaseButton.vue";

import { Modal } from "bootstrap";
import useAlert from "../composables/useAlert";

const { alert } = useAlert();

const dbModalRef = ref(null);

const databases = ref([]);
const dbFiltered = ref([]);
const filter = ref("");
const dbSelected = ref({});
const files = ref([]);
const fileSelected = ref(null);
const disableButtons = ref(false);

const loadingDatabases = ref(false);
const loadingFiles = ref(false);
const dialogBd = ref(false);

onMounted(async () => {
  await initialize();
});

/**
 * Getting databases.
 */
const initialize = async () => {
  try {
    loadingDatabases.value = true;
    const { data } = await gatewayApi.get(`/credentials/credentials`);

    databases.value = data.data;
    dbFiltered.value = data.data;

    loadingDatabases.value = false;
  } catch (error) {
    alert.error("No fue posible obtener las credenciales de datos.");
    // console.log(error);
    // toast.error("No fue posible obtener las credenciales de datos.");
  }
};

/**
 * Get files from Google Drive.
 *
 * @param {Object} db
 */
const selectDatabase = async (db) => {
  try {
    disableButtons.value = true;
    loadingFiles.value = true;
    files.value = [];
    dbSelected.value = db;
    fileSelected.value = null;

    const { data } = await gatewayApi.get(`/databases/databases`, {
      params: {
        filter: db.database,
        pageSize: 7,
      },
    });

    files.value = data.data;
    loadingFiles.value = false;
  } catch (error) {
    disableButtons.value = true;
    loadingFiles.value = false;
    files.value = [];
    alert.error("No fue posible obtener los archivos.");
  }
};

/**
 * Filters databases from any of the object.
 */
const filterDatabase = () => {
  clearValues();

  if (!filter.value) {
    dbFiltered.value = databases.value;
    return;
  }

  dbFiltered.value = databases.value.filter((db) => {
    // Busca en todas las propiedades del objeto
    for (let prop in db) {
      if (
        db[prop].toString().toLowerCase().includes(filter.value.toLowerCase())
      ) {
        return true; // Si encuentra una coincidencia, devuelve true
      }
    }

    return false; // Si no hay coincidencias, devuelve false
  });
};

/**
 * Sets the file to mount.
 * @param {Object} file
 */
const selectFile = (file) => {
  fileSelected.value = file;
  disableButtons.value = false;
};

/**
 * Calls an api to mount the database in the server.
 */
const mountDb = async () => {
  try {
    disableButtons.value = true;
    const params = {
      id: fileSelected.value.id,
      name: fileSelected.value.name,
      databaseName: dbSelected.value.database,
      databaseDriver: dbSelected.value.driver,
    };

    Modal.getInstance(dbModalRef.value)?.hide();

    alert.error(
      `Montando base de datos '${dbSelected.value.database}'. Por favor espere...`,
      -1
    );
    const response = await gatewayApi.post(`/databases/mount`, params);

    alert.success(
      `Base de datos '${dbSelected.value.database}' montada correctamente. Verifica el gestor de datos.`
    );
    disableButtons.value = false;
  } catch (error) {
    alert.error("Error" + error.message);
  }
};

/**
 * Clears the values from the form.
 */
const clearValues = () => {
  dbSelected.value = [];
  files.value = [];
  fileSelected.value = null;
};
</script>

<template>
  <h1 class="mt-4 mb-1">Bases de datos</h1>
  <hr class="mb-5" />

  <!-- <base-input /> -->

  <!-- <div class="row mx-auto">
    <div class="col-md-6 mx-auto mb-3 mt-3">
      <div class="input-group">
        <input
          type="text"
          class="form-control"
          placeholder="Buscar"
          v-model="filter"
          @input="filterDatabase()"
        />
      </div>
    </div>
  </div> -->

  <!-- Showing databases -->
  <v-row v-if="dbFiltered.length > 0 && !loadingDatabases">
    <v-col
      cols="12"
      sm="6"
      md="6"
      v-for="(item, index) in dbFiltered"
      :key="index"
    >
      <v-card :title="item.database">
        <v-card-text>
          <span
            >Nombre: <b>{{ item.database }}</b></span
          >
          <br />
          <span
            >Driver: <b>{{ item.driver }}</b></span
          >
          <br />
          <span
            >Host: <b>{{ item.host }}</b></span
          >
          <br />
          <span
            >Puerto: <b>{{ item.port }}</b></span
          >
          <br />

          <base-button
            title="Montar"
            type="primary"
            class="mt-3"
            @click="
              selectDatabase(item);
              dialogBd = true;
            "
          />
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
  <!-- Showing databases -->

  <!-- Showing empty message of databases -->
  <div
    class="text-center pt-3"
    v-if="dbFiltered.length == 0 && !loadingDatabases"
  >
    <span>No se encontraron bases de datos para montar.</span>
  </div>
  <!-- Showing empty message of databases -->

  <!-- Loader databases -->
  <div class="row" v-if="loadingDatabases">
    <Loader class="mx-auto" />
  </div>
  <!-- Loader databases -->

  <!-- Modal -->
  <v-dialog v-model="dialogBd" width="800px" persistent>
    <v-card>
      <v-card-title>
        <h3 class="text-center pt-3">{{ dbSelected.database }}</h3>
      </v-card-title>

      <v-card-text>
        <!-- Showing files -->
        <h5>Archivos disponibles</h5>
        <div v-for="file in files" :key="file.id">
          <v-card
            class="p-2 mt-1"
            @click="
              selectFile(file);
              disableButtons = false;
            "
            >{{ file.name }}</v-card
          >
        </div>
        <!-- Showing files -->

        <div class="text-center" v-if="files.length == 0 && !loadingFiles">
          <span>Sin archivos por mostrar.</span>
        </div>

        <!-- File selected -->
        <div class="pt-5" v-if="fileSelected && !loadingFiles">
          <b>Archivo seleccionado:</b>
          <span class="text-danger">{{ fileSelected.name }}</span>
        </div>
        <!-- File selected -->

        <div class="row">
          <Loader class="mx-auto" v-if="loadingFiles" />
        </div>
      </v-card-text>

      <v-card-actions class="mx-auto">
        <base-button
          title="Montar"
          type="primary"
          :disabled="disableButtons"
          @click="
            mountDb();
            dialogBd = false;
          "
        >
        </base-button>
        <base-button title="Cerrar" type="secondary" @click="dialogBd = false">
        </base-button>
      </v-card-actions>
    </v-card>
  </v-dialog>
  <!-- Modal -->
</template>

<style scoped>
a.card {
  color: black;
  text-decoration: none;
}

a.files {
  color: black;
  text-decoration: none;
}

.modal-body a:hover {
  background: #f4f7fd;
}

.card-footer {
  background: white;
}
</style>