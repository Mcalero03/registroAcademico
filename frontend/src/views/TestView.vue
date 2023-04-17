<script setup>
import { ref } from "vue";
import { useVuelidate } from "@vuelidate/core";
import { messages } from "@/utils/validators/i18n-validators";
import { helpers, minLength, required, email } from "@vuelidate/validators";

import BaseButton from "@/components/base-components/BaseButton.vue";
import BaseInput from "@/components/base-components/BaseInput.vue";
import BaseTextArea from "../components/base-components/BaseTextArea.vue";
import BaseSelect from "../components/base-components/BaseSelect.vue";

const langMessages = messages["es"].validations;
const state = ref({
  firstName: "Nombre",
  lastName: "",
  contact: {
    email: "",
  },
});

const rules = {
  firstName: {
    required: helpers.withMessage(langMessages.required, required),
    minLength: helpers.withMessage(
      ({ $params }) => langMessages.minLength($params),
      minLength(2)
    ),
    email: helpers.withMessage(langMessages.email, email),
  },
  description: {
    required: helpers.withMessage(langMessages.required, required),
    minLength: helpers.withMessage(
      ({ $params }) => langMessages.minLength($params),
      minLength(4)
    ),
  },
  country: {
    required: helpers.withMessage(langMessages.required, required),
    minLength: helpers.withMessage(
      ({ $params }) => langMessages.minLength($params),
      minLength(4)
    ),
  },
};

const v$ = useVuelidate(rules, state);

const validateForm = async () => {
  console.log(await v$.value.$validate());

  console.log(v$.$errors);
};
</script>

<template>
  <div>
    <BaseInput
      v-model="v$.firstName.$model"
      :rules="v$.firstName"
      label="Primer nombre"
    />

    <!-- Textarea -->
    <BaseTextArea
      v-model="v$.description.$model"
      :rules="v$.description"
      label="Descripcion"
    />
    <!-- Textarea -->

    <!-- BaseSelect -->
    <base-select
      v-model="v$.country.$model"
      label="País"
      :items="[
        'California',
        'Colorado',
        'Florida',
        'Georgia',
        'Texas',
        'Wyoming',
      ]"
      :rules="v$.country"
    />

    <!-- Button -->
    <!-- Type: primary || secondary -->
    <BaseButton
      type="secondary"
      title="Prueba"
      class="mt-3"
      @click="validateForm()"
    />
    <!-- Button -->
  </div>
</template>

<style>
</style>