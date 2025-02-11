<template>
  <div class="users">
    <Navbar />
    <div class="container">
      <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold leading-tight text-gray-800">
          Administração de Usuários
        </h1>
        <button @click="adicionarUsuario" class="bg-blue-500 text-white px-4 py-2 mb-2 rounded hover:bg-blue-700">
          <i class="fa-solid fa-user-plus"></i>
        </button>
      </div>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Permissão</th>
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
              <button class="remover bg-red-600" @click="remover(user)"><i class= "fa-solid fa-trash"></i></button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal edição de usuarios -->
  <Modal :show="showEditModal" @close="showEditModal = false" maxWidth="md">
    <template v-if="selectedUser">
      <h1 class="text-center text-2xl font-bold mb-10">Editar Usuario</h1>
      <form @submit.prevent="editUser">
        <input type="hidden" v-model="selectedUser.id" placeholder="Nome" required />
        <input type="text" v-model="selectedUser.name" placeholder="Nome" required />
        <input type="email" v-model="selectedUser.email" placeholder="Email" required />
        <select v-model="roleForSelect" required>
          <option value="" disabled>Selecione uma permissão</option>
          <option value="1">PADRÃO</option>
          <option value="2">ADMIN</option>
        </select>
        <button type="submit">Salvar</button>
      </form>
    </template>
  </Modal>

    <!-- Modal de confirmação de remoção -->
    <Modal :show="showDeleteModal" @close="showDeleteModal = false" maxWidth="sm">
      <template v-if="selectedUser">
        <h1 class="text-center text-red-600 text-2xl font-bold mb-4">Atenção!</h1>
        <p class="text-center mb-10 text-lg text-red-600">Deseja remover o usuario?</p>
        <div class="flex justify-center">
          <button @click="deleteUser" class="delete bg-red-500 text-white py-2 px-4 rounded hover:bg-red-700" type="button">Sim</button>
        </div>
      </template>
    </Modal>

  <!-- Modal de novos usuarios -->
  <Modal :show="showAddUserModal" @close="showAddUserModal = false" maxWidth="md">
    <template v-if="selectedUser">
      <h1 class="text-center text-2xl font-bold mb-10">Novo Usuario</h1>
      <form @submit.prevent="addUser">
        <input type="text" v-model="nome" placeholder="Nome" required />
        <input type="email" v-model="email" placeholder="Email" required />
        <select v-model="role" required>
          <option value="" disabled selected>Selecione uma permissão</option>
          <option value="1">PADRÃO</option>
          <option value="2">ADMIN</option>
        </select>
        <input type="password" v-model="senha" placeholder="Senha" required />
        <input type="password" v-model="confirmSenha" placeholder="Confirmar Senha" required />
        <button type="submit">Adicionar</button>
      </form>
    </template>
  </Modal>

    <!-- Modal para exibir feedback -->
    <Modal :show="showFeedbackModal" @close="showFeedbackModal = false" maxWidth="md">
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
          {{ isError ? message : message }}
      </h1>
    </Modal>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Navbar from '@/Components/Navbar.vue';
import Modal from '../Components/Modal.vue';
import { computed } from "vue";

const users = ref([]);
const selectedUser = ref({
  id: null,
  name: '',
  email: '',
  role: '',
});
const showEditModal = ref(false);
const showAddUserModal = ref(false);
const showFeedbackModal  = ref(false);
const showDeleteModal = ref(false);
const isError = ref(false);
const message = ref('');
const nome = ref('');
const email = ref('');
const senha = ref('');
const confirmSenha = ref('');
const role = ref('');

// Mapeamento para converter os valores
const roleMapping = {
  PADRÃO: "1",
  ADMIN: "2",
};
const reverseRoleMapping = {
  "1": "PADRÃO",
  "2": "ADMIN",
};

// Computed para gerenciar o v-model do select
const roleForSelect = computed({
  get() {
    return roleMapping[selectedUser.value.role] || ""; // Acessa o valor do ref
  },
  set(value) {
    selectedUser.value.role = reverseRoleMapping[value] || ""; // Atualiza o valor do ref
  },
});

const editar = (user) => {
  selectedUser.value = { ...user };
  showEditModal.value = true;
};

const remover = (user) => {
  selectedUser.value = { ...user };
  showDeleteModal.value = true;
};

const adicionarUsuario = () => {
  showAddUserModal.value = true;
}

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

const deleteUser = async () => {
  try {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      console.error('Token não encontrado');
      return;
    }

    await axios.delete(`/api/users/${selectedUser.value.id}`, {
      headers: {
        Authorization: `Bearer ${token}`,
      }
    });

    isError.value = false;
    message.value = 'Usuario excluido com sucesso!';

    fetchUsers();
    showDeleteModal.value = false;
    showFeedbackModal.value = true;
  } catch (error) {

    isError.value = true;
    showFeedbackModal.value = true;
    message.value = 'Erro ao deletar usuário.';
    console.error('Erro ao deletar usuário:', error);
  }
};

const editUser = async () => {
  try {
    const formData = {
      id: selectedUser.id,
      name: selectedUser.value.name,
      email: selectedUser.value.email,
      role: parseInt(roleMapping[selectedUser.value.role] || "0", 10),
    };

    const token = localStorage.getItem('auth_token');
    if (!token) {
      console.error('Token não encontrado');
      return;
    }
    await axios.put(`/api/users/${selectedUser.value.id}`, formData, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    isError.value = false;
    message.value = 'Dados atualizados com sucesso!';

    fetchUsers();
    showEditModal.value = false;
    showFeedbackModal.value = true;
  } catch (error) {

    isError.value = true;
    showFeedbackModal.value = true;
    message.value = 'Erro ao atualizar os dados.';
    console.error('Erro ao atualizar os dados:', error);
  }
};

const addUser = async () => {
  try {
    const formData = {
      name: nome.value,
      email: email.value,
      role: role.value,
      password: senha.value,
      password_confirmation: confirmSenha.value,
    };

    const token = localStorage.getItem('auth_token');
    if (!token) {
      console.error('Token não encontrado');
      return;
    }
    await axios.post(`${import.meta.env.VITE_API_URL}/register`, formData, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    isError.value = false;
    message.value = 'Usuario adicionado com sucesso!';

    fetchUsers();
    showAddUserModal.value = false;
    showFeedbackModal.value = true;

  } catch (error) {
    isError.value = true;
    showFeedbackModal.value = true;
    message.value = 'Erro ao cadastrar o usuario.';
    console.error('Erro ao cadastrar o usuario:', error);
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
