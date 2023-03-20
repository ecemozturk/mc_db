<template>
  <VueCtkDateTimePicker
    v-model="selectedDateTime"
    :format="config.dateFormat || 'DD.MM.YYYY HH:mm:ss'"
    :label="label"
    :required="required"
  />


</template>

<script>
import VueCtkDateTimePicker from 'vue-ctk-date-time-picker';
import 'vue-ctk-date-time-picker/dist/vue-ctk-date-time-picker.css';
import moment from 'moment';

export default {
  name: 'mcDateTimePicker',
  components: {
    VueCtkDateTimePicker,
  },
  props: {
    value: {
      type: String,
      default: '',
    },
    label: {
      type: String,
      default: 'Select date & time',
    },
    config: {
      type: Object,
      default: () => ({}),
    },
    required: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      selectedDateTime: this.value,
    };
  },
  watch: {
    selectedDateTime(newValue) {
      this.$emit('input', newValue);
    },
    value(newValue) {
      this.selectedDateTime = newValue;
    },
  },
};
</script>
