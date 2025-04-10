<template>
  <IonPage>
    <Navbar title="Pujar arxiu" />
    <IonContent class="ion-padding">
      <div class="mb-4">
        <input
            type="text"
            v-model="title"
            placeholder="Títol"
            class="w-full p-2 border rounded mb-4"
        />
        <select v-model="type" class="w-full p-2 border rounded mb-4">
          <option disabled value="">Selecciona tipus</option>
          <option value="image">Imatge</option>
          <option value="video">Vídeo</option>
        </select>

        <FilePond
            name="file"
            ref="pond"
            :server="server"
            :allow-multiple="false"
            :chunkUploads="false"
            :instantUpload="true"
            accepted-file-types="image/jpeg, image/png, video/mp4, video/x-mp4"
            label-idle="📁 Arrossega o fes clic per pujar"
        />
      </div>

      <Footer />
    </IonContent>
  </IonPage>
</template>

<script setup>
import { IonPage, IonContent, toastController } from '@ionic/vue'
import Navbar from '../components/Navbar.vue'
import Footer from '../components/Footer.vue'
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

// FilePond
import vueFilePond from 'vue-filepond'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size'
import 'filepond/dist/filepond.min.css'

const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginFileValidateSize)

const title = ref('')
const type = ref('')
const pond = ref(null)
const router = useRouter()

const showToast = async (message, color = 'success') => {
  const toast = await toastController.create({
    message,
    duration: 2500,
    color,
    position: 'top'
  })
  toast.present()
}

const server = {
  process: async (fieldName, file, metadata, load, error, progress, abort) => {
    if (!title.value || !type.value) {
      error('Omple el títol i el tipus.')
      showToast('Has d’omplir tots els camps!', 'warning')
      return
    }

    const formData = new FormData()
    formData.append('title', title.value)
    formData.append('type', type.value)
    formData.append('file', file, file.name)

    console.log('📤 Fitxer a enviar:', {
      name: file.name,
      size: file.size,
      type: file.type
    })

    try {
      const response = await axios.post('http://localhost:8000/api/multimedia', formData, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'multipart/form-data'
        },
        onUploadProgress: e => {
          progress(true, e.loaded, e.total)
        }
      })

      showToast('✅ Fitxer pujat correctament!')
      load(response.data.id.toString())

      // 🧼 Neteja del formulari
      title.value = ''
      type.value = ''
      if (pond.value) {
        pond.value.removeFile()
      }

      // 🔁 Redirigim després d’un petit delay perquè es vegi el toast
      setTimeout(() => {
        router.push('/home')
      }, 1000)

    } catch (err) {
      console.error('❌ Error en la pujada:', err)

      if (err.response?.status === 413) {
        showToast('⚠️ Fitxer massa gran! Màxim 20MB.', 'danger')
      } else if (err.response?.status === 422) {
        showToast('⚠️ Error de validació. Revisa camps i format.', 'warning')
      } else {
        showToast('❌ Error desconegut en la pujada.', 'danger')
      }

      error('Error en la pujada.')
    }
  },

  revert: (uniqueFileId, load, error) => {
    load()
  }
}
</script>

<style scoped>
input, select {
  font-size: 16px;
}
</style>
