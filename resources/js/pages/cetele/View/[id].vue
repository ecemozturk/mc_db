<script setup>
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import useCetele from '@/composables/useCetele';
import useUser from '@/composables/useUser';
import {useCities} from "@/composables/api/useCities";

const route = useRoute();
const ceteleId = route.params.id;
const cetele = ref([]);
const ceteleData = ref({});
const { cities, fetchCities } = useCities();

const name = (userId) => {
  if (!Array.isArray(useUser.userList.value)) {
    return 'unknown';
  }

  const user = useUser.userList.value.find((user) => user.id === userId)
  return user ? user.name : 'Unknown';
};

onMounted(async () => {
  await useUser.getUserList(); // Fetch the user list first
  ceteleData.value = await useCetele.getCeteleById(ceteleId);
  const userName = name(ceteleData.value.users_id);

  cetele.value = [
    { title: 'ID', value: ceteleData.value.id },
    { title: 'Görüşen Gönüllü', value: userName },
    { title: 'Arama Tarihi', value: ceteleData.value.arama_tarihi },
    { title: 'Anonim Mi', value: ceteleData.value.anonim_numara },
    { title: 'Arayan Adsoyad', value: ceteleData.value.arayan_adsoyad },
    { title: 'Arayan Ülke', value: ceteleData.value.arayan_ulke },
    { title: 'Arayan Şehir', value: ceteleData.value.arayan_sehir },
    { title: 'Gizli Numara mı', value: ceteleData.value.gizli_numara },
    { title: 'Arayan Numara', value: ceteleData.value.arayan_numara },
    { title: 'Kimin için Aradı', value: ceteleData.value.arayan_kimin_icin },
    { title: 'Ne Yapıldı', value: ceteleData.value.ne_yapildi },
    { title: 'Yönlendirilen Kurumlar', value: ceteleData.value.yonlendirilen_kurumlar },
    // Add other properties in the same format
  ];
});

</script>






<template>
  <VRow>
    <VCol cols="12">
      <!-- 👉 Main Card  -->
      <AppCardActions
        :title="`Çetele #${ceteleData.id}`"


        @refresh="refetchData"
      >

        <VCardText>
          <VTable>
            <thead>
            <tr>
              <th scope="col">
                Form Elementi
              </th>

              <th scope="col">
                Bilgi
              </th>
            </tr>
            </thead>
            <tbody>
            <tr
              v-for="data in cetele"
              :key="data.title"
            >
              <td>
                {{ data.title }}
              </td>

              <td>
                {{ data.value }}
              </td>
            </tr>
            </tbody>
          </VTable>
        </VCardText>
      </AppCardActions>
    </VCol>
  </VRow>
</template>

