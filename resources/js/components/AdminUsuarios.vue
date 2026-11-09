<template>
    <div class="admin">
        <div class="encabezado">
            <h2>Usuarios</h2>
            <button v-if="!formulario" class="nuevo" @click="nuevo">+ Nuevo usuario</button>
        </div>

        <div v-if="error" class="error">{{ error }}</div>
        <div v-if="exito" class="exito">{{ exito }}</div>

        <!-- Formulario para crear o editar -->
        <form v-if="formulario" class="form" @submit.prevent="guardar">
            <h3>{{ form.id ? 'Editar usuario' : 'Nuevo usuario' }}</h3>
            <input v-model="form.name" placeholder="Nombre" required />
            <input v-model="form.email" type="email" placeholder="Correo" required />
            <input v-model="form.password" type="password"
                   :placeholder="form.id ? 'Nueva contraseña (opcional)' : 'Contraseña'"
                   :required="!form.id" />
            <select v-model="form.role_id" :disabled="form.id === yo.id">
                <option v-for="r in roles" :key="r.id" :value="r.id">{{ r.description || r.name }}</option>
            </select>
            <div class="botones">
                <button type="submit" :disabled="guardando">{{ guardando ? 'Guardando...' : 'Guardar' }}</button>
                <button type="button" class="cancelar" @click="cerrar">Cancelar</button>
            </div>
        </form>

        <p v-if="cargando">Cargando...</p>

        <table v-else>
            <thead>
                <tr><th>Nombre</th><th>Correo</th><th>Rol</th><th></th></tr>
            </thead>
            <tbody>
                <tr v-for="u in usuarios" :key="u.id">
                    <td>{{ u.name }}</td>
                    <td>{{ u.email }}</td>
                    <td>{{ u.role?.description || u.role?.name }}</td>
                    <td class="acciones">
                        <button class="chico" @click="editar(u)">Editar</button>
                        <button v-if="u.id !== yo.id" class="chico rojo" @click="eliminar(u)">Eliminar</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { ref, onMounted, computed} from 'vue';
import { api } from '../api';

//
import { sesion } from '../sesion'; 

const yo = computed(()=>sesion.usuario);

const usuarios = ref([]);
const roles = ref([]);
const cargando = ref(true);
const guardando = ref(false);
const error = ref('');
const exito = ref('');
const formulario = ref(false);
const form = ref({});

// Saca el mensaje de error que manda Laravel
function mensajeError(e, porDefecto) {
  if (e.errors) return Object.values(e.errors)[0][0];
  return e.mensaje || e.message || porDefecto;
}

async function cargar() {
  try {
    const datos = await api('/admin/usuarios');
    usuarios.value = datos.usuarios;
    roles.value = datos.roles;
  } catch (e) {
    error.value = mensajeError(e, 'No se pudieron cargar los usuarios');
  } finally {
    cargando.value = false;
  }
}

onMounted(cargar);

function nuevo() {
  form.value = { id: null, name: '', email: '', password: '', role_id: 2 };
  error.value = exito.value = '';
  formulario.value = true;
}

function editar(u) {
  form.value = { id: u.id, name: u.name, email: u.email, password: '', role_id: u.role_id };
  error.value = exito.value = '';
  formulario.value = true;
}

function cerrar() {
  formulario.value = false;
}

async function guardar() {
  error.value = exito.value = '';
  guardando.value = true;
  try {
    if (form.value.id) {
      await api(`/admin/usuarios/${form.value.id}`, 'PUT', form.value);
      exito.value = 'Usuario actualizado';
    } else {
      await api('/admin/usuarios', 'POST', form.value);
      exito.value = 'Usuario creado';
    }
    formulario.value = false;
    await cargar();
  } catch (e) {
    error.value = mensajeError(e, 'No se pudo guardar');
  } finally {
    guardando.value = false;
  }
}

async function eliminar(u) {
  if (!confirm(`¿Eliminar a ${u.name}?`)) return;
  error.value = exito.value = '';
  try {
    await api(`/admin/usuarios/${u.id}`, 'DELETE');
    exito.value = 'Usuario eliminado';
    await cargar();
  } catch (e) {
    error.value = mensajeError(e, 'No se pudo eliminar');
  }
}
</script>

<style scoped>
button { border: 0; border-radius: 6px; background: #42b883; color: #fff; padding: 8px 14px; font-size: 14px; cursor: pointer; }
button:hover { opacity: .9; }
button:disabled { opacity: .6; cursor: not-allowed; }
select { cursor: pointer; }
.admin { background: #fff; padding: 20px; border-radius: 10px; margin: 0; }
.encabezado { display: flex; justify-content: space-between; align-items: center; }
h2 { font-size: 18px; margin: 0; }
h3 { margin-top: 0; font-size: 16px; }
.nuevo { width: auto !important; padding: 6px 12px !important; font-size: 13px !important; }
.form { background: #f8f8f8; padding: 14px; border-radius: 8px; margin: 12px 0; }
.form input, .form select { width: 100%; box-sizing: border-box; padding: 8px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 6px; }
.botones { display: flex; gap: 8px; }
.cancelar { background: #999 !important; }
table { width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 12px; }
th, td { padding: 8px; border-bottom: 1px solid #eee; text-align: left; }
.acciones { white-space: nowrap; }
.chico { width: auto !important; padding: 4px 10px !important; font-size: 12px !important; margin-left: 4px; }
.rojo { background: #e74c3c !important; }
.exito { background: #e6f7ef; color: #2d8a5f; padding: 8px; border-radius: 6px; margin: 10px 0; font-size: 14px; }
</style>
