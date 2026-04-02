async function cargarProductosInventario() {
    try {
        const resp = await fetch("../api/inventory.php");
        const data = await resp.json();

        if (!data.success) {
            alert(data.mensaje);
            return;
        }

        const container = document.querySelector("#ProductosView");
        if (!container) return;

        container.innerHTML = "";

        const productos = data.resultado.products;

        productos.forEach((p, index) => {
            const tr = document.createElement("tr");

            tr.innerHTML = `
        <td>${p.sku}</td>
        <td>${p.nombre}</td>
        <td style="max-width: 300px; word-wrap: break-word; overflow: hidden;">
            ${p.descripcion}
        </td>
        <td>${p.categoria}</td> 
        <td>$${p.precio}</td> 
        <td>${p.stock} pz.</td>
        <td style="font-weight:bold">
          ${p.estado === 1
                    ? `<span class="tk-status tk-paid">Activo</span>`
                    : `<span class="tk-status tk-cancelled">Inactivo</span>`
                }
        </td> 
        <td>
          <button class="btn-editar"
            style="background:#5b2eff; font-size:14px; color:white; border:none; padding:10px 16px; border-radius:5px; cursor:pointer;">
            Editar
          </button>

          <button class="btn-eliminar"
            style="background:#ff3b3b; font-size:14px; color:white; border:none; padding:10px 16px; border-radius:5px; cursor:pointer; margin-left:5px;">
            Eliminar
          </button>
        </td>
      `;

            const btnEditar = tr.querySelector(".btn-editar");
            const btnEliminar = tr.querySelector(".btn-eliminar");

            btnEditar.addEventListener("click", () => {
                abrirModalEditarProducto(p);
            });

            btnEliminar.addEventListener("click", () => {
                eliminarProducto(p.id);
            });

            container.appendChild(tr);
        });

    } catch (err) {
        console.error(err);
        alert("Error al cargar productos");
    }
}

// Editar / Agregar producto
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
    const form = document.getElementById("formProducto");

    if (!btnMostrar || !btnCerrar || !modal) return;

    // Modo Agregar
    btnMostrar.addEventListener("click", () => {
        form.reset();

        document.getElementById("id_producto").value = "";
        document.getElementById("tituloModalProducto").textContent = "Agregar producto";
        document.getElementById("btnSubmitProducto").textContent = "Agregar";

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

function abrirModalEditarProducto(producto) {
    const modal = document.getElementById("modalProducto");

    document.getElementById("tituloModalProducto").textContent = "Editar producto";
    document.getElementById("btnSubmitProducto").textContent = "Actualizar";

    document.getElementById("id_producto").value = producto.id;
    document.querySelector('[name="nombre"]').value = producto.nombre;
    document.querySelector('[name="descripcion"]').value = producto.descripcion;
    document.querySelector('[name="precio"]').value = producto.precio;
    document.querySelector('[name="stock"]').value = producto.stock;

    cargarCategorias().then(() => {
        document.querySelector('[name="id_categoria"]').value = producto.id_categoria;
    });

    document.querySelector('[name="activo"]').value = producto.estado;

    modal.classList.remove("oculto");
}

function iniciarFormProducto() {
    const formProducto = document.getElementById("formProducto");

    if (!formProducto) return;

    formProducto.addEventListener("submit", async (e) => {
        e.preventDefault();

        const formData = new FormData(formProducto);
        const idProducto = formData.get("id_producto");

        const data = {
            nombre: formData.get("nombre"),
            descripcion: formData.get("descripcion"),
            precio: parseFloat(formData.get("precio")),
            stock: parseInt(formData.get("stock")),
            id_categoria: parseInt(formData.get("id_categoria")),
            activo: parseInt(formData.get("activo"))
        };

        let url = "../api/inventory.php";
        let method = "POST";

        if (idProducto) {
            data.id = parseInt(idProducto);
            method = "PUT";
        }

        try {
            const res = await fetch(url, {
                method,
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(data)
            });

            const result = await res.json();

            if (res.ok) {
                alert(idProducto ? "Producto Actualizado" : "Producto Agregado");

                document.getElementById("modalProducto").classList.add("oculto");

                cargarProductosInventario();

            } else {
                alert(result.error || "Error ❌");
            }

        } catch (error) {
            console.error(error);
            alert("Error de conexión");
        }
    });
}

// Eliminar

async function eliminarProducto(id) {
    try {
        const confirmar = confirm("¿Seguro que deseas eliminar este producto?");

        if (!confirmar) return;

        const res = await fetch("../api/inventory.php", {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ id })
        });

        const data = await res.json();

        if (res.ok) {
            alert("Producto Eliminado");

            cargarProductosInventario();

        } else {
            alert(data.error || "Error al Eliminar Producto ");
        }

    } catch (error) {
        console.error(error);
        alert("Error de conexión");
    }
}

document.addEventListener("DOMContentLoaded", () => {
    cargarProductosInventario();
    iniciarModalProducto();
    iniciarFormProducto();
});