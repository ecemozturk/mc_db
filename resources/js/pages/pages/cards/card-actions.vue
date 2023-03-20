<script setup>
const ceteleData = {
  // Your cetele data object
}

const tableData = [
  { title: 'ID', value: ceteleData.id },
  { title: 'Arayan Numara', value: ceteleData.arayan_numara },
  { title: 'Arama Tarihi', value: ceteleData.arama_tarihi },
  { title: 'Arayan Adsoyad', value: ceteleData.arayan_adsoyad },
  { title: 'Arayan Şehir', value: ceteleData.arayan_sehir },
  // Add other properties in the same format
]
</script>

<template>
  <VRow>
    <VCol cols="12">
      <!-- 👉 Main Card  -->
      <AppCardActions
        title="Çetele #{{ ceteleData.id }}"
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
              v-for="data in tableData"
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
