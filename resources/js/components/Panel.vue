<template>
  <div class="caja" :style="usuario.es_admin ? 'max-width:640px' : ''">
        <h1>Bienvenido</h1>
        <p>Hola, <strong>{{usuario.name}}</strong></p>
        <p class="correo">{{usuario.email}}</p>
        <p class="rol">{{ usuario.es_admin ? 'Administrador' : 'Usuario' }}</p>
        <AdminUsuarios v-if="usuario.es_admin" :yo="usuario" />
        <button class="salir" @click="salir">Cerrar sesión</button>
    </div>
</template>

<script setup>
import {api, setToken} from '../api'; 
import AdminUsuarios from './AdminUsuarios.vue';

defineProps({usuario: Object});
const emit = defineEmits(['salio']);

async function salir() {
  try {
    const datos = await api('/logout', 'POST');
    setToken(datos.token);
  } finally {
    emit('salio');
  }
}

</script>

<style scoped>
p { text-align:center; }
.correo{ color: #777;font-size:14px; }
.salir{ background: #e74c3c !important; margin-top: 10px; }
</style>
