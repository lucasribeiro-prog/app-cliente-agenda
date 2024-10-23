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
                  <button class="detalhes" @click="viewDetails(agendamento)">Detalhes</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal para exibir detalhes do agendamento -->
    <Modal :show="showModal" @close="showModal = false">
      <template v-if="selectedAgendamento">
        <h1 class="text-center text-2xl font-bold mb-10">{{ selectedAgendamento.clientes.nome }}</h1>
        <p><strong>CPF:</strong> {{ selectedAgendamento.clientes.cpf }}</p>
        <p><strong>Telefone:</strong> {{ selectedAgendamento.contatos.telefone }}</p>
        <p><strong>Data do leilão:</strong> {{ selectedAgendamento.data_leilao }}</p>
        <p>
          <strong>Matrícula:</strong>
          <a href="selectedAgendamento.clientes.matricula" class="ml-2" target="_blank">
            <i class="fa-regular fa-file-pdf text-lg"></i>
          </a>
        </p>
      </template>
    </Modal>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Navbar from '../components/Navbar.vue';
import Modal from '../components/Modal.vue';
import axios from 'axios';
import { usePage } from '@inertiajs/vue3';

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

const showModal = ref(false); // Estado para controlar se o modal está aberto ou fechado
const selectedAgendamento = ref(null); // Agendamento selecionado para exibir os detalhes

const toggleDropdown = (index) => {
  days.value[index].isOpen = !days.value[index].isOpen;
};

const buscarAgendamentos = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/agendar');
    const agendamentos = response.data;

    // Reinicializa os agendamentos
    days.value.forEach(day => {
      day.agendamentos = [];
    });

    // Agrupa os agendamentos pelos dias da semana
    agendamentos.forEach(agendamento => {
      const date = new Date(agendamento.data);
      const weekDayIndex = date.getDay();
      days.value[weekDayIndex].agendamentos.push(agendamento);
    });
  } catch (error) {
    console.error('Erro ao carregar agendamentos:', error);
  }
};

const viewDetails = (agendamento) => {
  selectedAgendamento.value = agendamento; // Armazena o agendamento selecionado
  showModal.value = true; // Abre o modal
};

onMounted(() => {
  buscarAgendamentos();
});
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

.button-modal  {
  display: flex;
  justify-content: center;
}

h1 {
  margin-bottom: 25px;
}

strong {
  color: #0058b6;
  margin-right: 4px;
}

p {
  margin-bottom: 8px;
}
</style>
