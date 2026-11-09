<template>
    <form class="caja" @submit.prevent="entrar">
        <h1>Iniciar sesión</h1>
        <div v-if="error" class="error">{{ error }}</div>
        <input v-model="email" type="email" placeholder="Correo electronico" required/>
        <input v-model="password" type="password" placeholder="Contraseña" required/>

        <label class="recordar">
            <input v-model="recordar" type="checkbox"/>Recordarme
        </label>
        <button :disabled="enviando">
            {{ enviando ? 'Entrando...' : 'Entrar' }}
        </button>
    </form>
</template>

<script setup>
import { ref } from 'vue';
import { api, setToken } from '../api';

const emit = defineEmits(['entro']);

const email = ref('');
const password = ref('');
const recordar = ref(false);
const error = ref('');
const enviando = ref(false);

async function entrar() {
  error.value = '';
  enviando.value = true;
  try {
    const datos = await api('/login', 'POST', {
      email: email.value,
      password: password.value,
      recordar: recordar.value,
    });
    setToken(datos.token);
    emit('entro', datos.usuario);
  } catch (e) {
    error.value = e.mensaje || 'No se pudo iniciar sesión';
  } finally {
    enviando.value = false;
  }
}
</script>

<style scoped>
.recordar { display: block; font-size: 14px; margin-bottom: 14px; color: #555; }
</style>
