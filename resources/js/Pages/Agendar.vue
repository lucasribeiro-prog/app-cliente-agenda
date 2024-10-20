<template>
    <div class="container">
        <Navbar />
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
            <input type="hidden" :value="id_usuario" />
            <button type="submit">Agendar</button>
        </form>
    </div>
</template>

<script>
import Navbar from '../components/Navbar.vue';
import axios from 'axios';

export default {
    props: {
        id_usuario: {
            type: Number,
            required: true,
        },
    },
    name: "Agendar",
    components: {
        Navbar,
    },
    data() {
        return {
            localIdUsuario: this.id_usuario,
            nome: '',
            telefone: '',
            cpf: '',
            categoria: '',
            atendimento: '',
            data: '',
            hora: '',
            data_leilao: '',
            matricula: '',
        };
    },
    methods: {
    async submitForm() {
        try {
            // Crie um objeto com os dados do formulário diretamente
            const formData = {
                id_usuario: this.localIdUsuario,
                nome: this.nome,
                telefone: this.telefone,
                cpf: this.cpf,
                categoria: this.categoria,
                atendimento: this.atendimento,
                data: this.data,
                hora: this.hora,
                data_leilao: this.data_leilao,
                matricula: this.matricula,
            };
            
            // Envie os dados do formulário via requisição POST
            const response = await axios.post('http://localhost:8000/api/agendar', formData);
            
            // Exibe uma mensagem de sucesso ou faça algo com a resposta
            this.message = response.data.message;
            
            // Limpe os campos do formulário após o envio
            this.localIdUsuario = '';
            this.nome = '';
            this.telefone = '';
            this.cpf = '';
            this.categoria = '';
            this.atendimento = '';
            this.data = '';
            this.hora = '';
            this.data_leilao = '';
            this.matricula = '';
        } catch (error) {
            // Exibe mensagem de erro
            this.message = 'Erro ao criar agendamento.';
            console.error(error);
        }
    },
}

};
</script>

<style>
/* Estilos específicos da página de agendamento (opcional) */
</style>
