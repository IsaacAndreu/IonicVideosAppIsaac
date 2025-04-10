<!-- src/components/Navbar.vue -->
<template>
  <IonHeader>
    <IonToolbar>
      <IonTitle>IonicVideosApp</IonTitle>
      <IonButtons slot="end">
        <IonButton router-link="/home">Home</IonButton>
        <IonButton router-link="/my-files">Els meus arxius</IonButton>
        <IonButton router-link="/upload">Afegir</IonButton>
        <IonButton @click="logout">Sortir</IonButton>
      </IonButtons>
    </IonToolbar>
  </IonHeader>
</template>

<script setup>
import { IonHeader, IonToolbar, IonTitle, IonButtons, IonButton } from '@ionic/vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const logout = () => {
  localStorage.removeItem('token')
  router.push('/login')
}
</script>
