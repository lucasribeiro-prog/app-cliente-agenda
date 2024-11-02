<template>
  <nav class="sidebar">
    <ul>
      <li 
        @click="selectTable('aguardando')" 
        :style="{ 
          backgroundColor: currentTableType === 'aguardando' ? '#fff' : '',
          color: currentTableType === 'aguardando' ? '#000' : ''
        }">
        <i v-show="currentTableType === 'aguardando'" class="fa-solid fa-clock mr-3"></i>
        Aguardando Retorno
      </li>

      <li 
        @click="selectTable('andamento')" 
        :style="{ 
          backgroundColor: currentTableType === 'andamento' ? '#fff' : '',
          color: currentTableType === 'andamento' ? '#000' : '' 
        }">
        <i v-show="currentTableType === 'andamento'" class="fa-solid fa-sync-alt mr-3"></i>
        Em Andamento
      </li>

      <li 
        @click="selectTable('remarcar')" 
        :style="{ 
          backgroundColor: currentTableType === 'remarcar' ? '#fff' : '',
          color: currentTableType === 'remarcar' ? '#000' : '' 
        }">
        <i v-show="currentTableType === 'remarcar'" class="fa-solid fa-calendar-alt mr-3"></i>
        Remarcar
      </li>
    </ul>
  </nav>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: "Sidebar",
  props: {
    currentTableType: {
      type: String,
      default: '',
      required: true,
    },
    headerColors: {
      type: Object,
      required: true,
    },
  },
  setup(props, { emit }) {
    const selectTable = (type) => {
      emit('load-table', type);
    };

    return {
      selectTable,
    };
  },
});
</script>

<style scoped>
.sidebar {
  width: 300px;
  background-color: #0050a7;
  padding: 20px;
  border-right: 1px solid #dee2e6;
  color: #fff;
  border-radius: 5px;
}

.sidebar ul {
  list-style-type: none;
  padding: 0;
  display: block;
}

.sidebar li {
  cursor: pointer;
  margin-bottom: 10px;
  padding: 10px;
  transition: background-color 0.3s;
  display: block;
  border-radius: 5px;
}

.sidebar li:hover {
  background-color: #fff;
  color: #000;
}
</style>
