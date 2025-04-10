<template>
  <IonPage>
    <Navbar title="Els meus arxius" />
    <IonContent>
      <IonGrid>
        <IonRow>
          <IonCol v-for="file in myFiles" :key="file.id" size="12" size-md="6" size-lg="4">
            <IonCard>
              <IonCardHeader>
                <IonCardTitle>{{ file.title }}</IonCardTitle>
              </IonCardHeader>
              <IonCardContent>
                <img
                    v-if="file.type === 'image'"
                    :src="getFullPath(file.file_path)"
                    alt="Imatge"
                    class="w-full rounded"
                />
                <video
                    v-else-if="file.type === 'video'"
                    controls
                    class="w-full rounded"
                >
                  <source :src="getFullPath(file.file_path)" type="video/mp4" />
                  El teu navegador no suporta vídeos.
                </video>
                <div class="flex justify-between mt-3">
                  <IonButton color="danger" size="small" @click="deleteFile(file.id)">🗑️</IonButton>
                  <IonButton size="small" @click="editFile(file.id)">✏️</IonButton>
                </div>
              </IonCardContent>
            </IonCard>
          </IonCol>
        </IonRow>
      </IonGrid>
      <Footer />
    </IonContent>
  </IonPage>
</template>

<script setup>
import {
  IonPage,
  IonContent,
  IonGrid,
  IonRow,
  IonCol,
  IonCard,
  IonCardHeader,
  IonCardTitle,
  IonCardContent,
  IonButton,
  onIonViewWillEnter
} from '@ionic/vue'
import Navbar from '../components/Navbar.vue'
import Footer from '../components/Footer.vue'
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import { emitter } from '../events'

const myFiles = ref([])
const router = useRouter()

const getFullPath = (path) => {
  return path.startsWith('http') ? path : `http://localhost:8000${path}`
}

const fetchMyFiles = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get('http://localhost:8000/api/user/multimedia', {
      headers: { Authorization: `Bearer ${token}` }
    })
    myFiles.value = response.data
  } catch (error) {
    console.error('❌ Error carregant els arxius propis:', error)
  }
}

const deleteFile = async (id) => {
  try {
    const token = localStorage.getItem('token')
    await axios.delete(`http://localhost:8000/api/multimedia/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    myFiles.value = myFiles.value.filter(file => file.id !== id)
    emitter.emit('file-deleted', id) // 🔥 Notifica el Home que hi ha canvis
  } catch (error) {
    console.error('❌ Error eliminant el fitxer:', error)
  }
}

const editFile = (id) => {
  router.push(`/edit/${id}`)
}

onIonViewWillEnter(fetchMyFiles)
</script>

<style scoped>
video,
img {
  max-height: 250px;
  object-fit: cover;
}
.flex {
  display: flex;
  gap: 10px;
  justify-content: space-between;
}
.mt-3 {
  margin-top: 1rem;
}
</style>
