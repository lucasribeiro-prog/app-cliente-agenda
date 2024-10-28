<template>
  <nav class="sidebar">
    <ul>
      <li @click="selectTable('aguardando')" 
      :style="{ 
        backgroundColor: currentTableType === 'aguardando' ? '#d0d4d6' : '',
        color: currentTableType === 'aguardando' ? '#000' : ''
        }">
        Aguardando Retorno
      </li>

      <li  @click="selectTable('andamento')" 
      :style="{ 
        backgroundColor: currentTableType === 'andamento' ? '#d0d4d6' : '',
        color: currentTableType === 'andamento' ? '#000' : '' 
        }">
        Em Andamento
      </li>

      <li  @click="selectTable('remarcar')" 
      :style="{ 
        backgroundColor: currentTableType === 'remarcar' ? '#d0d4d6' : '',
        color: currentTableType === 'remarcar' ? '#000' : '' 
        }">
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
  background-color: rgb(75 85 99);
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
  background-color: #e2e6ea;
  color: #000;
}
</style>
