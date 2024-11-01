<template>
  <div id="home">
    <Navbar />
    <div class="container">
      <!-- Renderizando Sidebar independentemente do type inicial -->
      <Sidebar 
        :currentTableType="currentTable.type" 
        :headerColors="headerColors" 
        @load-table="loadTable" />
      <div class="content">
        <div v-if="currentTable">
          <h2 class="text-center text-2xl font-bold">{{ currentTable.title }}</h2>
          <table>
            <thead>
              <tr :style="{ backgroundColor: headerColor }">
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
                  <button class="detalhes bg-gray-600" @click="viewDetails(item.id)" title="Detalhes">
                    <i class="fas fa-eye"></i>
                  </button>

                  <button v-if="currentTable.type === 'remarcar'" class="detalhes bg-gray-600" @click="reschedule(item.id)" title="Remarcar">
                    <i class="fas fa-undo"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal que exibe os detalhes do cliente -->
    <Modal :show="showDetailsModal" @close="showDetailsModal = false">
      <template v-if="selectedAgendamento">
        <h1 class="text-center text-2xl font-bold mb-10">{{ selectedAgendamento.client }}</h1>
        <p><strong>CPF:</strong> {{ selectedAgendamento.cpf }}</p>
        <p><strong>Telefone:</strong> {{ selectedAgendamento.telefone }}</p>
        <p><strong>Data do leilão:</strong> {{ selectedAgendamento.data_leilao }}</p>
        <p>
          <strong>Matrícula:</strong>
          <a :href="selectedAgendamento.matricula" class="ml-2" target="_blank">
            <i class="fa-regular fa-file-pdf text-lg"></i>
          </a>
        </p>
      </template>
    </Modal>

    <!-- Modal que exibe o formulario para reagendar o cliente -->
    <Modal :show="showRescheduleModal" @close="showRescheduleModal = false">
      <template v-if="selectedAgendamento">
        <h1 class="text-center text-2xl font-bold mb-10">Remarcar</h1>
        <form @submit.prevent="submitForm">
          <input type="hidden" v-model="selectedAgendamento.id" placeholder="Id" required />
          <input type="date" v-model="selectedAgendamento.data" required />
          <input type="time" v-model="selectedAgendamento.hora" required />
          <button type="submit">Agendar</button>
        </form>
      </template>
    </Modal>
  </div>
</template>


<script>
import { onMounted } from 'vue';
import { ref } from 'vue';
import Navbar from '../Components/Navbar.vue';
import Sidebar from '../Components/Sidebar.vue';
import axios from 'axios';
import Modal from '../Components/Modal.vue';

export default {
  name: "Home",
  components: {
    Navbar,
    Sidebar,
    Modal,
  },
  setup() {
    const currentTable = ref({
      type: 'aguardando',
      title: "Aguardando retorno",
      data: [],
    });
    const headerColor = ref('rgb(20 184 166)');
    const showDetailsModal  = ref(false);
    const showRescheduleModal  = ref(false);
    const selectedAgendamento = ref(null);
    const headerColors = {
      aguardando: 'rgb(20 184 166)',
      andamento: 'rgb(74 222 128)',
      remarcar: 'rgb(239 68 68)',
    };

    const statusMap = {
      andamento: 1,
      remarcar: 2,
      aguardando: 3,
    };

    const loadTable = async (type) => {
      currentTable.value.type = type;
      currentTable.value.title = '';
      currentTable.value.data = [];
      headerColor.value = headerColors[type];

      try {
        const response = await axios.get(`http://localhost:8000/api/agendar`);
        const filteredData = response.data.filter(item => item.id_status === statusMap[type]);
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
            id: item.id
          })),
        };
      } catch (error) {
        console.error('Erro ao carregar dados da tabela:', error);
      }
    };

    const selectAgendamento = (id, modalType) => {
      const item = currentTable.value.data.find(dataItem => dataItem.id === id);
      if (item) {
        selectedAgendamento.value = item;
        if (modalType === 'details') {
          showDetailsModal.value = true;
        } else if (modalType === 'reschedule') {
          showRescheduleModal.value = true;
        }
      }
    };

    const viewDetails = (id) => {
      selectAgendamento(id, 'details');
    };

    const reschedule = (id) => {
      selectAgendamento(id, 'reschedule');
    };

    const submitForm = async () => {
      try {
        const formData = {
          id: selectedAgendamento.value.id,
          data: selectedAgendamento.value.data,
          hora: selectedAgendamento.value.hora,
        };

        const response = await axios.put(`http://localhost:8000/api/agendar/${selectedAgendamento.value.id}/reschedule`, formData);

        selectedAgendamento.value = null;
        showRescheduleModal.value = false;

        await loadTable('remarcar');

      } catch (error) {
        console.error('Erro ao tentar atualizar os dados:', error);
        message.value = 'Erro ao tentar atualizar os dados.';
      }
    };

    onMounted(() => {
      loadTable('aguardando');
    });

    return {
      currentTable,
      loadTable,
      viewDetails,
      reschedule,
      submitForm,
      headerColor,
      headerColors,
      selectedAgendamento,
      showDetailsModal,
      showRescheduleModal,
    };
  },
};
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

thead th {
  color: white;
  padding: 10px;
}
</style>
