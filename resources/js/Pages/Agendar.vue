<template>
    <div class="container">
        <Navbar />
        <h1 style="margin-bottom: 20px; margin-top: 10px;"><b>Agendar Atendimento</b></h1>
        <form @submit.prevent="submitForm">
            <input type="text" v-model="name" placeholder="Nome" required />
            <input type="tel" v-model="phone" placeholder="Telefone" required />
            <input type="text" v-model="cpf" placeholder="CPF" required />
            <select v-model="category" required>
                <option value="" disabled>Categoria</option>
                <option value="edital">Edital</option>
                <option value="leilao">Leilão</option>
            </select>
            <select v-model="serviceType" required>
                <option value="" disabled>Tipo de Atendimento</option>
                <option value="videoconferencia">Videoconferência</option>
                <option value="presencial">Presencial</option>
            </select>
            <input type="date" v-model="date" required />
            <input type="text" v-model="propertyRegistration" placeholder="Matrícula do Imóvel" required />
            <button type="submit">Agendar</button>
        </form>
    </div>
</template>

<script>
import Navbar from '../components/Navbar.vue';

export default {
    name: "Agendar",
    components: {
        Navbar,
    },
    data() {
        return {
            name: '',
            phone: '',
            cpf: '',
            category: '',
            serviceType: '',
            date: '',
            propertyRegistration: '',
        };
    },
    methods: {
        submitForm() {
            // Lógica para enviar os dados do formulário
        }
    }
};
</script>

<style>
/* Estilos específicos da página de agendamento (opcional) */
</style>
