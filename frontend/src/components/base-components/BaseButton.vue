<script setup>
const props = defineProps({
  title: {
    required: true,
    type: String,
  },
  type: {
    required: true,
    type: String,
  },
});

const { title, type } = props;
</script>

<template>
  <v-btn :class="'btn btn-' + type">{{ title }}</v-btn>
</template>