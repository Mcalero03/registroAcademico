<template>
  <div>
    <div
      v-for="error of rules.$errors"
      :key="error.$uid"
      class="d-flex warning pt-1"
    >
      <v-icon icon="mdi-alert-circle-outline"></v-icon>
      <strong>{{ error.$message }}</strong>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    rules: {
      type: Object,
      required: true,
    },
  },
};
</script>

<style>
</style>