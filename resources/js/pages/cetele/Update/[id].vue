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
import {computed, defineComponent, onMounted, reactive, ref, watch} from 'vue';
import { useKurum } from '@/composables/api/useKurum';
import { useCities } from '@/composables/api/useCities';
import { useCountry } from '@/composables/api/useCountry';
import mcDateTimePicker from "@/composables/templates/mcDateTimePicker.vue";
import dayjs from 'dayjs';
import axios from "axios";
import createCetele from "@/composables/useCetele";
import useCetele from '@/composables/useCetele';
import {useRoute} from "vue-router";
import useUser from "@/composables/useUser";


const name = (userId) => {
  if (!Array.isArray(useUser.userList.value)) {
    return 'unknown';
  }

  const user = useUser.userList.value.find((user) => user.id === userId)
  return user ? user.name : 'Unknown';
};

export default defineComponent({
  components: {
    mcDateTimePicker,
  },
  setup() {
    const route = useRoute();

    const ceteleId = route.params.id;


    const ceteleData = reactive({
      arayan_kimin_icin: '',
      ne_yapildi: [], // Initialize ne_yapildi as an empty array
      yonlendirilen_kurumlar: [],
      anonim_numara: false,
      gizli_numara: false,
      arama_tarihi: dayjs().format('DD.MM.YYYY HH:mm:ss'),
      arayan_adsoyad: '',
      arayan_ulke: '',


    });


    const cetele = ref({});

    const date = ref('');

    const { countries, fetchCountries } = useCountry();
    const { cities, fetchCities } = useCities();

    const isParentSelected = (parentKurum) => {
      return ceteleData.yonlendirilen_kurumlar.includes(parentKurum.id);
    };

    const isChildSelected = (childKurum) => {
      return ceteleData.yonlendirilen_kurumlar.includes(childKurum.id);
    };

    const volunteers = ref([]);
    const inlineRadio = ref(null);
    const { kurumlar, fetchKurumlar } = useKurum();
    const errorMessages = ref([]); // Add errorMessages ref
    const snackbarVisible = ref(false); // Add snackbarVisible ref

    async function fetchVolunteers() {
      try {
        const response = await axios.get('/api/users');
        console.log('Response data:', response.data);
        volunteers.value = response.data.data.map(user => user.name);
      } catch (error) {
        console.error('Error fetching volunteers:', error);
      }
    };

    function onlyNumbersAndPlus(value) {
      const regex = /^[0-9+]+$/;
      return regex.test(value) || 'Only numbers and "+" are allowed';
    }

    onMounted(async () => {
      await fetchVolunteers();
      await fetchCountries();
      await fetchCities();
      await fetchKurumlar();
    });

    function handleParentCheckboxChange(parentKurum) {
      const parentIndex = ceteleData.yonlendirilen_kurumlar.indexOf(parentKurum.id);
      if (parentIndex > -1) {
        // If the parent is selected, select all child items
        if (parentKurum.children) {
          parentKurum.children.forEach(childKurum => {
            if (!ceteleData.yonlendirilen_kurumlar.includes(childKurum.id)) {
              ceteleData.yonlendirilen_kurumlar.push(childKurum.id);
            }
          });
        }
      } else {
        // If the parent is deselected, deselect all child items
        if (parentKurum.children) {
          parentKurum.children.forEach(childKurum => {
            const childIndex = ceteleData.yonlendirilen_kurumlar.indexOf(childKurum.id);
            if (childIndex > -1) {
              ceteleData.yonlendirilen_kurumlar.splice(childIndex, 1);
            }
          });
        }
      }
    }

    function handleChildCheckboxChange(parentKurum, childKurum) {
      const childIndex = ceteleData.yonlendirilen_kurumlar.indexOf(childKurum.id);
      if (childIndex > -1) {
        // If the child is selected, select the parent item
        if (!ceteleData.yonlendirilen_kurumlar.includes(parentKurum.id)) {
          ceteleData.yonlendirilen_kurumlar.push(parentKurum.id);
        }
      } else {
        // If all child items are deselected, deselect the parent item
        const allChildrenDeselected = parentKurum.children.every(
          child => !ceteleData.yonlendirilen_kurumlar.includes(child.id)
        );
        if (allChildrenDeselected) {
          const parentIndex = ceteleData.yonlendirilen_kurumlar.indexOf(parentKurum.id);
          if (parentIndex > -1) {
            ceteleData.yonlendirilen_kurumlar.splice(parentIndex, 1);
          }
        }
      }
    }

    // Define your submitForm and other functions here
    function submitForm() {
      // Your submitForm logic
    }

    const defaultCountry = computed(() => {
      return countries.value.find(country => country.name === 'Türkiye');
    });

    const cityRequired = (value) => {
      if (ceteleData.arayan_ulke === "Türkiye") {
        return !!value || "Lütfen bir şehir seçin";
      }
      return true;
    };

    const isTurkeySelected = computed(() => {
      return ceteleData.arayan_ulke && ceteleData.arayan_ulke.name === 'Türkiye';
    });


    async function submitForm() {
      try {
        await createCetele(ceteleData.value);
        console.log('cetele created successfully');

        // You can add your success handling logic here
      } catch (error) {
        console.error('Error creating cetele:', error);
        // You can add error handling logic here
      }
    }


    function resetForm() {
      Object.assign(ceteleData, {
        arayan_kimin_icin: '',
        ne_yapildi: [],
        yonlendirilen_kurumlar: [],
        arama_tarihi: '',
        anonim_numara: 'Bilinen Numara',
        volunteers: '',
        arayan_ulke: '',
        arayan_adsoyad: '',

      });
    }

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



    // Set the ceteleData.arayan_ulke to defaultCountry
    watch(
      () => defaultCountry.value,
      (newValue) => {
        if (newValue) {
          ceteleData.arayan_ulke = newValue;
        }
      },
      { immediate: true }
    );



    return {
      ceteleData,
      isTurkeySelected,
      cityRequired,
      submitForm,
      resetForm,
      errorMessages,
      snackbarVisible,
      volunteers,
      countries,
      cities,
      onlyNumbersAndPlus,
      inlineRadio,
      kurumlar,
      handleParentCheckboxChange,
      handleChildCheckboxChange,
      isParentSelected,
      isChildSelected,


    };
  },
});
</script>

<style scoped>
/* Add any additional styling here */
</style>


<style scoped>
/* Add any additional styling here */
</style>
