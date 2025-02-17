<template>
  <div id="home">
    <Navbar />
    <div class="container">
      <!-- Renderizando Sidebar independentemente do type inicial -->
      <Sidebar 
        :currentTableType="currentTable.type" 
        :headerColors="headerColors" 
        @load-table="loadTable" />
      <div class="content loading-container">
        <div v-if="currentTable">
          <div v-if="isLoading" class="loading-icon">
            <i class="fas fa-spinner fa-spin text-3xl text-blue-500"></i>
          </div>

          <div v-else>
            <h2 class="text-center text-2xl font-bold">{{ currentTable.title }}</h2>
            <table>
              <thead>
                <tr :style="{ background: headerColor }">
                  <th>Cliente</th>
                  <th>Consultor</th>
                  <th>Opções</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in currentTable.data" :key="index">
                  <td>{{ item.client }}</td>
                  <td>{{ item.consultor }}</td>
                  <td>
                    <div class="tooltip">
                      <button class="bg-neutral-600 hover:bg-neutral-900" @click="viewDetails(item.id)">
                        <i class="fas fa-eye"></i>
                      </button>
                      <span class="tooltip-text">Detalhes</span>
                    </div>

                    <div class="tooltip">
                      <button class="bg-teal-600 hover:bg-teal-800" @click="reschedule(item.id)">
                        <i class="fas fa-undo"></i>
                      </button>
                      <span class="tooltip-text">Remarcar</span>
                    </div>  
                    
                    <div class="tooltip">
                      <button v-if="currentTable.type === 'aguardando'" class="bg-green-600 hover:bg-green-800" @click="pagamento(item.id)">
                        <i class="fas fa-check"></i>
                      </button>
                      <span class="tooltip-text">Pago</span>
                    </div>

                    <div class="tooltip">
                      <button v-if="currentTable.type === 'remarcar' || currentTable.type === 'aguardando'" class="bg-red-600 hover:bg-red-800" @click="remover(item.id)">
                        <i class= "fa-solid fa-trash"></i>
                      </button>
                      <span class="tooltip-text">Remover</span>
                    </div>

                    <div class="tooltip">
                      <button v-if="currentTable.type === 'andamento' && item.id_processo == null" class="bg-blue-900 hover:bg-blue-950" @click="processo(item.id)">
                        <i class="fa-solid fa-plus"></i>
                      </button>
                      <span class="tooltip-text">N° do processo</span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>

            <div class="flex justify-center mt-5 pagination-controls">
              <button class="bg-white" @click="prevPage" :disabled="currentPage === 1">
                <i class="text-zinc-500 fa-solid fa-chevron-left"></i>
              </button>
              <span class="font-bold mt-2 mr-2">Página {{ currentPage }} de {{ totalPages }}</span>
              <button class="bg-white" @click="nextPage" :disabled="currentPage === totalPages">
                <i class="text-zinc-500 fa-solid fa-chevron-right"></i>
              </button>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Modal que exibe os detalhes do cliente -->
    <Modal :show="showDetailsModal" @close="showDetailsModal = false">
      <template v-if="selectedAgendamento">
        <h1 class="text-center text-2xl font-bold mb-10">{{ selectedAgendamento.client }}</h1>
        <p v-if="selectedAgendamento.num_process != null"><strong>N° do processo:</strong> {{ selectedAgendamento.num_process }}</p>
        <p><strong>CPF:</strong> {{ formatarCPF(selectedAgendamento.cpf) }}</p>
        <p><strong>Telefone:</strong> {{ formatarTelefone(selectedAgendamento.telefone) }}</p>
        <p><strong>Data do leilão:</strong> {{ formatarData(selectedAgendamento.data_leilao) }}</p>
        <p>
          <strong>Matrícula:</strong>
          <a :href="selectedAgendamento.matricula" class="ml-2" target="_blank">
            <i class="fa-regular fa-file-pdf text-lg"></i>
          </a>
        </p>
        <p v-if="selectedAgendamento.observacao != null"><strong>Observação:</strong> {{ formatarCPF(selectedAgendamento.observacao) }} </p>
      </template>
    </Modal>

    <!-- Modal que exibe o formulario para reagendar o cliente -->
    <Modal :show="showRescheduleModal" @close="showRescheduleModal = false" maxWidth="sm">
      <template v-if="selectedAgendamento">
        <h1 class="text-center text-2xl font-bold mb-10">Remarcar</h1>
        <form @submit.prevent="submitForm">
          <input type="hidden" v-model="selectedAgendamento.id" placeholder="Id" required />
          <input type="date" v-model="selectedAgendamento.data" required />
          <input type="time" v-model="selectedAgendamento.hora" required />
          <div class="flex justify-end">
            <button type="submit">Agendar</button>
          </div>
        </form>
      </template>
    </Modal>

    <!-- Modal de confirmação de remoção -->
    <Modal :show="showDeleteModal" @close="showDeleteModal = false" maxWidth="sm">
      <template v-if="selectedAgendamento">
        <h1 class="text-center text-red-600 text-2xl font-bold mb-4">Atenção!</h1>
        <p class="text-center mb-10 text-lg">Deseja remover o cliente?</p>
        <div class="flex justify-center">
          <button @click="confirmarRemocao" class="delete bg-red-500 text-white py-2 px-4 rounded" type="button">Sim</button>
        </div>
      </template>
    </Modal>

    <!-- Modal de confirmação de pagamento -->
    <Modal :show="showPayModal" @close="showPayModal = false" maxWidth="sm">
      <template v-if="selectedAgendamento">
        <h1 class="text-center text-green-600 text-2xl font-bold mb-4">Atenção!</h1>
        <p class="text-center mb-10 text-lg text-green-600">Confirmar Pagamento?</p>
        <div class="flex justify-center">
          <button @click="confirmarPagamento" class="pagamento bg-green-500 text-white py-2 px-4 rounded" type="button">Sim</button>
        </div>
      </template>
    </Modal>

    <!-- Modal para adicionar o numero do processo -->
    <Modal :show="showModalProcesso" @close="showModalProcesso = false" maxWidth="md">
      <template v-if="selectedAgendamento">
        <h1 class="text-center text-2xl font-bold mb-10">Número do processo</h1>
        <form @submit.prevent="submitProcesso">
          <input type="text" v-model="process" placeholder="Adiconar processo" required />
          <div class="flex justify-end">
            <button type="submit">Adicionar</button>
          </div>
        </form>
      </template>
    </Modal>
  </div>
</template>


<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import Navbar from '../Components/Navbar.vue';
import Sidebar from '../Components/Sidebar.vue';
import Modal from '../Components/Modal.vue';

const props = defineProps({
  auth: Object,
});

// Definindo os estados reativos e computados
const currentTable = ref({
  type: 'aguardando',
  title: "Aguardando retorno",
  data: [],
});
const isLoading = ref(false);
const headerColor = ref('rgb(20 184 166)');
const currentPage = ref(1);
const itemsPerPage = ref(6);
const totalItems = ref(0);
const totalPages = computed(() => Math.ceil(totalItems.value / itemsPerPage.value));
const process = ref('');
const message = ref('');

const headerColors = {
  aguardando: 'linear-gradient(to bottom, rgb(215, 140, 10), rgb(245, 158, 11))',
  andamento: 'linear-gradient(to bottom, rgb(28, 158, 76), rgb(34, 197, 94))',
  remarcar: 'linear-gradient(to bottom, rgb(191, 54, 54), rgb(239, 68, 68))',
};

const showDetailsModal = ref(false);
const showRescheduleModal = ref(false);
const showDeleteModal = ref(false);
const showModalProcesso = ref(false);
const showPayModal = ref(false);
const selectedAgendamento = ref(null);

const statusMap = {
  andamento: 1,
  remarcar: 2,
  aguardando: 3,
};

// Funções para carregar e manipular a tabela
const loadTable = async (type, page = 1) => {
  isLoading.value = true;
  currentTable.value.type = type;
  currentTable.value.title = '';
  currentTable.value.data = [];
  headerColor.value = headerColors[type];
  currentPage.value = page;

  try {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      console.error('Token não encontrado');
      return;
    }
    const response = await axios.get(`${import.meta.env.VITE_API_URL}/agendar?page=${page}`, {
      params: {
        page: currentPage.value,
        limit: itemsPerPage.value,
        status: statusMap[type],
      },
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    const userId = props.auth.user.id;
    const userRole = props.auth.user.role;

    const filteredData = response.data.data.filter(item => {
      if (userRole === 'ADMIN') {
        return item.id_status === statusMap[type];
      } else {
        return item.id_status === statusMap[type] && item.id_usuario === userId;
      }
    });

    totalItems.value = response.data.total;
    currentPage.value = response.data.current_page;

    currentTable.value = {
      ...currentTable.value,
      title: type === 'aguardando' ? "Aguardando Retorno" : type === 'andamento' ? "Em Andamento" : "Remarcar",
      data: filteredData.map(item => ({
        client: item.clientes.nome,
        cpf: item.clientes.cpf,
        telefone: item.contatos.telefone,
        consultor: item.usuarios.name,
        data_leilao: item.data_leilao,
        matricula: item.clientes.matricula,
        observacao: item.observacao,
        id_processo: item.id_processo,
        num_process: item.processos ? item.processos.num_processo : null,
        id: item.id,
      })),
    };
  } catch (error) {
    console.error('Erro ao carregar dados da tabela:', error);
  } finally {
    isLoading.value = false;
  }
};

// Controle de paginação
const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    loadTable(currentTable.value.type, page);
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    goToPage(currentPage.value + 1);
  }
};

const prevPage = () => {
  if (currentPage.value > 1) {
    goToPage(currentPage.value - 1);
  }
};

// Funções para selecionar e manipular agendamentos
const selectAgendamento = (id, modalType) => {
  const item = currentTable.value.data.find(dataItem => dataItem.id === id);
  if (item) {
    selectedAgendamento.value = item;
    if (modalType === 'details') {
      showDetailsModal.value = true;
    } else if (modalType === 'reschedule') {
      showRescheduleModal.value = true;
    } else if (modalType === 'remover') {
      showDeleteModal.value = true;
    } else if (modalType === 'pagamento') {
      showPayModal.value = true;
    } else if (modalType === 'processo') {
      showModalProcesso.value = true;
    }
  }
};

// Métodos para visualização, remoção e manipulação de agendamentos
const viewDetails = (id) => selectAgendamento(id, 'details');
const reschedule = (id) => selectAgendamento(id, 'reschedule');
const remover = (id) => selectAgendamento(id, 'remover');
const pagamento = (id) => selectAgendamento(id, 'pagamento');
const processo = (id) => selectAgendamento(id, 'processo');

const confirmarRemocao = async () => {
  try {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      console.error('Token não encontrado');
      return;
    };

    await axios.delete(`${import.meta.env.VITE_API_URL}/agendar/${selectedAgendamento.value.id}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    showDeleteModal.value = false;
    selectedAgendamento.value = null;
    await loadTable(currentTable.value.type);
  } catch (error) {
    console.error('Erro ao tentar remover o agendamento:', error);
  }
};

const confirmarPagamento = async () => {
  try {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      console.error('Token não encontrado');
      return;
    };

    await axios.post(`${import.meta.env.VITE_API_URL}/agendar`, {
      agendamento_id: selectedAgendamento.value.id,
      status: 1,
    }, 
    {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    showPayModal.value = false;
    selectedAgendamento.value = null;
    await loadTable(currentTable.value.type);
  } catch (error) {
    console.error('Erro ao atualizar status:', error);
  }
};

const submitForm = async () => {
  try {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      console.error('Token não encontrado');
      return;
    };

    const formData = {
      id: selectedAgendamento.value.id,
      data: selectedAgendamento.value.data,
      hora: selectedAgendamento.value.hora,
    };
    await axios.put(`${import.meta.env.VITE_API_URL}/agendar/${selectedAgendamento.value.id}/reschedule`, formData, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    selectedAgendamento.value = null;
    showRescheduleModal.value = false;
    await loadTable(currentTable.value.type);
  } catch (error) {
    console.error('Erro ao tentar atualizar os dados:', error);
  }
};

const submitProcesso = async () => {
  try {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      console.error('Token não encontrado');
      return;
    };

    await axios.post(`${import.meta.env.VITE_API_URL}/agendar`, {
      agendamento_id: selectedAgendamento.value.id,
      process: process.value,
      status: 1,
    },
    {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
    message.value = 'Processo adicionado com sucesso!';

    selectedAgendamento.value.num_process = process.value;
    selectedAgendamento.value.id_status = statusMap.andamento;
    showModalProcesso.value = false;

    await loadTable(currentTable.value.type);
    process.value = "";

  } catch (error) {
    message.value = 'Erro ao inserir número do processo.';
    console.error('Erro ao inserir número do processo:', error);

    console.error('Erro ao tentar atualizar os dados:', error);
    message.value = 'Erro ao tentar inserir um registro.';
  }
};

// Funções de formatação
const formatarData = (data) => new Date(data).toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
const formatarCPF = (cpf) => cpf ? cpf.replace(/(\d{3})(\d)/g, '$1.$2').replace(/(\d{3})(\d{2})$/, '$1-$2') : '';
const formatarTelefone = (telefone) => telefone ? telefone.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3') : '';

onMounted(() => {
  if (props.auth && props.auth.token) {
    const token = props.auth.token;
    localStorage.setItem('auth_token', token);
  } else {
    console.log("Token não encontrado");
  }

  loadTable('aguardando', 1);

  document.title = 'Home - Smart Agenda';
});
</script>

<style scoped>
.container {
  display: flex;
  flex-direction: row;
  height: 600px;
}

.content {
  padding: 20px;
  flex-grow: 1;
}

.delete:hover {
  background-color: rgb(185 28 28);
}

.loading-container {
  position: relative;
  min-height: 100vh;
}

.loading-icon {
  position: absolute;  
  top: 40%;  
  left: 50%; 
  transform: translate(-50%, -50%);
}

button.bg-white:hover {
  background-color: rgb(212 212 216);
}

button {
  margin-left: 1px;
}

thead th {
  color: white;
  padding: 10px;
}
</style>
