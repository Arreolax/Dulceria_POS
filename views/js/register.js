document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("registerForm");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const nombre = document.getElementById("name").value.trim();
        const correo = document.getElementById("email").value.trim();
        const contrasena = document.getElementById("password").value.trim();
        const con_contrasena = document.getElementById("con_password").value.trim();
        const telefono = document.getElementById("phone").value.trim();

        if (!nombre || !correo || !contrasena || !con_contrasena || !telefono) {
            alert("Completa todos los campos");
            return;
        }

        if (contrasena != con_contrasena) {
            alert("Contraseñas mal escritas");
            return;
        }

        try {
            const res = await fetch("../api/register.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ nombre, correo, contrasena, telefono })
            });

            const data = await res.json();
            console.log(data);

            if (res.ok) {
                alert("Cuenta Creada!");
                setTimeout(() => {
                    window.location.href = "index.php";
                }, 1000);
            } else {
                alert(data.error || "Error al iniciar sesión");
            }

        } catch (error) {
            alert("No se pudo conectar con el servidor");
            console.error(error);
        }
    });
});
