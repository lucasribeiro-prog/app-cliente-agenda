<template>
  <div id="consultar">
    <!-- Navbar -->
    <Navbar />

    <div class="container">
      <h1><b>Consultar Agendamentos</b></h1>

      <!-- Dropdown para cada dia da semana -->
      <div v-for="(day, index) in days" :key="index" class="day-section">
        <div @click="toggleDropdown(index)" class="dropdown-toggle">
          {{ day.name }}
        </div>

        <div v-if="day.isOpen" class="dropdown-content">
          <table>
            <thead>
              <tr>
                <th>Hora</th>
                <th>Cliente</th>
                <th>Consultor</th>
                <th>Opções</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="agendamento in day.agendamentos" :key="agendamento.id">
                <td>{{ agendamento.hora }}</td>
                <td>{{ agendamento.clientes.nome }}</td>
                <td>{{ agendamento.usuarios.name }}</td> 
                <td>
                  <button class="detalhes" @click="viewDetails(agendamento.id)">Detalhes</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Navbar from '../components/Navbar.vue';
import { usePage } from '@inertiajs/vue3';

export default {
  components: {
    Navbar,
  },
  setup() {
    const { props } = usePage();
    const user = props.auth.user;

    const days = ref([
      { name: 'Segunda-feira', isOpen: false, agendamentos: [] },
      { name: 'Terça-feira', isOpen: false, agendamentos: [] },
      { name: 'Quarta-feira', isOpen: false, agendamentos: [] },
      { name: 'Quinta-feira', isOpen: false, agendamentos: [] },
      { name: 'Sexta-feira', isOpen: false, agendamentos: [] },
      { name: 'Sábado', isOpen: false, agendamentos: [] },
      { name: 'Domingo', isOpen: false, agendamentos: [] },
    ]);

    const toggleDropdown = (index) => {
      days.value[index].isOpen = !days.value[index].isOpen;
    };

    const buscarAgendamentos = async () => {
      try {
        const response = await axios.get('http://localhost:8000/api/agendar');
        const agendamentos = response.data;

        days.value.forEach(day => {
          day.agendamentos = [];
        });

        agendamentos.forEach(agendamento => {
          const date = new Date(agendamento.data);
          const weekDayIndex = date.getDay(); // 0 = Domingo, 1 = Segunda, ..., 6 = Sábado
          days.value[weekDayIndex].agendamentos.push(agendamento);
        });
      } catch (error) {
        console.error('Erro ao carregar agendamentos:', error);
      }
    };

    const viewDetails = (id) => {
      // Lógica para exibir os detalhes do agendamento
      alert(`Detalhes do agendamento: ${id}`);
    };

    onMounted(() => {
      buscarAgendamentos();
    });

    return {
      days,
      toggleDropdown,
      viewDetails,
      user,
    };
  },
};
</script>

<style>
.container {
  max-width: 1200px;
  margin: 0 auto;
  margin-top: 25px;
  padding: 1rem;
}

.day-section {
  margin-bottom: 1rem;
}

.dropdown-toggle {
  background-color: #007bff;
  color: white;
  padding: 0.5rem 1rem;
  cursor: pointer;
  border-radius: 5px;
}

.dropdown-content {
  margin-top: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 5px;
  padding: 1rem;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 0.75rem;
  border: 1px solid #ddd;
}

td {
  text-align: center;;
}

.detalhes {
  background-color: #28a745;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  cursor: pointer;
  margin-left: 10px;
}

.detalhes:hover {
  background-color: #218838;
}

h1 {
  margin-bottom: 25px;
}
</style>
