<template>
  <div>
    <!-- Date input -->
    <VTextField v-model="dateInput" label="Arama Tarihi" type="date" required @change="updateDateTime" />

    <!-- Time input -->
    <VTextField v-model="timeInput" label="Arama Saati" type="time" required @change="updateDateTime" />
  </div>
</template>

<script>
import { ref, watch } from 'vue';
import dayjs from 'dayjs';

export default {
  props: {
    modelValue: {
      type: String,
      default: '',
    },
    config: {
      type: Object,
      default: () => ({}),
    },
  },
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    const dateInput = ref('');
    const timeInput = ref('');

    const updateDateTime = () => {
      const customDateTime = dayjs(`${dateInput.value}T${timeInput.value}`).format(props.config.dateFormat);
      emit('update:modelValue', customDateTime);
    };

    watch(() => props.modelValue, (newValue) => {
      const dateTime = dayjs(newValue);
      dateInput.value = dateTime.format('YYYY-MM-DD');
      timeInput.value = dateTime.format('HH:mm');
    }, { immediate: true });

    return {
      dateInput,
      timeInput,
      updateDateTime,
    };
  },
};
</script>

<style scoped>
/* Add any desired custom styles for your component here */
</style>
