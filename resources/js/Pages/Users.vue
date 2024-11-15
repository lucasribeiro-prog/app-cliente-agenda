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
              <button class="editar bg-teal-500" @click="editUser(user)"><i class="fas fa-pencil-alt"></i></button>
              <button class="remover bg-red-600" @click="deleteUser(user.id)"><i class= "fa-solid fa-trash"></i></button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Navbar from '@/Components/Navbar.vue';

const users = ref([]);

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

const editUser = (user) => {
  console.log('Editar usuário:', user);
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
