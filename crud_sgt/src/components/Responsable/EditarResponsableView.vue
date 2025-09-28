<template>
  <div class="container">
    <div class="card">
      <div class="card-header">Editar responsable</div>
      <div class="card-body">
        <form @submit.prevent="actualizarRegistro">
          <!-- Mostrar Codigo_Resp y ID_Resp como lectura, no editables -->
          <div class="row mb-1">
            <label>Codigo (no editable)</label>
            <input type="text" class="form-control" :value="codigoResp" disabled>
          </div>
          <div class="row mb-1">
            <label>ID (no editable)</label>
            <input type="text" class="form-control" :value="paciente.ID_Resp" disabled>
          </div>

          <!-- Campos editables -->
          <div class="row mb-1">
            <label for="Cedula">Cédula</label>
            <input id="Cedula" v-model="paciente.Cedula" class="form-control" required>
          </div>

          <div class="row mb-1">
            <label for="Nombre">Nombre</label>
            <input id="Nombre" v-model="paciente.Nombre" class="form-control" required>
          </div>

          <div class="row mb-1">
            <label for="Apellido">Apellido</label>
            <input id="Apellido" v-model="paciente.Apellido" class="form-control" required>
          </div>

          <div class="row mb-1">
            <label for="Cargo">Cargo</label>
            <input id="Cargo" v-model="paciente.Cargo" class="form-control">
            <small class="form-text text-muted">Si cambia el cargo, el Código primario puede modificarse automáticamente.</small>
          </div>

          <div class="row mb-1">
            <label for="Telefono">Teléfono</label>
            <input id="Telefono" v-model="paciente.Telefono" class="form-control">
          </div>

          <div class="btn-group" role="group">
            <button type="submit" class="btn btn-success">Guardar</button>
            <router-link :to="{ name: 'ResponsableView' }" class="btn btn-warning">Cancelar</router-link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['Codigo_Resp'], // si usas props: true en la ruta
  data() {
    return {
      paciente: {
        ID_Resp: '',
        Cedula: '',
        Nombre: '',
        Apellido: '',
        Cargo: '',
        Telefono: ''
      }
    }
  },
  computed: {
    codigoResp() {
      // Si la ruta usa param o prop, usa el prop; fallback a $route
      return this.Codigo_Resp || this.$route.params.Codigo_Resp || '';
    }
  },
  created() {
    this.obtenerPacienteID();
  },
  methods: {
    obtenerPacienteID() {
      const codigo = encodeURIComponent(this.codigoResp);
      fetch(`http://localhost/practica1_sgt/apis/responsable.php?consultar=${codigo}`)
        .then(res => res.json())
        .then(data => {
          if (!data || data.success === 0) {
            alert('Responsable no encontrado');
            this.$router.push({ name: 'ResponsableView' });
            return;
          }
          // aquí la API devuelve un objeto asociativo (no array)
          // asignamos directamente
          this.paciente = data;
        })
        .catch(err => {
          console.error(err);
          alert('Error al obtener datos del responsable');
        });
    },

    actualizarRegistro() {
      const codigoOld = this.codigoResp;
      const datosEnviar = {
        // No es necesario enviar Codigo_Resp en body: la API usa ?actualizar=codigoOld
        Cedula: this.paciente.Cedula,
        Nombre: this.paciente.Nombre,
        Apellido: this.paciente.Apellido,
        Cargo: this.paciente.Cargo,
        Telefono: this.paciente.Telefono
      };

      fetch(`http://localhost/practica1_sgt/apis/responsable.php?actualizar=${encodeURIComponent(codigoOld)}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datosEnviar)
      })
        .then(res => res.json())
        .then(resp => {
          if (resp.success) {
            // la API puede devolver "Codigo_Resp" nuevo si cambió el cargo
            if (resp.Codigo_Resp && resp.Codigo_Resp !== codigoOld) {
              alert(`Actualizado. Nuevo Codigo_Resp: ${resp.Codigo_Resp}`);
            } else {
              alert('Actualización exitosa');
            }
            this.$router.push({ name: 'ResponsableView' });
          } else {
            alert('Error al actualizar: ' + (resp.error || 'desconocido'));
          }
        })
        .catch(err => {
          console.error(err);
          alert('Error en la petición de actualización');
        });
    }
  }
}
</script>
