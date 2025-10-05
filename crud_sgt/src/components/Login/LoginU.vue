<template>
  <div class="container mt-5" style="max-width: 400px;">
    <div class="card shadow">
      <div class="card-header text-center bg-primary text-white">
        <h4>Login</h4>
      </div>
      <div class="card-body">
        <form @submit.prevent="login">
          <div class="mb-3">
            <label class="form-label">Usuario</label>
            <input v-model="usuario" type="text" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input v-model="password" type="password" class="form-control" required />
          </div>
          <button class="btn btn-primary w-100" type="submit">Ingresar</button>
        </form>

        <!-- Botón para ir al registro -->
        <div class="text-center mt-3">
          <p>¿No tienes cuenta?</p>
          <button class="btn btn-outline-secondary w-100" @click="irARegistro">
            Registrar nuevo usuario
          </button>
        </div>

        <!-- Mensajes -->
        <div v-if="mensaje" class="alert mt-3" :class="{'alert-success': exito, 'alert-danger': !exito}">
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
      mensaje: "",
      exito: false,
    };
  },
  methods: {
    login() {
      fetch("http://localhost/practica1_sgt/apis/login.php?insertar=1", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          usuario: this.usuario,
          password: this.password,
        }),
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            this.mensaje = "Bienvenido " + data.usuario;
            this.exito = true;

            // ✅ Guardar sesión en localStorage
            localStorage.setItem("usuario", JSON.stringify(data));

            // ✅ Actualizar reactividad global en App.vue (sin recargar)
            this.$root.loggedIn = true;
            this.$root.usuario = data.usuario;

            // ✅ Redirigir a página principal
            this.$router.push({ name: "home" }); 
          } else {
            this.mensaje = data.error;
            this.exito = false;
          }
        })
        .catch(() => {
          this.mensaje = "Error de conexión al servidor";
          this.exito = false;
        });
    },
    irARegistro() {
      this.$router.push({ name: "RegistrarU" });
    },
  },
};
</script>

