<template>
  <VRow>
    <VCol cols="12">
      <VCard title="Create Cetele">
        <VCardText>
          <VRow>

            <!--Step1 Volunteers-->
            <VCol cols="8" md="8">
              <VSelect
                v-model="ceteleData.users_id"
                :items="users_id"
                label="Görüşme Yapan Gönüllü"
                :rules="[requiredValidator]"
                density="compact"
              />
            </VCol>
            <!--TODO Add dark theme for mcDataTimePicker-->
            <!-- Step 2 Arama Tarihi-->
            <VCol cols="12" md="6">
              <mcDateTimePicker
                v-model="ceteleData.arama_tarihi"
                label="Arama Tarihi"
                :rules="[requiredValidator]"
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
                :rules="[requiredValidator]"
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
            <!-- Step 8 Arayan Numara -->
            <VCol cols="12" md="6" v-if="!ceteleData.gizli_numara">
              <VTextField
                v-model="ceteleData.arayan_numara"
                label="Arayan Numarası"
              />
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
                value="yonlendirilen_kurumlar"
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
            <!-- Step 11 Yönlendirilen Kurumlar -->
            <!--TODO Yönlendilrilen Kurumlar not saved in database-->
            <VCol cols="8" md="8" v-if="ceteleData.ne_yapildi.includes('yonlendirilen_kurumlar')">
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

<!-- TODO: Add options textarea for diğer seçenek -->

            <VCol cols="8" md="8">
              <VSelect
                v-model="ceteleData.mc_nereden_duydu"
                :items="[
        'İnternet',
        'Sosyal medya hesapları',
        'Kitle İletişim Araçları',
        'Kurum Yönlendirmesi',
        'Tanıdık Tavsiyesi',
        'Diğer'
      ]"
                label="Mor Çatı'dan Nasıl Haberdar Oldu?"
              />
            </VCol>

            <!-- Step 12.1 Diğer -->
            <VCol cols="8" md="8" v-if="ceteleData.mc_nereden_duydu === 'Diğer'">
              <VTextField
                v-model="ceteleData.mc_nereden_duydu_diger"
                label="Diğer (Açıklama)"
                hint="Mor Çatı'dan nasıl haberdar olduğunu açıklayınız."
                persistent-hint
              />
            </VCol>

            <!-- Step 13 Çetele Notları -->
            <VCol cols="8" md="8">
              <VTextarea
                v-model="ceteleData.cetele_notlari"
                label="Çetele Notları"
                hint="Çetele ile ilgili ek bilgiler veya notlarınızı buraya yazabilirsiniz."
                persistent-hint
              />
            </VCol>

            <!-- Submit Button -->
            <VCol cols="8" md="8">
              <VBtn
                color="primary"
                @click="submitForm"
              >
                Çetele Formunu Gönder
              </VBtn>
            </VCol>
          </VRow>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>


<script setup>

import {
  reactive,
  ref,
  onMounted,
  watch,
  computed,
} from 'vue';
import { useRouter } from 'vue-router';
import moment from 'moment';
import Swal from 'sweetalert2';

import axios from 'axios';
import { useKurum } from '@/composables/api/useKurum';
import { useCities } from '@/composables/api/useCities';
import { useCountry } from '@/composables/api/useCountry';
import mcDateTimePicker from "@/composables/templates/mcDateTimePicker.vue";
import dayjs from 'dayjs';
import { createCetele as createCeteleComposable } from '@/composables/useCetele';




/*TODO Configure validation rules*/
const ceteleData = reactive({
  arayan_kimin_icin: '',
  ne_yapildi: [], // Initialize ne_yapildi as an empty array
  yonlendirilen_kurumlar: [],
  anonim_numara: false,
  gizli_numara: false,
  arama_tarihi: dayjs().format('DD.MM.YYYY HH:mm:ss'),
  arayan_adsoyad: '',
  arayan_ulke: '',
  id: '',
});

const date = ref('');
const { countries, fetchCountries } = useCountry();
const { cities, fetchCities } = useCities();
const router = useRouter()

const isParentSelected = (parentKurum) => {
  return ceteleData.yonlendirilen_kurumlar.includes(parentKurum.id);
};

const isChildSelected = (childKurum) => {
  return ceteleData.yonlendirilen_kurumlar.includes(childKurum.id);
};

const users_id = ref([]);
const inlineRadio = ref(null);
const { kurumlar, fetchKurumlar } = useKurum();
const errorMessages = ref([]); // Add errorMessages ref
const snackbarVisible = ref(false); // Add snackbarVisible ref

/*TODO List user name instead of user id*/
async function fetchVolunteers() {
  try {
    const response = await axios.get('/api/users');
    console.log('Response data:', response.data);
    users_id.value = response.data.data.map(user => user.id);
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

//KURUM YÖNLENDİRMESİ

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
    const dataToSend = { ...ceteleData };

    // Convert arayan_sehir, arayan_ulke to strings
    dataToSend.arayan_sehir = ceteleData.arayan_sehir?.name || '';
    dataToSend.arayan_ulke = ceteleData.arayan_ulke?.name || '';

    // Convert ne_yapildi and yonlendirilen_kurumlar to comma-separated strings
    dataToSend.ne_yapildi = ceteleData.ne_yapildi.join(',') || '';
    dataToSend.yonlendirilen_kurumlar = ceteleData.yonlendirilen_kurumlar.length > 0 ? ceteleData.yonlendirilen_kurumlar.join(',') : '';

    // Set default values for nullable fields
    dataToSend.yonlendirilen_kurumlar = dataToSend.yonlendirilen_kurumlar || '';
    dataToSend.mc_nereden_duydu = dataToSend.mc_nereden_duydu || '';
    dataToSend.cetele_notlari = dataToSend.cetele_notlari || '';

    // Convert arama_tarihi to a format compatible with MySQL's datetime field
    if (ceteleData.arama_tarihi) {
      const aramaTarihi = moment(ceteleData.arama_tarihi, 'DD.MM.YYYY HH:mm:ss');
      dataToSend.arama_tarihi = aramaTarihi.format('YYYY-MM-DD HH:mm:ss');
    }

  //  const ecem = await createCeteleComposable(dataToSend);


   // const createdCeteleId = ecem.data.id;
   router.push(`/cetele/list`);


    Swal.fire({
      icon: 'success',
      title: 'Kaydedildi!',
      text: 'Çetele formu kaydedildi. Formu görüntüleyin.',
      confirmButtonText: 'OK'
    })
      // After the user clicks 'OK' on the Swal dialog, redirect to the single form view




    // You can add your success handling logic here
  } catch (error) {
    console.error('Error creating cetele:', error);

    Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: 'Something went wrong!',
      confirmButtonText: 'OK'
    });
  }
}


function resetForm() {
  Object.assign(ceteleData, {
    arayan_kimin_icin: '',
    ne_yapildi: [],
    yonlendirilen_kurumlar: [],
    arama_tarihi: '',
    anonim_numara: 'Bilinen Numara',
    users_id: '',
    arayan_ulke: '',
    arayan_adsoyad: '',
  });
}



watch(
  () => defaultCountry.value,
  (newValue) => {
    if (newValue) {
      ceteleData.arayan_ulke = newValue;
    }
  },
  { immediate: true }
);

</script>
