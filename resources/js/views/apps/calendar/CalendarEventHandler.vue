<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { VForm } from 'vuetify/components'
import { requiredValidator } from '@validators'
import { ref, computed, defineProps, defineEmits, watch, nextTick } from 'vue'
import useCalendar from '@/composables/useCalendar'

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
  event: {
    type: Object,
    required: true,
  },
})

// Format Date
const formatDate = (date) => {
  const year = date.getFullYear();
  const month = `0${date.getMonth() + 1}`.slice(-2);
  const day = `0${date.getDate()}`.slice(-2);
  const hours = `0${date.getHours()}`.slice(-2);
  const minutes = `0${date.getMinutes()}`.slice(-2);
  const seconds = `0${date.getSeconds()}`.slice(-2);
  return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
};


// Emits
const emit = defineEmits([
  'update:isDrawerOpen',
  'createEvent',
  'updateEvent',
  'removeEvent',
]);
const updateEvent = async (id, eventData) => {
  try {
    const response = await fetch(`/api/events/${id}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(eventData)
    });

    if (!response.ok) {
      throw new Error(`Failed to update event with id ${id}`);
    }

    const updatedEvent = await response.json();
    return updatedEvent;
  } catch (error) {
    console.error(error);
  }
};


const refForm = ref();

// 👉 Event

const resetEvent = () => {
  event.value = JSON.parse(JSON.stringify(props.event));
  nextTick(() => {
    refForm.value?.resetValidation();
  });
};

watch(() => props.isDrawerOpen, resetEvent);

const removeEvent = () => {
  emit('removeEvent', event.value.id);

  // Close drawer
  emit('update:isDrawerOpen', false);
};

const updateEventMethod = async (id, eventData) => {
  await updateEvent(id, eventData);
  emit('updateEvent', eventData);
};

const createEvent = async () => {
  await storeCalendar(event.value);
  emit('createEvent', event.value);
  event.value = { title: '', description:'', start: '', end: '' , id:''};
};

// 👉 Form
const onCancel = () => {

  // Close drawer
  emit('update:isDrawerOpen', false);
  nextTick(() => {
    refForm.value?.reset();
    resetEvent();
    refForm.value?.resetValidation();
  });
};

const dialogModelValueUpdate = val => {
  emit('update:isDrawerOpen', val);
};

const { storeCalendar } = useCalendar();
const event = ref({ title: '', description: '', start: '', end: '', id:'' });


const handleSubmit = () => {
  refForm.value?.validate().then(({ valid }) => {
    if (valid) {
      // If id exists on event => Update event
      if ('id' in props.event) {
        console.log('if event id', event.value);

        updateEventMethod(props.event.id, {
          title: event.value.title,
          description: event.value.description,
          start: formatDate(new Date(event.value.start)),
          end: event.value.end ? formatDate(new Date(event.value.end)) : null,
        });
      }
      // Else => Add new event
      else {
        createEvent(); // Call createEvent method here
      }

      // Close drawer
      emit('update:isDrawerOpen', false);
    }
  });
};

const startDateTimePickerConfig = computed(() => {
  const config = {
    enableTime: true,
    dateFormat: 'Y-m-d H:i:00',
  };

  if (event.value.end)
    config.maxDate = event.value.end;

  return config;
});

const endDateTimePickerConfig = computed(() => {
  const config = {
    enableTime: true,
    dateFormat: 'Y-m-d H:i:00',
  }

  if (event.value.start)
    config.minDate = event.value.start

  return config
})

</script>



<template>
  <VNavigationDrawer
    temporary
    location="end"
    :model-value="props.isDrawerOpen"
    width="420"
    class="scrollable-content"
    @update:model-value="dialogModelValueUpdate"
  >
    <!-- 👉 Header -->
    <div class="d-flex align-center pa-6 pb-1">
      <h6 class="text-h6">
        {{ event.id ? 'Update' : 'Add' }} Event
      </h6>

      <VSpacer />

      <VBtn
        v-show="event.id"
        icon
        variant="tonal"
        size="32"
        class="rounded me-3"
        color="default"
        @click="removeEvent"
      >
        <VIcon
          size="18"
          icon="tabler-trash"
        />
      </VBtn>

      <VBtn
        variant="tonal"
        color="default"
        icon
        size="32"
        class="rounded"
        @click="$emit('update:isDrawerOpen', false)"
      >
        <VIcon
          size="18"
          icon="tabler-x"
        />
      </VBTn>
    </div>

    <PerfectScrollbar :options="{ wheelPropagation: false }">
      <VCard flat>
        <VCardText>
          <!-- SECTION Form -->
          <VForm
            ref="refForm"
            @submit.prevent="handleSubmit"
          >
            <VRow>
              <!-- 👉 Title -->
              <VCol cols="12">
                <VTextField
                  v-model="event.title"
                  label="Title"
                  :rules="[requiredValidator]"
                />
              </VCol>

              <!-- 👉 Calendar -->

              <!-- 👉 Start date -->
              <VCol cols="12">
                <AppDateTimePicker
                  :key="JSON.stringify(startDateTimePickerConfig)"
                  v-model="event.start"
                  :rules="[requiredValidator]"
                  label="Start date"
                  :config="startDateTimePickerConfig"
                />
              </VCol>

              <!-- 👉 End date -->
              <VCol cols="12">
                <AppDateTimePicker
                  :key="JSON.stringify(endDateTimePickerConfig)"
                  v-model="event.end"
                  :rules="[requiredValidator]"
                  label="End date"
                  :config="endDateTimePickerConfig"
                />
              </VCol>

              <!-- 👉 All day -->

              <!-- 👉 Event URL -->

              <!-- 👉 Guests -->

              <!-- 👉 Location -->

              <!-- 👉 Description -->
              <VCol cols="12">
                <VTextarea
                  v-model="event.description"
                  label="Description"
                  :rules="[requiredValidator]"

                />
              </VCol>

              <!-- 👉 Form buttons -->
              <VCol cols="12">
                <VBtn
                  type="submit"
                  class="me-3"
                >
                  Submit
                </VBtn>
                <VBtn
                  variant="tonal"
                  color="secondary"
                  @click="onCancel"
                >
                  Cancel
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
          <!-- !SECTION -->
        </VCardText>
      </VCard>
    </PerfectScrollbar>
  </VNavigationDrawer>
</template>
