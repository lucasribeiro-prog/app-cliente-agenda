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
                  <button @click="viewDetails(item.id)">Detalhes</button>
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
import { onMounted } from 'vue';
import { ref } from 'vue';
import Navbar from '../Components/Navbar.vue';
import Sidebar from '../Components/Sidebar.vue';
import axios from 'axios';

export default {
  name: "Home",
  components: {
    Navbar,
    Sidebar,
  },
  setup() {
    const currentTable = ref({
      type: 'aguardando',
      title: "Aguardando retorno",
      data: [],
    });
    const headerColor = ref('rgb(20 184 166)');

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
            consultor: item.usuarios.name,
            id: item.id
          })),
        };
      } catch (error) {
        console.error('Erro ao carregar dados da tabela:', error);
      }
    };

    const viewDetails = (id) => {
      // Navegar para a página de detalhes com o ID
      // Pode usar this.$inertia.visit(`/detalhes/${id}`);
      alert(`Exibir detalhes para o cliente com ID: ${id}`);
    };

    onMounted(() => {
      loadTable('aguardando'); // Define 'aguardando' como o tipo inicial
    });

    return {
      currentTable,
      loadTable,
      viewDetails,
      headerColor,
      headerColors,
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
