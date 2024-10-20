<template>
  <div id="home">
    <Navbar />

    <div class="container">
      <h1>Bem-vindo ao Sistema de Agendamentos</h1>
      <p>Utilize o menu para navegar entre as páginas.</p>
    </div>
  
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
