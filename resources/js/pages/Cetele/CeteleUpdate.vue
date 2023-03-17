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
        <VTextField
          label="Arayan Şehir"
          v-model="cetele.arayan_sehir"
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
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import useCetele from '@/composables/useCetele';

const router = useRouter();
const route = useRoute();
const ceteleId = route.params.id;
const cetele = ref({});

onMounted(async () => {
  cetele.value = await useCetele.getCeteleById(ceteleId);
});

const form = ref(null);

const submitForm = async () => {
  if (!form.value.validate()) {
    return;
  }

  await useCetele.updateCetele(ceteleId, cetele.value);
  router.push({ name: 'cetele-list' });
};

</script>
