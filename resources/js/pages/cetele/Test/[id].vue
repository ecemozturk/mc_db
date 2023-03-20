<template>
  <VCard id="cetele-update">
    <VCardText>
      <h2>Update Cetele</h2>
      <VForm ref="form" @submit="submitForm">
        <VTextField
          label="Arayan Numara"
          v-model="cetele.arayan_numara"
          required
        />
        <VTextField
          label="Arama Tarihi"
          v-model="cetele.arama_tarihi"
          required
        />
        <VTextField
          label="Arayan Adsoyad"
          v-model="cetele.arayan_adsoyad"
          required
        />
        <VSelect
          label="Arayan Şehir"
          :items="filteredCities"
          :item-title="city => city.name"
          :item-value="city => city.code"
          v-model="selectedCity"
          required
        />
        <VBtn type="submit" color="primary">
          Update Cetele
        </VBtn>
      </VForm>
    </VCardText>
  </VCard>
</template>

<script setup>
import { onMounted, ref, watchEffect, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import useCetele from '@/composables/useCetele';
import { useCities } from '@/composables/api/useCities';

const router = useRouter();
const route = useRoute();
const ceteleId = route.params.id;
const cetele = ref({ arayan_sehir: '' });
const selectedCity = ref(null);
const { cities, fetchCities } = useCities();
const form = ref(null);

onMounted(async () => {
  await fetchCities();
});


onMounted(async () => {
  cetele.value = await useCetele.getCeteleById(ceteleId);
});

const filteredCities = computed(() => {
  return cities.value ? cities.value.filter(city => city.code !== cetele.value.arayan_sehir) : [];
});


watchEffect(() => {
  if (cities.value && cetele.value.arayan_sehir) {
    selectedCity.value = cities.value.find(city => city.code === cetele.value.arayan_sehir);
  }
});

const submitForm = async () => {
  if (!form.value.validate()) {
    return;
  }

  await useCetele.updateCetele(ceteleId, { ...cetele.value, arayan_sehir: selectedCity.value.code });
  router.push({ name: 'cetele-list' });
};
</script>
