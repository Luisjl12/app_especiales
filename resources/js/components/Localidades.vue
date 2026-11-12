<template>
  <div class="seccion">
    <div class="encabezado">
      <h2>Localidades</h2>
      <div class="acciones-top">
        <a href="/admin/municipios/plantilla" class="boton-link">⬇ Plantilla</a>
        <button class="azul" :disabled="importando" @click="archivo.click()">
          {{ importando ? 'Importando...' : '⬆ Importar Excel' }}
        </button>
        <button :disabled="!municipioId" @click="nueva">+ Nueva localidad</button>
      </div>
      <input ref="archivo" type="file" accept=".xlsx,.xls,.csv" hidden @change="importar" />
    </div>

    <div v-if="error" class="error">{{ error }}</div>
    <div v-if="exito" class="exito">{{ exito }}</div>

    <!-- Resultado de la importación -->
    <div v-if="resultado" class="resultado">
      <strong>Importación terminada:</strong>
      {{ resultado.municipios_nuevos }} municipios nuevos,
      {{ resultado.localidades_nuevas }} localidades nuevas,
      {{ resultado.ya_existian }} ya existían,
      {{ resultado.errores.length }} con error.
      <ul v-if="resultado.errores.length">
        <li v-for="e in resultado.errores" :key="e.fila">Fila {{ e.fila }}: {{ e.mensaje }}</li>
      </ul>
      <button class="chico gris" @click="resultado = null">Cerrar</button>
    </div>

    <!-- Selector de municipio -->
    <div class="filtros">
      <select v-model="municipioId" @change="cargarLocalidades">
        <option :value="null">-- Elige un municipio --</option>
        <option v-for="m in municipios" :key="m.id" :value="m.id">
          {{ m.nombre }} ({{ m.localidades_count }})
        </option>
      </select>
      <input v-if="municipioId" v-model="buscar" placeholder="Buscar localidad..." />
    </div>

    <!-- Formulario (nueva o editar) -->
    <form v-if="formulario" class="form" @submit.prevent="guardar">
      <h3>{{ form.id ? 'Editar localidad' : 'Nueva localidad' }}</h3>
      <div class="fila-form">
        <select v-model="form.municipio_id" required>
          <option v-for="m in municipios" :key="m.id" :value="m.id">{{ m.nombre }}</option>
        </select>
        <input v-model="form.nombre" placeholder="Nombre de la localidad" maxlength="255" required />
        <button type="submit" :disabled="guardando">{{ guardando ? 'Guardando...' : 'Guardar' }}</button>
        <button type="button" class="gris" @click="formulario = false">Cancelar</button>
      </div>
    </form>

    <p v-if="!municipioId" class="vacio">Elige un municipio para ver sus localidades.</p>
    <p v-else-if="cargando">Cargando...</p>
    <p v-else-if="!filtradas.length" class="vacio">Este municipio no tiene localidades.</p>

    <table v-else>
      <thead>
        <tr><th>Localidad</th><th>Planteles</th><th></th></tr>
      </thead>
      <tbody>
        <tr v-for="l in filtradas" :key="l.id">
          <td>{{ l.nombre }}</td>
          <td>{{ l.planteles_count }}</td>
          <td class="acciones">
            <button class="chico" @click="editar(l)">Editar</button>
            <button class="chico rojo" @click="eliminar(l)">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { api, apiArchivo } from '../api';

const municipios = ref([]);
const municipioId = ref(null);
const localidades = ref([]);
const cargando = ref(false);
const guardando = ref(false);
const error = ref('');
const exito = ref('');
const buscar = ref('');
const formulario = ref(false);
const form = ref({});
const archivo = ref(null);
const importando = ref(false);
const resultado = ref(null);

const filtradas = computed(() => {
  const t = buscar.value.toLowerCase();
  return localidades.value.filter(l => l.nombre.toLowerCase().includes(t));
});

function mensajeError(e, porDefecto) {
  if (e.errors) return Object.values(e.errors)[0][0];
  return e.mensaje || e.message || porDefecto;
}

async function cargarMunicipios() {
  try {
    const datos = await api('/admin/localidades/municipios');
    municipios.value = datos.municipios;
  } catch (e) {
    error.value = mensajeError(e, 'No se pudieron cargar los municipios');
  }
}

async function cargarLocalidades() {
  localidades.value = [];
  buscar.value = '';
  formulario.value = false;
  if (!municipioId.value) return;

  cargando.value = true;
  try {
    const datos = await api(`/admin/localidades?municipio_id=${municipioId.value}`);
    localidades.value = datos.localidades;
  } catch (e) {
    error.value = mensajeError(e, 'No se pudieron cargar las localidades');
  } finally {
    cargando.value = false;
  }
}

onMounted(cargarMunicipios);

function nueva() {
  form.value = { id: null, municipio_id: municipioId.value, nombre: '' };
  error.value = exito.value = '';
  formulario.value = true;
}

function editar(l) {
  form.value = { id: l.id, municipio_id: l.municipio_id, nombre: l.nombre };
  error.value = exito.value = '';
  formulario.value = true;
}

async function guardar() {
  error.value = exito.value = '';
  guardando.value = true;
  try {
    if (form.value.id) {
      await api(`/admin/localidades/${form.value.id}`, 'PUT', form.value);
      exito.value = 'Localidad actualizada';
    } else {
      await api('/admin/localidades', 'POST', form.value);
      exito.value = 'Localidad creada';
    }
    formulario.value = false;
    await cargarMunicipios(); // actualiza los conteos del selector
    await cargarLocalidades();
  } catch (e) {
    error.value = mensajeError(e, 'No se pudo guardar');
  } finally {
    guardando.value = false;
  }
}

async function eliminar(l) {
  if (!confirm(`¿Eliminar la localidad ${l.nombre}?`)) return;
  error.value = exito.value = '';
  try {
    await api(`/admin/localidades/${l.id}`, 'DELETE');
    exito.value = 'Localidad eliminada';
    await cargarMunicipios();
    await cargarLocalidades();
  } catch (e) {
    error.value = mensajeError(e, 'No se pudo eliminar');
  }
}

async function importar(ev) {
  const file = ev.target.files[0];
  if (!file) return;

  error.value = exito.value = '';
  resultado.value = null;
  importando.value = true;

  try {
    const fd = new FormData();
    fd.append('archivo', file);
    resultado.value = await apiArchivo('/admin/municipios/importar', fd);
    await cargarMunicipios();
    await cargarLocalidades();
  } catch (e) {
    error.value = mensajeError(e, 'No se pudo importar el archivo');
  } finally {
    importando.value = false;
    ev.target.value = '';
  }
}
</script>

<style scoped>
.seccion { background: #fff; padding: 20px; border-radius: 10px; }
.encabezado { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
h2 { font-size: 18px; margin: 0; }
h3 { margin: 0 0 10px; font-size: 16px; }
button { border: 0; border-radius: 6px; background: #42b883; color: #fff; padding: 8px 14px; font-size: 14px; cursor: pointer; }
button:hover { opacity: .9; }
button:disabled { opacity: .6; cursor: not-allowed; }
.gris { background: #999; }
.rojo { background: #e74c3c; }
.azul { background: #3498db; }
.chico { padding: 4px 10px; font-size: 12px; margin-left: 4px; }
.acciones-top { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
.boton-link { background: #7f8c8d; color: #fff; padding: 8px 14px; border-radius: 6px; text-decoration: none; font-size: 14px; }
.boton-link:hover { opacity: .9; }
.filtros { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 10px; margin: 14px 0; }
.filtros select, .filtros input, .fila-form select, .fila-form input {
  width: 100%; box-sizing: border-box; padding: 8px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;
}
.form { background: #f8f8f8; padding: 14px; border-radius: 8px; margin-bottom: 14px; }
.fila-form { display: grid; grid-template-columns: 1fr 2fr auto auto; gap: 8px; align-items: center; }
@media (max-width: 700px) { .fila-form { grid-template-columns: 1fr; } }
table { width: 100%; border-collapse: collapse; font-size: 14px; }
th, td { padding: 8px; border-bottom: 1px solid #eee; text-align: left; }
.acciones { white-space: nowrap; text-align: right; }
.vacio { color: #777; text-align: center; }
.exito { background: #e6f7ef; color: #2d8a5f; padding: 8px; border-radius: 6px; margin: 10px 0; font-size: 14px; }
.resultado { background: #eef6fd; border: 1px solid #cfe3f7; padding: 12px; border-radius: 6px; margin: 10px 0; font-size: 14px; }
.resultado ul { margin: 8px 0; padding-left: 20px; color: #c0392b; max-height: 200px; overflow-y: auto; }
</style>