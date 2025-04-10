<template>
  <IonPage>
    <IonHeader>
      <IonToolbar>
        <IonTitle>Inicia Sessió</IonTitle>
      </IonToolbar>
    </IonHeader>

    <IonContent class="ion-padding">
      <form @submit.prevent="handleLogin">
        <IonItem>
          <IonLabel position="floating">Email</IonLabel>
          <IonInput v-model="email" type="email" required />
        </IonItem>

        <IonItem>
          <IonLabel position="floating">Contrasenya</IonLabel>
          <IonInput v-model="password" type="password" required />
        </IonItem>

        <IonButton expand="block" type="submit" class="ion-margin-top">Entrar</IonButton>

        <IonButton expand="block" fill="clear" @click="router.push('/register')" class="ion-margin-top">
          Encara no tens compte? Registra't
        </IonButton>

        <p v-if="errorMessage" class="text-red-500 mt-4">{{ errorMessage }}</p>
      </form>
    </IonContent>
  </IonPage>
</template>


<script setup>
import {
  IonPage,
  IonHeader,
  IonToolbar,
  IonTitle,
  IonContent,
  IonItem,
  IonLabel,
  IonInput,
  IonButton
} from '@ionic/vue'

import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const router = useRouter()

const email = ref('')
const password = ref('')
const errorMessage = ref('')

const handleLogin = async () => {
  try {
    const response = await axios.post('http://localhost:8000/api/login', {
      email: email.value,
      password: password.value
    })

    // Desa el token i redirigeix
    localStorage.setItem('token', response.data.token)
    await router.push('/home')
  } catch (error) {
    errorMessage.value = 'Credencials incorrectes'
    console.error('❌ Error al login:', error)
  }
}
</script>

<style scoped>
.text-red-500 {
  color: red;
}
</style>
