<template>
  <IonPage>
    <Navbar title="Editar fitxer" />
    <IonContent class="ion-padding">
      <form @submit.prevent="handleUpdate">
        <input
            type="text"
            v-model="title"
            placeholder="Nou títol"
            class="w-full p-2 border rounded mb-4"
        />
        <select v-model="type" class="w-full p-2 border rounded mb-4">
          <option disabled value="">Selecciona tipus</option>
          <option value="image">Imatge</option>
          <option value="video">Vídeo</option>
        </select>

        <IonButton expand="block" type="submit" color="primary">Desar canvis</IonButton>
      </form>

      <!-- 🦶 Footer afegit -->
      <Footer />
    </IonContent>
  </IonPage>
</template>

<script setup>
import {IonPage, IonContent, IonButton, toastController} from '@ionic/vue'
import Navbar from '../components/Navbar.vue'
import Footer from '../components/Footer.vue'
import {ref, onMounted} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()
const fileId = route.params.id
const title = ref('')
const type = ref('')

const showToast = async (message, color = 'success') => {
  const toast = await toastController.create({
    message,
    duration: 2000,
    color,
    position: 'top'
  })
  toast.present()
}

onMounted(async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get(`http://localhost:8000/api/multimedia/${fileId}`, {
      headers: {Authorization: `Bearer ${token}`}
    })
    title.value = response.data.title
    type.value = response.data.type
  } catch (error) {
    showToast('❌ No s’ha pogut carregar el fitxer.', 'danger')
  }
})

const handleUpdate = async () => {
  try {
    const token = localStorage.getItem('token')
    await axios.put(`http://localhost:8000/api/multimedia/${fileId}`, {
      title: title.value,
      type: type.value
    }, {
      headers: {Authorization: `Bearer ${token}`}
    })

    showToast('✅ Fitxer actualitzat correctament!')
    router.push('/myfiles')
  } catch (error) {
    showToast('❌ Error actualitzant el fitxer.', 'danger')
  }
}
</script>

<style scoped>
input, select {
  font-size: 16px;
}
</style>
