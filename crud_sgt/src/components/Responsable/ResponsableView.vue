<template>
  <div class="container">
    <div class="card">
      <div class="card-header">Listar Responsables</div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-primary">
            <thead>
              <tr>
                <th>Codigo</th>
                <th>ID</th>
                <th>Cédula</th>
                <th>Nombre completo</th>
                <th>Cargo</th>
                <th>Teléfono</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="resp in pacientes" :key="resp.Codigo_Resp">
                <td>{{ resp.Codigo_Resp }}</td>
                <td>{{ resp.ID_Resp }}</td>
                <td>{{ resp.Cedula }}</td>
                <td>{{ resp.Nombre }} {{ resp.Apellido }}</td>
                <td>{{ resp.Cargo }}</td>
                <td>{{ resp.Telefono }}</td>
                <td>
                  <div class="btn-group"> 
                    <router-link
                      :to="{ name: 'EditarResponsableView', params: { Codigo_Resp: resp.Codigo_Resp } }"
                      class="btn btn-warning"
                    >
                      Editar
                    </router-link>
                    <button
                      type="button"
                      @click="borrarPaciente(resp.Codigo_Resp)"
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
      pacientes: []
    }
  },
  created() {
    this.consultarPacientes()
  },
  methods: {
    consultarPacientes() {
      fetch('http://localhost/practica1_sgt/apis/responsable.php')
        .then(res => res.json())
        .then(data => {
          if (Array.isArray(data) && data.length > 0 && !data[0].success) {
            this.pacientes = data
          }
        })
        .catch(console.error)
    },
    borrarPaciente(codigo) {
      fetch('http://localhost/practica1_sgt/apis/responsable.php?borrar=' + codigo)
        .then(res => res.json())
        .then(resp => {
          if (resp.success) {
            this.consultarPacientes() // recargar tabla en lugar de window.location.href
          }
        })
        .catch(console.error)
    }
  }
}
</script>