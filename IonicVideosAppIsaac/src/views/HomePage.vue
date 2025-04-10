<template>
  <IonPage>
    <Navbar />

    <IonContent>
      <IonGrid>
        <IonRow>
          <IonCol
              v-for="file in multimedia"
              :key="file.id"
              size="12"
              size-md="6"
              size-lg="4"
          >
            <IonCard>
              <IonCardHeader>
                <IonCardTitle>{{ file.title }}</IonCardTitle>
              </IonCardHeader>
              <IonCardContent>
                <img
                    v-if="file.type === 'image'"
                    :src="file.file_path"
                    alt="Imatge"
                    class="w-full rounded"
                />
                <video
                    v-else-if="file.type === 'video'"
                    controls
                    class="w-full rounded"
                >
                  <source :src="file.file_path" type="video/mp4" />
                  El teu navegador no suporta vídeos.
                </video>
              </IonCardContent>
            </IonCard>
          </IonCol>

          <IonCol size="12" v-if="multimedia.length === 0">
            <p class="text-center mt-4">⚠️ No s'han trobat arxius multimèdia.</p>
          </IonCol>
        </IonRow>
      </IonGrid>
      <!-- 🦶 Footer aquí -->
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
  onIonViewWillEnter
} from '@ionic/vue'

import { ref, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'
import Navbar from '@/components/Navbar.vue'
import Footer from '@/components/Footer.vue' // 📥 importat aquí
import { emitter } from '@/events'

const multimedia = ref([])

const fetchMultimedia = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/multimedia', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    multimedia.value = response.data
  } catch (error) {
    console.error('❌ Error carregant els arxius:', error)
  }
}

onIonViewWillEnter(fetchMultimedia)

emitter.on('file-deleted', fetchMultimedia)
emitter.on('file-uploaded', fetchMultimedia)

onBeforeUnmount(() => {
  emitter.off('file-deleted', fetchMultimedia)
  emitter.off('file-uploaded', fetchMultimedia)
})
</script>

<style scoped>
video,
img {
  max-height: 250px;
  object-fit: cover;
}

.text-center {
  text-align: center;
}

.mt-4 {
  margin-top: 1rem;
}
</style>
