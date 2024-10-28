<template>
  <div id="home">

    <Navbar />
    <div class="container">
      <Sidebar 
      :currentTableType="currentTable.type" 
      :headerColors="headerColors" 
      @load-table="loadTable" />
      <div class="content">
        <div v-if="currentTable">
          <h2>{{ currentTable.title }}</h2>
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
import { ref } from 'vue';
import Navbar from '../Components/Navbar.vue';
import Sidebar from '../Components/Sidebar.vue';

export default {
  name: "Home",
  components: {
    Navbar,
    Sidebar,
  },
  setup() {
    const currentTable = ref({
      type: 'aguardando',
      title: "Aguardando Retorno",
      data: [
        { client: "Cliente 1", consultor: "Consultor 1", id: 1 },
        { client: "Cliente 2", consultor: "Consultor 2", id: 2 },
      ],
    });
    const headerColor = ref('rgb(20 184 166)');

    const tablesData = {
      aguardando: {
        title: "Aguardando Retorno",
        data: [
          { client: "Cliente 1", consultor: "Consultor 1", id: 1 },
          { client: "Cliente 2", consultor: "Consultor 2", id: 2 },
        ],
      },
      andamento: {
        title: "Em Andamento",
        data: [
          { client: "Cliente 3", consultor: "Consultor 3", id: 3 },
          { client: "Cliente 4", consultor: "Consultor 4", id: 4 },
        ],
      },
      remarcar: {
        title: "Remarcar",
        data: [
          { client: "Cliente 5", consultor: "Consultor 5", id: 5 },
          { client: "Cliente 6", consultor: "Consultor 6", id: 6 },
        ],
      },
    };

    const headerColors = {
      aguardando: 'rgb(20 184 166)',
      andamento: 'rgb(74 222 128)',
      remarcar: 'rgb(239 68 68)',
    };

    const loadTable = (type) => {
      currentTable.value = {
        type: type,
        ...tablesData[type],
      };
      headerColor.value = headerColors[type];
    };

    const viewDetails = (id) => {
      // Navegar para a página de detalhes com o ID
      // Pode usar this.$inertia.visit(`/detalhes/${id}`);
      alert(`Exibir detalhes para o cliente com ID: ${id}`);
    };

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
