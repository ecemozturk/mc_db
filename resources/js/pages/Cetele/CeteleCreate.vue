<template>
  <VRow>
    <VCol cols="12">
      <VCard title="Create Cetele">
        <VCardText>
          <VForm @submit.prevent="submitForm">
            <VRow>
              <!-- 👉 Gizli Numara -->
              <VCol cols="12" md="6">
                <VCheckbox v-model="ceteleData.gizli_numara" label="Gizli Numara" />
              </VCol>
              <!-- 👉 Arayan Numara -->
              <VCol cols="12" md="6">
                <VTextField v-model="ceteleData.arayan_numara" label="Arayan Numara" required />
              </VCol>
              <!-- 👉 Arama Tarihi -->
              <VCol cols="12" md="6">
                <VTextField v-model="ceteleData.arama_tarihi" label="Arama Tarihi" type="datetime-local" required />
              </VCol>
              <!-- 👉 Anonim Numara -->
              <VCol cols="12" md="6">
                <VCheckbox v-model="ceteleData.anonim_numara" label="Anonim Numara" />
              </VCol>
              <!-- 👉 Arayan Adsoyad -->
              <VCol cols="12" md="6">
                <VTextField v-model="ceteleData.arayan_adsoyad" label="Arayan Adsoyad" required />
              </VCol>
              <!-- 👉 Arayan Şehir -->
              <VCol cols="12" md="6">
                <VTextField v-model="ceteleData.arayan_sehir" label="Arayan Şehir" required />
              </VCol>
              <!-- 👉 Arayan Ülke -->
              <VCol cols="12" md="6">
                <VTextField v-model="ceteleData.arayan_ulke" label="Arayan Ülke" required />
              </VCol>
              <!-- 👉 Arayan Kimin İçin -->
              <VCol cols="12" md="6">
                <VTextField v-model="ceteleData.arayan_kimin_icin" label="Arayan Kimin İçin" required />
              </VCol>
              <!-- 👉 Ne Yapıldı -->
              <VCol cols="12" md="6">
                <VTextField v-model="ceteleData.ne_yapildi" label="Ne Yapıldı" required />
              </VCol>
              <!-- 👉 Yönlendirilen Kurumlar -->
              <VCol cols="12" md="6">
                <VTextField v-model="ceteleData.yonlendirilen_kurumlar" label="Yönlendirilen Kurumlar" required />
              </VCol>
              <!-- 👉 MC Nereden Duydu -->
              <VCol cols="12" md="6">
                <VTextField v-model="ceteleData.mc_nereden_duydu" label="MC Nereden Duydu" required />
              </VCol>
              <!-- 👉 Celete Notları -->
              <VCol cols="12" md="6">
                <VTextarea v-model="ceteleData.celete_notlari" label="Celete Notları" />
              </VCol>
              <!-- 👉 Form Actions -->
              <VCol cols="12" class="d-flex flex-wrap gap-4">
                <VBtn type="submit">Create</VBtn>
                <VBtn
                  color="secondary"
                  variant="tonal"
                  type="reset"
                  @click.prevent="resetForm"
                >
                  Reset
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>

  <!-- Error Messages -->
  <VRow v-if="errorMessages.length > 0">
    <VCol cols="12">
      <VAlert color="error" variant="tonal">
        <VAlertTitle>Error</VAlertTitle>
        <ul>
          <li v-for="message in errorMessages" :key="message">{{ message }}</li>
        </ul>
      </VAlert>
    </VCol>


  </VRow>

  <!-- Error Messages -->
  <VRow v-if="errorMessages.length > 0">
    <VCol cols="12">
      <VAlert color="error" variant="tonal">
        <VAlertTitle>Error</VAlertTitle>
        <ul>
          <li v-for="message in errorMessages" :key="message">{{ message }}</li>
        </ul>
      </VAlert>
    </VCol>
  </VRow>

  <!-- Success Snackbar -->
  <VSnackbar
    v-model="snackbarVisible"
    color="success"
    :timeout="5000"
  >
    Cetele successfully created!
  </VSnackbar>
</template>




<script>
import { reactive, ref, toRaw } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

export default {
  name: 'CeteleCreate',
  setup() {
    const ceteleData = reactive({
      gizli_numara: false,
      arayan_numara: '',
      arama_tarihi: '',
      anonim_numara: false,
      arayan_adsoyad: '',
      arayan_sehir: '',
      arayan_ulke: '',
      arayan_kimin_icin: '',
      ne_yapildi: '',
      yonlendirilen_kurumlar: '',
      mc_nereden_duydu: '',
      celete_notlari: '',
    });

    const errorMessages = ref([]);
    const snackbarVisible = ref(false);

    const router = useRouter();

    async function submitForm() {
      errorMessages.value = [];

      try {
        const response = await axios.post('/api/ceteles', toRaw(ceteleData));

        if (response.status === 201) {
          snackbarVisible.value = true;
          setTimeout(() => {
            router.push('/cetele/cetelelist');
          }, 5000);
        }
      } catch (error) {
        if (error.response && error.response.status === 422) {
          errorMessages.value = Object.values(error.response.data.errors).flat();
        } else {
          errorMessages.value.push('An error occurred while creating the cetele. Please try again.');
        }
      }
    }

    return {
      ceteleData,
      submitForm,
      errorMessages,
      snackbarVisible,
    };
  },
};
</script>

<style scoped>
/* Add any desired styles here */
</style>

