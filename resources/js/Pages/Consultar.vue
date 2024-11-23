<template>
  <div id="consultar">
    <!-- Navbar -->
    <Navbar />

    <div class="container">
      <h1 class="text-xl font-semibold leading-tight text-gray-800">Consultar Agendamentos</h1>

      <!-- Dropdown para cada dia da semana -->
      <div v-for="(day, index) in days" :key="index" class="day-section">
        <div @click="toggleDropdown(index)" class="dropdown-toggle">
          <span class="text-bold">{{ day.name }}</span>
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
                <td>{{ formatarHora(agendamento.hora) }}</td>
                <td>{{ agendamento.clientes.nome }}</td>
                <td>{{ agendamento.usuarios.name }}</td>
                <td>
                  <div class="tooltip">
                    <button class="detalhes bg-gray-600" @click="viewDetails(agendamento)">
                      <i class="fas fa-eye"></i>
                    </button>
                    <span class="tooltip-text">Detalhes</span>
                  </div>

                  <div class="tooltip">
                    <button 
                    class="editar bg-teal-500" 
                    @click="editar(agendamento)"
                    :disabled="user.id !== agendamento.usuarios.id && user.role !== 'ADMIN'"
                    :class="{ 'disabled-button': user.id !== agendamento.usuarios.id && user.role !== 'ADMIN'}">
                      <i class="fas fa-pencil-alt"></i>
                    </button>
                    <span class="tooltip-text">Editar</span>
                  </div>

                  <div class="tooltip">
                    <button v-if="user.role === 'ADMIN'" class="atendido bg-green-500" @click="openModal(agendamento, 'atendido')">
                      <i class="fas fa-check"></i>
                    </button>
                    <span class="tooltip-text">Antendido</span>
                  </div>

                  <div class="tooltip">
                    <button v-if="user.role === 'ADMIN'" class="nao-compareceu bg-red-500" @click="openModal(agendamento, 'nao_compareceu')">
                      <i class="fas fa-times"></i>
                    </button>
                    <span class="tooltip-text">Não Compareceu</span>
                  </div>
                  
                  <div class="tooltip">
                    <button v-if="user.role === 'ADMIN' && agendamento.id_link == null" class="adicionar_link bg-cyan-700" @click="adicionarLink(agendamento)">
                      <i class="fa-solid fa-link-slash"></i>
                    </button>
                    <span class="tooltip-text">Adiconar Link</span>
                  </div>
                  

                  <div class="tooltip">
                    <button v-if="agendamento.id_link !== null" class="copiar_link bg-cyan-700" @click="copiarLink(agendamento.links.link_reuniao)">
                      <i class="fa-solid fa-link"></i>
                    </button>
                    <span class="tooltip-text">Copiar Link</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal para exibir detalhes do agendamento -->
    <Modal :show="showDetailsModal" @close="showDetailsModal = false" maxWidth="md">
      <template v-if="selectedAgendamento">
        <h1 class="text-center text-2xl font-bold mb-10">{{ selectedAgendamento.clientes.nome }}</h1>
        <p><strong>CPF:</strong> {{ formatarCPF(selectedAgendamento.clientes.cpf) }}</p>
        <p><strong>Telefone:</strong> {{ formatarTelefone(selectedAgendamento.contatos.telefone) }}</p>
        <p><strong>Data do leilão:</strong> {{ formatarData(selectedAgendamento.data_leilao) }}</p>
        <p>
          <strong>Matrícula:</strong>
          <a :href="selectedAgendamento.clientes.matricula" class="ml-2" target="_blank">
            <i class="fa-regular fa-file-pdf text-lg"></i>
          </a>
        </p>
      </template>
    </Modal>

    <!-- Modal edição do agendamento -->
    <Modal :show="showEditModal" @close="showEditModal = false" maxWidth="md">
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
          <div class="flex justify-end">
            <button type="submit">Salvar</button>
          </div>
        </form>
      </template>
    </Modal>

    <!-- Modal para adicionar o link da reunião -->
    <Modal :show="showLinkModal" @close="showLinkModal = false" maxWidth="md">
      <template v-if="selectedAgendamento">
        <h1 class="text-center text-2xl font-bold mb-10">Link</h1>
        <form @submit.prevent="submitLink">
          <input type="text" v-model="link" placeholder="Adiconar link para a reunião" required />
          <button type="submit">Salvar</button>
        </form>
      </template>
    </Modal>

    <!-- Modal para exibir feedback de atualização -->
    <Modal :show="showFeedbackModal" @close="showFeedbackModal = false">
      <!-- Ícone de Sucesso -->
      <div class="mb-4">
          <svg v-if="isError" class="w-12 h-12 text-red-500 mx-auto" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zM13 7h-2v6h2V7zm0 8h-2v2h2v-2z"/>
          </svg>
          <svg v-else class="w-12 h-12 text-green-500 mx-auto" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
              <path d="M12 0C5.372 0 0 5.373 0 12s5.372 12 12 12 12-5.373 12-12S18.628 0 12 0zm-1.533 17.025l-4.51-4.509 1.412-1.412 3.098 3.1 7.379-7.379 1.412 1.412-8.791 8.788z"/>
          </svg>
      </div>
      <h1 :class="isError ? 'text-red-700' : 'text-green-700'" class="text-center text-2xl font-bold mb-2">
          {{ isError ? 'Erro ao atualizar os dados' : 'Dados atualizados com sucesso!' }}
      </h1>
    </Modal>

    <!-- Modal de status do atendimento -->
    <Modal :show="showStatusModal" @close="showStatusModal = false" maxWidth="md">
      <template v-if="selectedAgendamento">
        <h2 class="font-bold text-lg">{{ modalTipo === 'atendido' ? 'Cliente Atendido' : 'Não Compareceu' }}</h2>
        <p class="mb-9">{{ selectedAgendamento.clientes.nome }} - {{ formatarHora(selectedAgendamento.hora) }}</p>
        <textarea v-model="observacao" placeholder="Escreva uma observação (opcional)"></textarea>
        <div class="flex justify-end">
          <button :class="modalTipo === 'atendido' ? 'bg-green-500 hover:bg-green-700' : 'bg-red-500 hover:bg-red-700'"  @click="submitStatus">Enviar</button>
        </div>
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
]);

const showFeedbackModal  = ref(false);
const showLinkModal = ref(false);
const showDetailsModal  = ref(false);
const showEditModal = ref(false);
const showStatusModal = ref(false);
const isError = ref(false);
const selectedAgendamento = ref(null);
const modalTipo = ref('');
const observacao = ref('');
const message = ref('');
const link = ref('');

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
    message.value = response.data.message;

    await buscarAgendamentos();

    selectedAgendamento.value = null;
    showEditModal.value = false;
    showFeedbackModal.value = true;

  } catch (error) {
    if (error.response && error.response.status === 422) {
      message.value = error.response.data.message || 'Erro ao processar o agendamento.';
      
      const errorDetails = error.response.data.errors;
      if (errorDetails && errorDetails.data_leilao) {
        message.value += ` ${errorDetails.data_leilao[0]}`;
      }
    } else {
      message.value = 'Ocorreu um erro inesperado. Tente novamente mais tarde.';
    }

    isError.value = true;
    showFeedbackModal.value = true;
    console.error('Erro ao tentar atualizar os dados:', error);
    message.value = 'Erro ao tentar atualizar os dados.';
  }
};

const submitLink = async () => {
  try {

    const response = await axios.post(`http://localhost:8000/api/agendar`, {
      agendamento_id: selectedAgendamento.value.id,
      link: link.value,
    });
    message.value = response.data.message;

    await buscarAgendamentos();

    selectedAgendamento.value = null;
    link.value = "";
    showLinkModal.value = false;

  } catch (error) {
    if (error.response && error.response.status === 422) {
      message.value = error.response.data.message || 'Erro inserir o link da reuniao.';
    } else {
      message.value = 'Ocorreu um erro inesperado. Tente novamente mais tarde.';
    }

    isError.value = true;
    showFeedbackModal.value = true;
    console.error('Erro ao tentar atualizar os dados:', error);
    message.value = 'Erro ao tentar inserir um registro.';
  }
};
       

const buscarAgendamentos = async () => {
  try {
    const response = await axios.get('http://localhost:8000/api/consultar');
    const agendamentos = response.data;

    days.value.forEach(day => {
      day.agendamentos = [];
    });

    // Agrupa os agendamentos pelos dias da semana
    agendamentos.forEach(agendamento => {
      const date = new Date(agendamento.data);
      const weekDayIndex = date.getDay();
      if(agendamento.id_status == null) {
        days.value[weekDayIndex].agendamentos.push(agendamento);
      }
    });

    //Ordena a agenda com base no horário
    days.value.forEach(day => {
      day.agendamentos.sort((a, b) => {
        const horaA = a.hora;
        const horaB = b.hora;
        return horaA.localeCompare(horaB);
      });
    });
  } catch (error) {
    console.error('Erro ao carregar agendamentos:', error);
  }
};

const submitStatus = async () => {
  const status = modalTipo.value === 'atendido' ? 3 : 2;

  try {
    await axios.post('http://localhost:8000/api/agendar', {
      agendamento_id: selectedAgendamento.value.id,
      status: status,
      observacao: observacao.value,
    });

    showStatusModal.value = false;

    await buscarAgendamentos();
  } catch (error) {
    console.error('Erro ao atualizar status:', error);
  }
};

const copiarLink = (idLink) => {
  const url = idLink;

  navigator.clipboard.writeText(url)
    .then(() => {
      alert("Link copiado para a área de transferência!");
    })
    .catch((err) => {
      console.error("Erro ao copiar o link:", err);
    });
};

const viewDetails = (agendamento) => {
  selectedAgendamento.value = agendamento;
  showDetailsModal.value = true;
};

const editar = (agendamento) => {
  selectedAgendamento.value = { ...agendamento };
  selectedAgendamento.value.contatos.telefone = formatarTelefone(selectedAgendamento.value.contatos.telefone);
  selectedAgendamento.value.clientes.cpf = formatarCPF(selectedAgendamento.value.clientes.cpf);
  showEditModal.value = true;
};

const adicionarLink = (agendamento) => {
  selectedAgendamento.value = { ...agendamento };
  showLinkModal.value = true;
};

const openModal = (agendamento, tipo) => {
  selectedAgendamento.value = agendamento;
  modalTipo.value = tipo;
  showStatusModal.value = true;
};

const formatarData = (data) => {
  const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
  return new Date(data).toLocaleDateString('pt-BR', options);
};

const formatarHora = (hora) => {
  const [hours, minutes] = hora.split(':');
  return `${hours}:${minutes}`;
};

const formatarCPF = (cpf) => {
  if (!cpf) return '';
  return cpf.replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d{2})$/, '$1-$2');
};

const formatarTelefone = (telefone) => {
  if (!telefone) return '';
  return telefone.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
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
  background: linear-gradient(to left, #000033, #0050a7);
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

.disabled-button {
  opacity: 0.5;
  background-color: #c2c2c2;
  cursor: not-allowed;
}

button.disabled-button:hover {
  background-color: #c2c2c2;
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

.adicionar_link:hover, .copiar_link:hover {
  background-color: rgb(22 78 99);
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