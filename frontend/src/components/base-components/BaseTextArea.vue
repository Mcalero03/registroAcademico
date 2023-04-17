<template>
  <div>
    <v-textarea
      v-bind="$attrs"
      class="mt-4"
      variant="outlined"
      v-model="rules.$model"
      @keyup="validateText()"
    />

    <base-error :rules="rules" />
  </div>
</template>

<script>
import BaseError from "./BaseError.vue";

export default {
  components: { BaseError },
  props: {
    rules: {
      type: Object,
      default: {},
    },
  },
};
</script>