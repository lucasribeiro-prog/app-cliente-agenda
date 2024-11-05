<template>
    <div id="agendar">
        <Navbar />

        <div class="container">
            <h1 style="margin-bottom: 20px; margin-top: 10px;"><b>Agendar Atendimento</b></h1>
            <form @submit.prevent="submitForm">
                <input type="text" v-model="nome" placeholder="Nome" required />

                <input
                type="tel"
                v-model="telefone"
                @input="formatTelefone"
                maxlength="15"
                placeholder="Telefone"
                required
                />

                <input
                type="text"
                v-model="cpf"
                @input="formatCPF"
                maxlength="14"
                placeholder="CPF"
                required
                />

                <select v-model="categoria" required>
                    <option value="" disabled>Categoria</option>
                    <option value="1">EDITAL</option>
                    <option value="2">LEILAO</option>
                </select>
                <div v-if="categoria === '2'">
                    <label for="date_leilao"><b>Data do leilão</b></label>
                    <input type="date" v-model="data_leilao" />
                </div>
                <select v-model="atendimento" required>
                    <option value="" disabled>Tipo de Atendimento</option>
                    <option value="1">ONLINE</option>
                    <option value="2">PRESENCIAL</option>
                </select>
                <input type="date" v-model="data" required />
                <input type="time" v-model="hora" required />
                <input type="text" v-model="matricula" placeholder="Matrícula do Imóvel" required />
                <input type="hidden" :value="localIdUsuario" />
                <button type="submit">Agendar</button>
            </form>

            <!-- Modal para exibir feedback do agendamento -->
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
                    {{ isError ? 'Erro ao criar agendamento' : 'Sucesso!' }}
                </h1>
                <p :class="isError ? 'text-center text-red-600' : 'text-center text-gray-600'">{{ message }}</p>
            </Modal>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Navbar from '../Components/Navbar.vue';
import Modal from '../Components/Modal.vue';
import { usePage } from '@inertiajs/vue3';

export default {
    name: "Agendar",
    components: {
        Navbar,
        Modal,
    },
    setup() {
        const { props } = usePage();
        const user = props.auth.user;
        
        const isError = ref(false);
        const showFeedbackModal  = ref(false);
        const localIdUsuario = ref(user.id);
        const nome = ref('');
        const telefone = ref('');
        const cpf = ref('');
        const categoria = ref('');
        const atendimento = ref('');
        const data = ref('');
        const hora = ref('');
        const data_leilao = ref('');
        const matricula = ref('');
        const message = ref('');

        const formatTelefone = () => {
            let num = telefone.value.replace(/\D/g, '').slice(0, 11);
            if (num.length > 10) {
                telefone.value = `(${num.slice(0, 2)}) ${num.slice(2, 7)}-${num.slice(7, 11)}`;
            } else if (num.length > 6) {
                telefone.value = `(${num.slice(0, 2)}) ${num.slice(2, 6)}-${num.slice(6, 10)}`;
            } else if (num.length > 2) {
                telefone.value = `(${num.slice(0, 2)}) ${num.slice(2)}`;
            } else {
                telefone.value = num;
            }
        };

        const formatCPF = () => {
            let num = cpf.value.replace(/\D/g, '').slice(0, 11);
            if (num.length > 9) {
                cpf.value = `${num.slice(0, 3)}.${num.slice(3, 6)}.${num.slice(6, 9)}-${num.slice(9, 11)}`;
            } else if (num.length > 6) {
                cpf.value = `${num.slice(0, 3)}.${num.slice(3, 6)}.${num.slice(6)}`;
            } else if (num.length > 3) {
                cpf.value = `${num.slice(0, 3)}.${num.slice(3)}`;
            } else {
                cpf.value = num;
            }
        };

        const submitForm = async () => {
        try {

            const formData = {
                id_usuario: localIdUsuario.value,
                nome: nome.value,
                telefone: telefone.value,
                cpf: cpf.value,
                categoria: categoria.value,
                atendimento: atendimento.value,
                data: data.value,
                hora: hora.value,
                data_leilao: data_leilao.value,
                matricula: matricula.value,
            };
            
            const response = await axios.post('http://localhost:8000/api/agendar', formData);
            
            message.value = response.data.message;
            showFeedbackModal.value = true;
            isError.value = false;
            nome.value = '';
            telefone.value = '';
            cpf.value = '';
            categoria.value = '';
            atendimento.value = '';
            data.value = '';
            hora.value = '';
            data_leilao.value = '';
            matricula.value = '';
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
            console.error(error);
        }
        
    };

        return {
            localIdUsuario,
            nome,
            telefone,
            cpf,
            categoria,
            atendimento,
            data,
            hora,
            data_leilao,
            matricula,
            message,
            submitForm,
            formatTelefone,
            formatCPF,
            showFeedbackModal,
            isError,
        };
    },


};
</script>

<style scoped>
    .container {
        width: 50%;
    }
</style>