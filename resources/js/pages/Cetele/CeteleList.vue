<template>
  <VCard id="cetele-list">
    <VCardText class="d-flex align-center flex-wrap gap-4">
      <!-- Rows per page -->
      <div class="d-flex align-center" style="width: 135px;">
        <span class="text-no-wrap me-3">Shows:</span>
        <VSelect v-model="rowsPerPage" density="compact" :items="[10, 20, 30, 50]" />
      </div>

      <div class="me-3">
        <!-- Create cetele -->
        <VBtn prepend-icon="tabler-plus" :to="{ name: 'cetele-create' }">
          Yeni Veri Ekle
        </VBtn>
      </div>

      <VSpacer />

    </VCardText>

    <VDivider />

    <!-- SECTION Table -->
    <VTable class="text-no-wrap cetele-list-table">
      <!-- Table head -->
      <thead class="text-uppercase">
      <tr>
        <th scope="col"></th>
        <th scope="col">          <VTextField v-model="searchUserName" placeholder="Gönüllüleri Filtrele" density="compact" class="filterArea" />
        </th>
        <th scope="col"></th>
        <th scope="col">          <VTextField v-model="searchArayanAdSoyad" placeholder="Arayan Adı Filtrele" density="compact" class="filterArea" />
        </th>
        <th scope="col">          <VTextField v-model="searchArayanSehir" placeholder="Şehir Filtrele" density="compact" class="filterArea" />
        </th>
        <th scope="col"></th>
      </tr>
      </thead>
      <thead class="text-uppercase">
      <tr>
        <th scope="col">#ID</th>
        <th scope="col">Görüşen Gönüllü</th>
        <th scope="col">Arama Tarihi/Saati</th>
        <th scope="col">Arayan Adı Soyadı</th>
        <th scope="col">Arayan Şehri</th>
        <th scope="col">İşlemler</th>
      </tr>
      </thead>

      <!-- Table Body -->
      <tbody>
      <template v-if="filteredCeteles.length === 0">
        <tr>
          <td colspan="6" class="text-center text-body-1">
            Hiçbir kayıt bulunamadı.
          </td>
        </tr>
      </template>

      <tr v-for="cetele in paginatedCeteles" v-else :key="cetele.id" style="height: 3.75rem;">
        <!-- Id -->
        <td>{{ cetele.id }}</td>

        <!-- Görüşen Gönüllü -->
        <td>{{ cetele.user ? cetele.user.name : 'Unknown' }}</td>

        <!-- Arama Tarihi -->
        <td>{{ cetele.users_id }}</td>

        <!-- Arayan Adsoyad -->
        <td>{{ cetele.arayan_adsoyad }}</td>

        <!-- Arayan Şehir -->
        <td>{{ cetele.arayan_sehir }}</td>

        <!-- Actions -->
        <td style="width: 8rem;">
          <!-- Add the 'to' attribute to the Edit button -->
          <VBtn
            icon
            variant="text"
            color="default"
            size="x-small"
            :to="{ name: 'cetele-update', params: { id: cetele.id } }"
          >
            <VIcon :size="22" icon="tabler-pencil" />
          </VBtn>

          <VBtn
            icon
            variant="text"
            color="default"
            size="x-small"
            :to="{ name: 'cetele-details', params: { id: cetele.id } }"
          >
            <VIcon :size="22" icon="tabler-eye" />
          </VBtn>
        </td>
      </tr>
      </tbody>

      <!-- Table footer -->
      <template v-if="filteredCeteles.length === 0">
        <tfoot>
        <tr>
          <td colspan="6" class="text-center text-body-1">
            No data available
          </td>
        </tr>
        </tfoot>
      </template>
    </VTable>
    <!-- !SECTION -->

    <VDivider />

    <!-- SECTION Pagination -->
    <VCardText class="d-flex align-center flex-wrap gap-4 py-3">
      <!-- Pagination meta -->
      <span class="text-sm text-disabled">
        {{ paginationData }}
      </span>

      <VSpacer />

      <!-- Pagination -->
      <VPagination
        v-model="currentPage"
        size="small"
        :total-visible="5"
        :length="totalPage"
        @next="selectedRows = []"
        @prev="selectedRows = []"
      />
    </VCardText>
    <!-- !SECTION -->
  </VCard>
</template>

<script setup>
import { computed, ref, watchEffect } from 'vue';
import useCetele from '@/composables/useCetele';
import useUser from '@/composables/useUser';
import { useRouter } from 'vue-router';

const router = useRouter();

const searchQuery = ref('');
const rowsPerPage = ref(10);
const currentPage = ref(1);

//filter
const searchUserName = ref('');
const searchArayanAdSoyad = ref('');
const searchArayanSehir = ref('');


const name = (userId) => {
  if (!Array.isArray(useUser.userList.value)) {
    return 'unknown';
  }

  const user = useUser.userList.value.find((user) => user.id === userId)
  return user ? user.name : 'Unknown';
};

const fetchCeteles = async () => {
  await Promise.all([useCetele.getCeteleList(), useUser.getUserList()]);
  console.log('User list:', useUser.userList.value);
  console.log('Cetele list:', useCetele.ceteleList.value);

  if (!Array.isArray(useUser.userList.value)) {
    console.error('User list is not an array');
    return;
  }

  const users = useUser.userList.value;
  console.log('Users:', users);

  useCetele.ceteleList.value = useCetele.ceteleList.value.map(cetele => {
    const user = users.find(user => user.id === cetele.users_id);
    return { ...cetele, user }; // Attach the user object to the cetele object
  });
};

//filter
const filteredCeteles = computed(() => {
  return useCetele.ceteleList.value.filter((cetele) => {
    const userName = name(cetele.users_id).toLowerCase();
    const arayanAdSoyad = cetele.arayan_adsoyad.toLowerCase();
    const arayanSehir = cetele.arayan_sehir.toLowerCase();

    const searchUserNameValue = searchUserName.value.toLowerCase();
    const searchArayanAdSoyadValue = searchArayanAdSoyad.value.toLowerCase();
    const searchArayanSehirValue = searchArayanSehir.value.toLowerCase();

    return (
      (searchUserNameValue === '' || userName.includes(searchUserNameValue)) &&
      (searchArayanAdSoyadValue === '' || arayanAdSoyad.includes(searchArayanAdSoyadValue)) &&
      (searchArayanSehirValue === '' || arayanSehir.includes(searchArayanSehirValue))
    );
  });
});

const paginatedCeteles = computed(() => {
  const start = (currentPage.value - 1) * rowsPerPage.value;
  const end = start + rowsPerPage.value;
  return filteredCeteles.value.slice(start, end);
});

const totalPage = computed(() => {
  return Math.ceil(filteredCeteles.value.length / rowsPerPage.value);
});

watchEffect(async () => {
  await fetchCeteles();
  console.log('filteredCeteles:', filteredCeteles.value);
});


</script>
<style scoped>
.filterArea {
  gap: 4px;
  margin: 10px 10px 10px -20px;
  max-width: 200px;
}
</style>

