document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    if (!email || !password) {
      alert("Completa todos los campos");
      return;
    }

    try {
      const res = await fetch("../api/login.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({ email, password })
      });

      const data = await res.json();

      if (res.ok) {
        //alert("Login exitoso");
        window.location.href = "index.php";
      } else {
        alert(data.error || "Error al iniciar sesión");
      }

    } catch (error) {
      alert("No se pudo conectar con el servidor");
      console.error(error);
    }
  });
});
