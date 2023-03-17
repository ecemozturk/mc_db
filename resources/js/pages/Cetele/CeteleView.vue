<template>
  <VCard id="cetele-view">
    <VCardText>
      <h2>Cetele Details</h2>
      <p><strong>ID:</strong> {{ cetele.id }}</p>
      <p><strong>Arayan Numara:</strong> {{ cetele.arayan_numara }}</p>
      <p><strong>Arama Tarihi:</strong> {{ cetele.arama_tarihi }}</p>
      <p><strong>Arayan Adsoyad:</strong> {{ cetele.arayan_adsoyad }}</p>
      <p><strong>Arayan Şehir:</strong> {{ cetele.arayan_sehir }}</p>
    </VCardText>
  </VCard>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import useCetele from '@/composables/useCetele';

const route = useRoute();
const ceteleId = route.params.id;
const cetele = ref({});

onMounted(async () => {
  cetele.value = await useCetele.getCeteleById(ceteleId);
});
</script>
