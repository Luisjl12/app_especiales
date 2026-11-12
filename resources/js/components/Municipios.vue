<template>
  <div class="seccion">
    <div class="encabezado">
      <h2>Municipios y localidades</h2>
      <div class="acciones-top">
        <a href="/admin/municipios/plantilla" class="boton-link">⬇ Plantilla</a>
        <button class="azul" :disabled="importando" @click="archivo.click()">
          {{ importando ? 'Importando...' : '⬆ Importar Excel' }}
        </button>
      </div>
      <input ref="archivo" type="file" accept=".xlsx,.xls,.csv" hidden @change="importar" />
    </div>

    <div v-if="error" class="error">{{ error }}</div>

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

    <input v-model="buscar" class="buscar" placeholder="Buscar municipio o localidad..." />

    <p class="total" v-if="!cargando">
      {{ municipios.length }} municipios · {{ totalLocalidades }} localidades
    </p>

    <p v-if="cargando">Cargando...</p>
    <p v-else-if="!filtrados.length" class="vacio">No hay municipios.</p>

    <table v-else>
      <thead>
        <tr><th>Municipio</th><th>Localidades</th><th></th></tr>
      </thead>
      <tbody>
        <template v-for="m in filtrados" :key="m.id">
          <tr>
            <td><strong>{{ m.nombre }}</strong></td>
            <td>{{ m.localidades_count }}</td>
            <td class="acciones">
              <button v-if="m.localidades_count" class="chico" @click="alternar(m.id)">
                {{ abiertos.has(m.id) ? 'Ocultar' : 'Ver' }}
              </button>
            </td>
          </tr>
          <tr v-if="abiertos.has(m.id)" class="detalle">
            <td colspan="3">
              <span v-for="l in m.localidades" :key="l.id" class="etiqueta">{{ l.nombre }}</span>
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { api, apiArchivo } from '../api';

const municipios = ref([]);
const cargando = ref(true);
const error = ref('');
const buscar = ref('');
const abiertos = ref(new Set());
const archivo = ref(null);
const importando = ref(false);
const resultado = ref(null);

const totalLocalidades = computed(() =>
  municipios.value.reduce((suma, m) => suma + m.localidades_count, 0)
);

// Busca por municipio o por cualquiera de sus localidades
const filtrados = computed(() => {
  const t = buscar.value.toLowerCase();
  if (!t) return municipios.value;
  return municipios.value.filter(m =>
    m.nombre.toLowerCase().includes(t) ||
    m.localidades.some(l => l.nombre.toLowerCase().includes(t))
  );
});

function alternar(id) {
  const s = new Set(abiertos.value);
  s.has(id) ? s.delete(id) : s.add(id);
  abiertos.value = s;
}

function mensajeError(e, porDefecto) {
  if (e.errors) return Object.values(e.errors)[0][0];
  return e.mensaje || e.message || porDefecto;
}

async function cargar() {
  try {
    const datos = await api('/admin/municipios');
    municipios.value = datos.municipios;
  } catch (e) {
    error.value = mensajeError(e, 'No se pudieron cargar los municipios');
  } finally {
    cargando.value = false;
  }
}

onMounted(cargar);

async function importar(ev) {
  const file = ev.target.files[0];
  if (!file) return;

  error.value = '';
  resultado.value = null;
  importando.value = true;

  try {
    const fd = new FormData();
    fd.append('archivo', file);
    resultado.value = await apiArchivo('/admin/municipios/importar', fd);
    await cargar();
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
button { border: 0; border-radius: 6px; background: #42b883; color: #fff; padding: 8px 14px; font-size: 14px; cursor: pointer; }
button:hover { opacity: .9; }
button:disabled { opacity: .6; cursor: not-allowed; }
.gris { background: #999; }
.azul { background: #3498db; }
.chico { padding: 4px 10px; font-size: 12px; }
.acciones-top { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
.boton-link { background: #7f8c8d; color: #fff; padding: 8px 14px; border-radius: 6px; text-decoration: none; font-size: 14px; }
.boton-link:hover { opacity: .9; }
.buscar { width: 100%; box-sizing: border-box; padding: 8px; margin-top: 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
.total { color: #777; font-size: 13px; margin: 8px 0; }
table { width: 100%; border-collapse: collapse; font-size: 14px; }
th, td { padding: 8px; border-bottom: 1px solid #eee; text-align: left; }
.acciones { text-align: right; }
.detalle td { background: #fafafa; }
.etiqueta { display: inline-block; background: #e6f7ef; color: #2d8a5f; padding: 3px 8px; border-radius: 12px; font-size: 12px; margin: 2px 4px 2px 0; }
.vacio { color: #777; text-align: center; }
.resultado { background: #eef6fd; border: 1px solid #cfe3f7; padding: 12px; border-radius: 6px; margin: 10px 0; font-size: 14px; }
.resultado ul { margin: 8px 0; padding-left: 20px; color: #c0392b; max-height: 200px; overflow-y: auto; }
</style>
