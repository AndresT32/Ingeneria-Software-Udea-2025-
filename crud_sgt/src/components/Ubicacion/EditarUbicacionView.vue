<template>
  <div class="container">
    <div class="card">
      <div class="card-header">Editar ubicación</div>
      <div class="card-body">
        <form @submit.prevent="actualizarUbicacion">
          <!-- Mostrar Codigo_ubi y ID_ubi como lectura, no editables -->
          <div class="row mb-1">
            <label>Codigo (no editable)</label>
            <input type="text" class="form-control" :value="codigoUbi" disabled>
          </div>
          <div class="row mb-1">
            <label>ID (no editable)</label>
            <input type="text" class="form-control" :value="ubicacion.ID_ubi" disabled>
          </div>

          <!-- Campos editables -->
          <div class="row mb-1">
            <label for="Nombre_ubi">Nombre de la Ubicación</label>
            <input id="Nombre_ubi" v-model="ubicacion.Nombre_ubi" class="form-control" required>
          </div>

          <div class="row mb-1">
            <label for="Ubicacion">Ubicación física</label>
            <input id="Ubicacion" v-model="ubicacion.Ubicacion" class="form-control">
          </div>

          <div class="row mb-1">
            <label for="Telefono">Teléfono</label>
            <input id="Telefono" v-model="ubicacion.Telefono" class="form-control">
          </div>

          <div class="btn-group" role="group">
            <button type="submit" class="btn btn-success">Guardar</button>
            <router-link :to="{ name: 'UbicacionView' }" class="btn btn-warning">Cancelar</router-link>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['Codigo_ubi'], // si usas props: true en la ruta
  data() {
    return {
      ubicacion: {
        ID_ubi: '',
        Nombre_ubi: '',
        Ubicacion: '',
        Telefono: ''
      }
    }
  },
  computed: {
    codigoUbi() {
      return this.Codigo_ubi || this.$route.params.Codigo_ubi || '';
    }
  },
  created() {
    this.obtenerUbicacionID();
  },
  methods: {
    obtenerUbicacionID() {
      const codigo = encodeURIComponent(this.codigoUbi);
      fetch(`http://localhost/practica1_sgt/apis/ubicacion.php?consultar=${codigo}`)
        .then(res => res.json())
        .then(data => {
          if (!data || data.success === 0) {
            alert('Ubicación no encontrada');
            this.$router.push({ name: 'UbicacionView' });
            return;
          }
          this.ubicacion = data;
        })
        .catch(err => {
          console.error(err);
          alert('Error al obtener datos de la ubicación');
        });
    },

    actualizarUbicacion() {
      const codigoOld = this.codigoUbi;
      const datosEnviar = {
        Nombre_ubi: this.ubicacion.Nombre_ubi,
        Ubicacion: this.ubicacion.Ubicacion,
        Telefono: this.ubicacion.Telefono
      };

      fetch(`http://localhost/practica1_sgt/apis/ubicacion.php?actualizar=${encodeURIComponent(codigoOld)}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datosEnviar)
      })
        .then(res => res.json())
        .then(resp => {
          if (resp.success) {
            if (resp.Codigo_ubi && resp.Codigo_ubi !== codigoOld) {
              alert(`Actualizado. Nuevo Codigo_ubi: ${resp.Codigo_ubi}`);
            } else {
              alert('Actualización exitosa');
            }
            this.$router.push({ name: 'UbicacionView' });
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