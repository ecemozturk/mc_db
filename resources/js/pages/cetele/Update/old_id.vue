<template>
  <VRow>
    <VCol cols="12">
      <VCard title="Update Cetele">
        <VCardText>
          <VRow>
            <!--Step1 Volunteers-->
            <VCol cols="8" md="8">
              <VSelect
                v-model="ceteleData.volunteers"
                :items="volunteers"
                label="Görüşme Yapan Gönüllü"
                density="compact"
              />
            </VCol>
            <!-- Step 2 Arama Tarihi-->
            <VCol cols="12" md="6">
              <mcDateTimePicker
                v-model="ceteleData.arama_tarihi"
                label="Arama Tarihi"
                :config="{ dateFormat: 'DD.MM.YYYY HH:mm:ss' }"
              />
            </VCol>
            <!-- Step 3 Anonim Numara Mı -->
            <VCol cols="12" md="6">
              <VRadioGroup v-model="ceteleData.anonim_numara" row>
                <VRadio label="Anonim Kişi" :value="true"/>
                <VRadio label="Anonim Değil" :value="false"/>
              </VRadioGroup>
            </VCol>
            <!-- Step 4 Arayan Adsoyad -->
            <VCol cols="12" md="6" v-if="!ceteleData.anonim_numara">
              <VTextField
                v-model="ceteleData.arayan_adsoyad"
                label="Arayan Adı Soyadı"
              />
            </VCol>
            <!-- Step 5 Ülke -->
            <VCol cols="8" md="8">
              <v-combobox
                v-model="ceteleData.arayan_ulke"
                :items="countries"
                :item-title="country => country.name"
                :item-value="country => country.code"
                label="Hangi Ülkeden Arıyor?"
                :menu-props="{ maxHeight: '250px' }"
              >
              </v-combobox>
            </VCol>
            <!-- Step 6 Şehir -->
            <VCol cols="8" md="8" v-if="isTurkeySelected">
              <v-combobox
                v-model="ceteleData.arayan_sehir"
                :items="cities"
                :item-title="cities => cities.name"
                :item-value="cities => cities.code"
                label="Hangi Şehirden Arıyor?"
                :menu-props="{ maxHeight: '250px' }"
              >
              </v-combobox>
            </VCol>
            <!-- Step 7 Gizli Numara -->
            <VCol cols="12" md="6">
              <VCheckbox v-model="ceteleData.gizli_numara" label="Gizli Numara"/>
            </VCol>
            <!-- Step 9 Kimin için Aradı -->
            <VCol cols="8" md="8">
              <VSelect
                v-model="ceteleData.arayan_kimin_icin"
                :items="['Kendisi', 'Başkası']"
                label="Kimin için Aradı?"
                value="Kendisi"
              />
            </VCol>
            <!-- Step 10 Ne Yapıldı -->
            <VCol cols="8" md="8">
              <p>Ne Yapıldı</p>
              <VCheckbox
                v-model="ceteleData.ne_yapildi"
                label="Sosyal çalışmacıya yönlendirildi"
                value="sosyal_calismaciya_yonlendirildi"
              />
              <VCheckbox
                v-model="ceteleData.ne_yapildi"
                label="Kurum yönlendirilmesi yapıldı"
                value="kurum_yonlendirilmesi"
              />
              <VCheckbox
                v-model="ceteleData.ne_yapildi"
                label="Tekrar araması söylendi"
                value="tekrar_aramasi"
              />
              <VCheckbox
                v-model="ceteleData.ne_yapildi"
                label="Kadının araması istendi"
                value="kadinin_aramasi"
              />
            </VCol>
            <!-- Step 11 Yönlendirilen Kurumlar -->
            <VCol cols="8" md="8" v-if="ceteleData.ne_yapildi.includes('kurum_yonlendirilmesi')">
              <v-label>Yönlendirilen Kurumlar</v-label>
              <div v-for="parentKurum in kurumlar" :key="parentKurum.id">
                <VCheckbox
                  :input-value="isParentSelected(parentKurum)"
                  :label="parentKurum.name"
                  @change="handleParentCheckboxChange(parentKurum)"
                />
                <div v-for="childKurum in parentKurum.children" :key="childKurum.id" class="ml-4">
                  <VCheckbox
                    :input-value="isChildSelected(childKurum)"
                    :label="childKurum.name"
                    @change="handleChildCheckboxChange(parentKurum, childKurum)"
                  />
                </div>
              </div>
            </VCol>

            <!-- Step 12 Mor Çatı'dan Nasıl Haberdar Oldu? -->
            <VCol cols="8" md="8">
              <VSelect
                v-model="ceteleData.mc_nereden_duydu"
                :items="[
      'İnternet',
      'Sosyal medya hesapları',
      'Kitle İletişim Araçları',
      'Kurum Yönlendirmesi',
      'Tanıdık Tavsiyesi',
      'Diğer',
    ]"
                label="Mor Çatı'dan Nasıl Haberdar Oldu?"
                value="İnternet"
              />
            </VCol>

            <VTextField
              v-model="ceteleData.arayan_numara"
              label="Arayan Numarası"
              ></VTextField>

            <!-- Step 12.1 Diğer -->
            <VCol cols="8" md="8" v-if="ceteleData.mc_nereden_duydu === 'Diğer'">
              <VTextField
                v-model="ceteleData.mc_nereden_duydu_diger"
                label="Diğer (Açıklama)"
              />
            </VCol>

            <!-- Step 13 Notlar -->
            <VCol cols="8" md="8">
              <VTextarea
                v-model="ceteleData.notlar"
                label="Notlar"
                auto-grow
                clearable
              />
            </VCol>

            <!-- Step 14 Submit Button -->
            <VCol cols="8" md="8">
              <VBtn
                color="primary"
                @click="submitCeteleForm"
              >
                Kaydet
              </VBtn>
            </VCol>
          </VRow>


          </VCardText></VCard>
    </VCol>
      </VRow>
</template>


<script>
import {
  reactive,
  ref,
  onMounted,
  watch,
  defineComponent,
  computed,
} from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { useKurum } from '@/composables/api/useKurum';
import { useCities } from '@/composables/api/useCities';
import { useCountry } from '@/composables/api/useCountry';
import mcDateTimePicker from "@/composables/templates/mcDateTimePicker.vue";
import dayjs from 'dayjs';
import useCetele from '@/composables/useCetele';

export default defineComponent({
  components: {
    mcDateTimePicker,
  },
  setup() {
    const route = useRoute();
    const ceteleId = route.params.id;
    const ceteleData = reactive({
      arayan_kimin_icin: '',
      ne_yapildi: [],
      yonlendirilen_kurumlar: [],
      anonim_numara: false,
      gizli_numara: false,
      arama_tarihi: dayjs().format('DD.MM.YYYY HH:mm:ss'),
      arayan_adsoyad: '',
      arayan_ulke: '',

    });

    // ... other refs and functions from create.vue

    onMounted(async () => {
      const route = useRoute();
      const id = route.params.id;
      const fetchedData = await useCetele.getCeteleById(id);

      if (fetchedData) {
        ceteleData.arayan_numara = fetchedData.arayan_numara;
        ceteleData.arayan_kimin_icin = fetchedData.arayan_kimin_icin;
        ceteleData.ne_yapildi = fetchedData.ne_yapildi;
        ceteleData.mc_nereden_duydu = fetchedData.mc_nereden_duydu;
        ceteleData.mc_nereden_duydu_diger = fetchedData.mc_nereden_duydu_diger;
        ceteleData.notlar = fetchedData.notlar;
      }
    });


    async function submitForm() {
      try {
        await updateCetele(ceteleId, ceteleData);
        console.log('cetele updated successfully');

        // You can add your success handling logic here
      } catch (error) {
        console.error('Error updating cetele:', error);
        // You can add error handling logic here
      }
    }

    // ... other functions from create.vue

    return {
      ceteleData,
      // ... other returned values from create.vue
    };
  },
});
</script>


<style scoped>
/* Add any additional styling here */
</style>
