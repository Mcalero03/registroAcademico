<template></template> 

<script>
import { useVuelidate } from "@vuelidate/core";
import { messages } from "@/utils/validators/i18n-validators";
import { helpers, minLength, required, email } from "@vuelidate/validators";

import directionApi from "@/services/directionApi";
import BaseButton from "../components/base-components/BaseButton.vue";
import BaseInput from "../components/base-components/BaseInput.vue";

import useAlert from "../composables/useAlert";

const { alert } = useAlert();
const langMessages = messages["es"].validations;

export default {
  components: { BaseButton, BaseInput },
  setup() {
    return { v$: useVuelidate() };
  },
  data() {
    return {
      search: "",
      dialog: false,
      dialogDelete: false,
      headers: [
        { title: "TIPO PENSUM", key: "pensum_type_name" },
        { title: "ACCIONES", key: "actions", sortable: false },
      ],
      title: "TIPO PENSUM",
      total: 0,
      records: [],
      loading: false,
    };
  },
};
</script>
