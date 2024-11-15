<template>
  <div class="users">
    <Navbar />
    <div class="container">
      <h1 class="text-xl font-semibold leading-tight text-gray-800">Administração de Usuários</h1>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Papel</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td>{{ user.id }}</td>
            <td>{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.role }}</td>
            <td>
              <button class="editar bg-teal-500" @click="editar(user)"><i class="fas fa-pencil-alt"></i></button>
              <button class="remover bg-red-600" @click="deleteUser(user.id)"><i class= "fa-solid fa-trash"></i></button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal edição de usuarios -->
  <Modal :show="showEditModal" @close="showEditModal = false">
    <template v-if="selectedUser">
      <h1 class="text-center text-2xl font-bold mb-10">Editar Usuario</h1>
      <form @submit.prevent="editUser">
        <input type="hidden" v-model="selectedUser.id" placeholder="Nome" required />
        <input type="text" v-model="selectedUser.name" placeholder="Nome" required />
        <input type="email" v-model="selectedUser.email" placeholder="Email" required />
        <select v-model="selectedUser.role" required>
          <option value="" disabled selected>Selecione uma permissão</option>
          <option value="1">CONSULTOR</option>
          <option value="2">ADMIN</option>
        </select>
        <button type="submit">Salvar</button>
      </form>
    </template>
  </Modal>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Navbar from '@/Components/Navbar.vue';
import Modal from '../Components/Modal.vue';

const users = ref([]);
const selectedUser = ref(null);
const showEditModal = ref(false);

const editar = (user) => {
  selectedUser.value = { ...user };
  showEditModal.value = true;
};

const fetchUsers = async () => {
  try {
    const token = localStorage.getItem('auth_token');
    
    if (!token) {
      console.error('Token não encontrado');
      return;
    }

    const response = await axios.get('/api/users', {
      headers: {
        Authorization: `Bearer ${token}`,
      }
    });

    users.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar usuários:', error);
  }
};

const deleteUser = async (userId) => {
  try {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      console.error('Token não encontrado');
      return;
    }

    await axios.delete(`/api/users/${userId}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      }
    });

    fetchUsers();
  } catch (error) {
    console.error('Erro ao deletar usuário:', error);
  }
};

const editUser = async () => {
  try {
    const formData = {
      id: selectedUser.id,
      name: selectedUser.value.name,
      email: selectedUser.value.email,
      role: selectedUser.value.role,
    };
    console.log(formData);

    const token = localStorage.getItem('auth_token');
    if (!token) {
      console.error('Token não encontrado');
      return;
    }
    console.log(selectedUser.value)
    await axios.put(`/api/users/${selectedUser.value.id}`, formData, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    fetchUsers();
    showEditModal.value = false;
  } catch (error) {
    console.error('Erro ao atualizar os dados:', error);
  }
};

onMounted(fetchUsers);
</script>

<style scoped>
    thead {
      background: linear-gradient(to left, #000033, #0050a7);
      color: #fff;
    }

    button.editar:hover {
      background-color: #0d9488;
    }

    button.remover:hover {
      background-color: #960000;
    }
</style>
