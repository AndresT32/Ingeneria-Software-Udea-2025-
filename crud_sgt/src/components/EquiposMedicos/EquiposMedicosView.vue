<template>
  <div class="container">
    <div class="card">
      <div class="card-header">Listar Equipos Médicos</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-primary">
            <thead>
              <tr>
                <th>ID</th>
                <th>Número Activo</th>
                <th>Marca</th>
                <th>Modelo</th>
                <th>Código Ubicación</th>
                <th>Código Responsable</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="eq in equipos" :key="eq.ID_EM">
                <td>{{ eq.ID_EM }}</td>
                <td>{{ eq.Num_activo }}</td>
                <td>{{ eq.Marca }}</td>
                <td>{{ eq.Modelo }}</td>
                <td>{{ eq.Codigo_ubi }}</td>
                <td>{{ eq.Codigo_Resp }}</td>
                <td>
                  <div class="btn-group">
                    <router-link
                      :to="{ name: 'EditarEquiposMedicosView', params: { ID_EM: eq.ID_EM } }"
                      class="btn btn-warning"
                    >
                      Editar
                    </router-link>
                    <button
                      type="button"
                      @click="borrarEquipo(eq.ID_EM)"
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
      equipos: []
    }
  },
  created() {
    this.consultarEquipos()
  },
  methods: {
    consultarEquipos() {
      fetch('http://localhost/practica1_sgt/apis/equiposMedicos.php')
        .then(res => res.json())
        .then(data => {
          if (Array.isArray(data) && data.length > 0 && !data[0].success) {
            this.equipos = data
          }
        })
        .catch(console.error)
    },
    borrarEquipo(id) {
      fetch('http://localhost/practica1_sgt/apis/equiposMedicos.php?borrar=' + id)
        .then(res => res.json())
        .then(resp => {
          if (resp.success) {
            this.consultarEquipos() // recargar tabla
          }
        })
        .catch(console.error)
    }
  }
}
</script>
