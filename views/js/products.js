async function haySesion() {
  const res = await fetch("../api/session.php");
  const data = await res.json();
  return data.logged;
}

async function cargarProductos() {
  try {
    const sesionActiva = await haySesion();

    const resp = await fetch("../api/product.php");
    const data = await resp.json();

    if (!data.success) {
      alert(data.mensaje);
      return;
    }

    const container = document.querySelector("#productosView");
    if (!container) return;

    container.innerHTML = "";

    data.resultado.products.forEach((p) => {
      const div = document.createElement("div");
      div.classList.add("col-3");

      div.innerHTML = `
        <section class="box feature producto-card">
          <a class="image featured img-container">
            <img src="${p.imagen}" alt="${p.nombre}" class="producto-img" />
            <span class="etiqueta">
              ${p.stock <= 0 ? "SIN STOCK" : p.stock <= 20 ? "POCAS UNIDADES" : "DISPONIBLE"}
            </span>
          </a>

          <div class="producto-body">
            <h3 class="producto-titulo">${p.nombre}</h3>

            <p class="producto-desc">
              ${p.descripcion}<br><br>
            </p>

            <p class="producto-desc">
              Categoria: <a href="#">${p.categoria}</a>
            </p>

            <p class="producto-stock">
              ${p.stock <= 0 
                ? `<strong style="color: red;">Unidades No Disponibles</strong>` 
                : "Unidades Disponibles: " + `<strong>${p.stock} pz.</strong>`}
            </p>

            <p class="producto-precio">
              $${parseFloat(p.precio).toFixed(2)}
            </p>
          </div>

          ${
            sesionActiva
              ? `<button 
                  class="btn custom-btn btn-sm agregar-carrito"
                  data-id="${p.id}" ${p.stock <= 0 ? "disabled" : ""}>
                  ${p.stock <= 0 ? "No Disponible" : "AGREGAR AL CARRITO"}
                </button>`
              : `<button 
                  class="btn custom-btn btn-sm agregar-carrito" disabled>
                  ${p.stock <= 0 ? "No Disponible" : "Inicia Sesion Primero"}
                </button>`
          }
        </section>
      `;

      container.appendChild(div);
    });

  } catch (err) {
    console.error(err);
    alert("Error al cargar productos");
  }
}

async function agregarCarrito(productId) {
  try {
    const resp = await fetch("../api/cart.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        id_producto: productId,
        cantidad: 1,
      }),
    });

    const data = await resp.json();

    if (!data.ok) {
      alert(data.error || "Error al agregar al carrito");
      return;
    }

    alert("Producto agregado a tu carrito 🛒");
    cargarProductos();

  } catch (error) {
    console.error(error);
    alert("Error al agregar al carrito");
  }
}

let categoriasCargadas = false;

async function cargarCategorias() {
  try {
    const selectCategorias = document.getElementById("selectCategorias");

    if (!selectCategorias) return;

    if (categoriasCargadas) return;

    const res = await fetch("../api/category.php");
    const data = await res.json();

    if (!data.success) {
      throw new Error("La API falló");
    }

    const categorias = data.resultado.categorias;

    selectCategorias.innerHTML = '<option value="">Selecciona una categoría</option>';

    categorias.forEach(cat => {
      const option = document.createElement("option");
      option.value = cat.id;
      option.textContent = cat.nombre;
      selectCategorias.appendChild(option);
    });

    categoriasCargadas = true;

  } catch (error) {
    console.error("Error cargando categorías:", error);
  }
}

function iniciarModalProducto() {
  const btnMostrar = document.getElementById("btnNuevoProd");
  const btnCerrar = document.getElementById("btnCerrar");
  const modal = document.getElementById("modalProducto");

  if (!btnMostrar || !btnCerrar || !modal) return;

  btnMostrar.addEventListener("click", () => {
    modal.classList.remove("oculto");
    cargarCategorias();
  });

  btnCerrar.addEventListener("click", () => {
    modal.classList.add("oculto");
  });

  modal.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.classList.add("oculto");
    }
  });
}

function iniciarFormProducto() {
  const formProducto = document.getElementById("formProducto");

  if (!formProducto) return;

  formProducto.addEventListener("submit", async (e) => {
    e.preventDefault();

    const formData = new FormData(formProducto);

    const data = {
      nombre: formData.get("nombre"),
      descripcion: formData.get("descripcion"),
      precio: parseFloat(formData.get("precio")),
      stock: parseInt(formData.get("stock")),
      id_categoria: parseInt(formData.get("id_categoria")),
      activo: parseInt(formData.get("activo"))
    };

    try {
      const res = await fetch("../api/product.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
      });

      const result = await res.json();

      if (res.ok) {
        alert("Producto agregado correctamente ✅");
        document.getElementById("modalProducto").classList.add("oculto");

        cargarProductos(); 

      } else {
        alert(result.error || "Error al agregar producto ❌");
      }

    } catch (error) {
      console.error(error);
      alert("Error de conexión");
    }
  });
}

document.addEventListener("DOMContentLoaded", () => {
  cargarProductos();
  iniciarModalProducto();
  iniciarFormProducto();
});

document.addEventListener("click", (e) => {
  if (e.target.classList.contains("agregar-carrito")) {
    const productId = e.target.dataset.id;
    agregarCarrito(productId);
  }
});