<template>
  <div class="container">
      <Navbar />
      <h1>Bem-vindo ao Sistema de Agendamentos</h1>
      <p>Utilize o menu para navegar entre as páginas.</p>
  </div>
</template>

<script>
import Navbar from '../components/Navbar.vue';

export default {
  name: "Home",
  components: {
      Navbar,
  },
};
</script>

<style>
/* Estilos específicos da página inicial (opcional) */
</style>
