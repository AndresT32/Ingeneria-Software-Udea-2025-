<template>
  <div class="container">
    <div class="card">
      <div class="card-header">Listar Ubicaciones</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-primary">
            <thead>
              <tr>
                <th>Codigo</th>
                <th>ID</th>
                <th>Nombre</th>
                <th>Ubicación</th>
                <th>Teléfono</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="ubi in ubicaciones" :key="ubi.Codigo_ubi">
                <td>{{ ubi.Codigo_ubi }}</td>
                <td>{{ ubi.ID_ubi }}</td>
                <td>{{ ubi.Nombre_ubi }}</td>
                <td>{{ ubi.Ubicacion }}</td>
                <td>{{ ubi.Telefono }}</td>
                <td>
                  <div class="btn-group"> 
                    <router-link
                      :to="{ name: 'EditarUbicacionView', params: { Codigo_ubi: ubi.Codigo_ubi } }"
                      class="btn btn-warning"
                    >
                      Editar
                    </router-link>
                    <button
                      type="button"
                      @click="borrarUbicacion(ubi.Codigo_ubi)"
                      class="btn btn-danger"
                    >
                      Borrar
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer">@Ingenieriadesw</div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      ubicaciones: []
    }
  },
  created() {
    this.consultarUbicaciones()
  },
  methods: {
    consultarUbicaciones() {
      fetch('http://localhost/practica1_sgt/apis/ubicacion.php')
        .then(res => res.json())
        .then(data => {
          if (Array.isArray(data) && data.length > 0 && !data[0].success) {
            this.ubicaciones = data
          }
        })
        .catch(console.error)
    },
    borrarUbicacion(codigo) {
      fetch('http://localhost/practica1_sgt/apis/ubicacion.php?borrar=' + codigo)
        .then(res => res.json())
        .then(resp => {
          if (resp.success) {
            this.consultarUbicaciones() // recargar tabla
          } else {
            alert('Error al borrar: ' + (resp.error || 'Desconocido'))
          }
        })
        .catch(console.error)
    }
  }
}
</script>