<template>
    <div id="agendar">
        <Navbar />

        <div class="container">
            <h1 style="margin-bottom: 20px; margin-top: 10px;"><b>Agendar Atendimento</b></h1>
            <form @submit.prevent="submitForm">
                <input type="text" v-model="nome" placeholder="Nome" required />
                <input type="tel" v-model="telefone" placeholder="Telefone" required />
                <input type="text" v-model="cpf" placeholder="CPF" required />
                <select v-model="categoria" required>
                    <option value="" disabled>Categoria</option>
                    <option value="1">EDITAL</option>
                    <option value="2">LEILAO</option>
                </select>
                <select v-model="atendimento" required>
                    <option value="" disabled>Tipo de Atendimento</option>
                    <option value="1">ONLINE</option>
                    <option value="2">PRESENCIAL</option>
                </select>
                <input type="date" v-model="data" required />
                <input type="time" v-model="hora" required />
                <input type="text" v-model="matricula" placeholder="Matrícula do Imóvel" required />
                <label for="date_leilao"><b>Data do leilão</b></label>
                <input type="date" v-model="data_leilao" required />
                <input type="hidden" :value="localIdUsuario" />
                <button type="submit">Agendar</button>
            </form>
            <p v-if="message">{{ message }}</p>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Navbar from '../Components/Navbar.vue';
import { usePage } from '@inertiajs/vue3';

export default {
    name: "Agendar",
    components: {
        Navbar,
    },
    setup() {
        const { props } = usePage();
        const user = props.auth.user;
        
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
            
            localIdUsuario.value = '';
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
            message.value = 'Erro ao criar agendamento.';
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
        };
    },


};
</script>

<style scoped>
    .container {
        width: 50%;
    }
</style>