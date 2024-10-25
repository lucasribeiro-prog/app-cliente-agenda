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
                  <button class="detalhes bg-gray-600" @click="viewDetails(agendamento)" title="Detalhes">
                    <i class="fas fa-eye"></i>
                  </button>

                  <button class="editar bg-teal-500" @click="editar(agendamento)" title="Editar">
                    <i class="fas fa-pencil-alt"></i>
                  </button>

                  <button class="atendido bg-green-400" @click="openModal(agendamento, 'atendido')" title="Atendido">
                    <i class="fas fa-check"></i>
                  </button>

                  <button class="nao-compareceu bg-red-500" @click="openModal(agendamento, 'nao_compareceu')" title="Não Compareceu">
                    <i class="fas fa-times"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal para exibir detalhes do agendamento -->
    <Modal :show="showDetailsModal" @close="showDetailsModal = false">
      <template v-if="selectedAgendamento">
        <h1 class="text-center text-2xl font-bold mb-10">{{ selectedAgendamento.clientes.nome }}</h1>
        <p><strong>CPF:</strong> {{ selectedAgendamento.clientes.cpf }}</p>
        <p><strong>Telefone:</strong> {{ selectedAgendamento.contatos.telefone }}</p>
        <p><strong>Data do leilão:</strong> {{ selectedAgendamento.data_leilao }}</p>
        <p>
          <strong>Matrícula:</strong>
          <a :href="selectedAgendamento.clientes.matricula" class="ml-2" target="_blank">
            <i class="fa-regular fa-file-pdf text-lg"></i>
          </a>
        </p>
      </template>
    </Modal>

    <!-- Modal edição do agendamento -->
    <Modal :show="showEditModal" @close="showEditModal = false">
      <template v-if="selectedAgendamento">
        <h1 class="text-center text-2xl font-bold mb-10">Editar Agendamento</h1>
        <form @submit.prevent="submitForm">
          <input type="hidden" v-model="selectedAgendamento.id" placeholder="Nome" required />
          <input type="text" v-model="selectedAgendamento.clientes.nome" placeholder="Nome" required />
          <input type="tel" v-model="selectedAgendamento.contatos.telefone" placeholder="Telefone" required />
          <input type="text" v-model="selectedAgendamento.clientes.cpf" placeholder="CPF" required />
          <input type="date" v-model="selectedAgendamento.data" required />
          <input type="time" v-model="selectedAgendamento.hora" required />
          <input type="text" v-model="selectedAgendamento.clientes.matricula" placeholder="Matrícula do Imóvel" required />
          <button type="submit">Salvar</button>
        </form>
      </template>
    </Modal>

    <!-- Modal de status do atendimento -->
    <Modal :show="showStatusModal" @close="showStatusModal = false">
      <template v-if="selectedAgendamento">
        <h2 class="font-bold text-lg">{{ modalTipo === 'atendido' ? 'Cliente Atendido' : 'Não Compareceu' }}</h2>
        <p class="mb-9">{{ selectedAgendamento.clientes.nome }} - {{ selectedAgendamento.hora }}</p>
        <textarea v-model="observacao" placeholder="Escreva uma observação (opcional)"></textarea>
        <button @click="submitStatus">Enviar</button>
      </template>
    </Modal>
    
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Navbar from '../Components/Navbar.vue';
import Modal from '../Components/Modal.vue';
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

const showDetailsModal  = ref(false);
const showEditModal = ref(false);
const showStatusModal = ref(false);
const selectedAgendamento = ref(null); // Agendamento selecionado para exibir os detalhes
const modalTipo = ref('');
const observacao = ref('');
const message = ref('');

const toggleDropdown = (index) => {
  days.value[index].isOpen = !days.value[index].isOpen;
};

const submitForm = async () => {
  try {
    const formData = {
      id: selectedAgendamento.value.id,
      nome: selectedAgendamento.value.clientes.nome,
      telefone: selectedAgendamento.value.contatos.telefone,
      cpf: selectedAgendamento.value.clientes.cpf,
      data: selectedAgendamento.value.data,
      hora: selectedAgendamento.value.hora,
      matricula: selectedAgendamento.value.clientes.matricula,
    };

    const response = await axios.put(`http://localhost:8000/api/agendar/${selectedAgendamento.value.id}`, formData);

    // Atualiza o agendamento localmente e recarrega os agendamentos
    await buscarAgendamentos();

    // Limpar seleção e fechar modal
    selectedAgendamento.value = null;
    showEditModal.value = false;

  } catch (error) {
    console.error('Erro ao tentar atualizar os dados:', error);
    message.value = 'Erro ao tentar atualizar os dados.';
  }
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

const submitStatus = async () => {
  const status = modalTipo.value === 'atendido' ? 'em_andamento' : 'aguardando_retorno';

  try {
    /*console.log('Dados enviados:', {
    agendamento_id: selectedAgendamento.value.id,
    status: status,
    observacao: observacao.value,
  });*/

    await axios.post('/api/update-appointment-status', {
      agendamento_id: selectedAgendamento.value.id,
      status: status,
      observacao: observacao.value,
    });

    alert('Status atualizado com sucesso!');
    showStatusModal.value = false;

    await buscarAgendamentos();
  } catch (error) {
    console.error('Erro ao atualizar status:', error);
  }
};

const viewDetails = (agendamento) => {
  selectedAgendamento.value = agendamento;
  showDetailsModal.value = true;
};

const editar = (agendamento) => {
  selectedAgendamento.value = { ...agendamento };
  showEditModal.value = true;
};

const openModal = (agendamento, tipo) => {
  selectedAgendamento.value = agendamento;
  modalTipo.value = tipo;
  showStatusModal.value = true;
};

const closeModal = () => {
  showStatusModal.value = false;
  selectedAgendamento.value = null;
  observacao.value = '';
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
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  cursor: pointer;
  margin-left: 10px;
}

.detalhes:hover {
  background-color: #374151;
}

.button-modal  {
  display: flex;
  justify-content: center;
}

.editar:hover {
  background-color: #0d9488;
}

.atendido:hover {
  background-color: #38a169;
}

.nao-compareceu:hover {
 background-color: #c53030;
}

button {
  margin-right: 10px;
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
