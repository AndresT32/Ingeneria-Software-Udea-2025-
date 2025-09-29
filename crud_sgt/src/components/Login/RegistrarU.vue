<template>
  <div class="container mt-5" style="max-width: 400px;">
    <div class="card shadow">
      <div class="card-header text-center bg-success text-white">
        <h4>Registrar Usuario</h4>
      </div>
      <div class="card-body">
        <form @submit.prevent="registrar">
          <div class="mb-3">
            <label class="form-label">Usuario</label>
            <input v-model="usuario" type="text" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input v-model="password" type="password" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Rol</label>
            <select v-model="rol" class="form-control" required>
              <option disabled value="">Seleccione un rol</option>
              <option value="admin">Administrador</option>
              <option value="medico">Médico</option>
              <option value="enfermero">Enfermero</option>
              <option value="usuario">Usuario</option>
            </select>
          </div>
          <button class="btn btn-success w-100" type="submit">Registrar</button>
        </form>

        <div
          v-if="mensaje"
          class="alert mt-3"
          :class="{'alert-success': exito, 'alert-danger': !exito}"
        >
          {{ mensaje }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      usuario: "",
      password: "",
      rol: "",
      mensaje: "",
      exito: false,
    };
  },
  methods: {
    registrar() {
      fetch("http://localhost/practica1_sgt/apis/registrar.php?insertar=1", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          usuario: this.usuario,
          password: this.password,
          rol: this.rol,
        }),
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            this.mensaje = "Usuario registrado correctamente";
            this.exito = true;

            // limpiar formulario
            this.usuario = "";
            this.password = "";
            this.rol = "";

            // redirigir al login después de un pequeño delay
            setTimeout(() => {
              this.$router.push({ name: "LoginU" }); // asegúrate de que tu ruta de login se llame "Login"
            }, 1500);
          } else {
            this.mensaje = data.error || "No se pudo registrar";
            this.exito = false;
          }
        })
        .catch(() => {
          this.mensaje = "Error de conexión al servidor";
          this.exito = false;
        });
    },
  },
};
</script>
