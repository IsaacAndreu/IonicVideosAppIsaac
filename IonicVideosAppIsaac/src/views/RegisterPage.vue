<template>
  <IonPage>
    <IonHeader>
      <IonToolbar>
        <IonTitle>Registra't</IonTitle>
      </IonToolbar>
    </IonHeader>

    <IonContent class="ion-padding">
      <form @submit.prevent="handleRegister">
        <IonItem>
          <IonLabel position="floating">Nom</IonLabel>
          <IonInput v-model="name" type="text" required />
        </IonItem>

        <IonItem>
          <IonLabel position="floating">Email</IonLabel>
          <IonInput v-model="email" type="email" required />
        </IonItem>

        <IonItem>
          <IonLabel position="floating">Contrasenya</IonLabel>
          <IonInput v-model="password" type="password" required />
        </IonItem>

        <IonItem>
          <IonLabel position="floating">Confirma Contrasenya</IonLabel>
          <IonInput v-model="passwordConfirmation" type="password" required />
        </IonItem>

        <IonButton expand="block" type="submit" class="ion-margin-top">Registrar-se</IonButton>

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
const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const errorMessage = ref('')

const handleRegister = async () => {
  try {
    const response = await axios.post('http://localhost:8000/api/register', {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value
    })

    localStorage.setItem('token', response.data.token)
    await router.push('/home')
  } catch (error) {
    console.error(error)
    errorMessage.value = 'Error al registrar: comprova les dades'
  }
}
</script>

<style scoped>
.text-red-500 {
  color: red;
}
</style>
