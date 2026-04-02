async function cargarCarrito() {
    try {

        const resp = await fetch("../api/cart.php");
        const data = await resp.json();

        if (!data.success) {
            alert(data.mensaje);
            return;
        }

        const container = document.querySelector("#CartView");
        container.innerHTML = "";

        let totalGeneral = 0;

        const productos = data.resultado.products;

        if (productos.length === 0) {
            const noProducts = document.createElement("tr");
            noProducts.innerHTML = `
                <td colspan="10" style="color:red; font-weight:bold; padding:50px; font-style: italic; text-align:center; font-weight:bold; font-size:24px">
                    ⚠️ No hay productos en el carrito ⚠️
                    <br><br>
                    <button style="background-color:#5b2eff; color:white; border:none; padding:12px 28px; font-size:16px; font-weight:bold; border-radius:8px; cursor:pointer; box-shadow:0 4px 10px rgba(0,0,0,0.2); transition:all 0.3s ease;"
                    onmouseover="this.style.backgroundColor='#4f46e5'; this.style.transform='scale(1.05)'"
                    onmouseout="this.style.backgroundColor='#5b2eff'; this.style.transform='scale(1)'"
                    onclick="window.location.href='products.php'">
                        Ver productos
                    </button>
                </td>`;
            container.appendChild(noProducts);
            return;
        }

        productos.forEach((p) => {
            const tr = document.createElement("tr");

            const subtotal = parseFloat(p.subtotal);
            totalGeneral += subtotal;

            tr.innerHTML = `
            <td> 
                <img src="${p.imagen}" alt="${p.nombre_producto}" style="width:60px; height:60px; object-fit:cover; border-radius:6px;"> 
            </td> 
            <td>
                ${p.folio_producto ?? "-"}
            </td> 
            <td>
                ${p.nombre_producto}
            </td> 
            <td>
                ${p.descripcion_producto}
            </td> 
            <td>
                ${p.nombre_categoria}
            </td> 
            <td>
                $${parseFloat(p.precio_unitario).toFixed(2)}
            </td> 
            <td> 
                <input type="number" disabled value="${p.cantidad}" min="1" max="${p.stock}" style="width:60px; text-align:center; font-size:16px;" onchange="actualizarCantidad(${p.id}, this.value)" > 
            </td> 
            <td> 
                $${subtotal.toFixed(2)} 
            </td>  
            <td> 
                <button onclick="eliminarProducto(${p.id_producto})" class="tk-btn tk-btn-delete"> 
                    Eliminar 
                </button> 
            </td>`;

            container.appendChild(tr);
        });

        if (totalGeneral > 0) {
            const totalRow = document.createElement("tr");
            totalRow.innerHTML = `
        <td colspan="6" style="text-align:right; font-weight:bold;">
          Total:
        </td>
        <td colspan="2" style="font-weight:bold;">
          $${totalGeneral.toFixed(2)}
        </td>
        <button id="btnConfirmar" onclick="confirmarCompra()" class="btn-comprar">
            Confirmar Compra
        </button>
        
      `;
            container.appendChild(totalRow);
        }

    } catch (err) {
        console.error(err);
        alert("Error al cargar carrito");
    }
}

async function eliminarProducto(id_producto) {
    if (!confirm("Quieres eliminar un producto de tu carrito?")) return;
    //console.log(id_producto);

    try {
        const response = await fetch("../api/cart.php", {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                id_producto: id_producto
            })
        });

        const data = await response.json();

        if (data.ok || data.success) {
            alert("Producto eliminado");
            cargarCarrito();
        } else {
            alert(data.error || data.mensaje);
        }

    } catch (error) {
        console.error(error);
        alert("Error al eliminar producto");
    }
}

async function confirmarCompra() {
    const btn = document.getElementById("btnConfirmar");

    try {
        if (btn) {
            btn.disabled = true;
            btn.innerText = "Procesando...";
        }

        const response = await fetch("../api/sale.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            }
        });

        const data = await response.json();

        if (!data.ok) {
            throw new Error(data.error || "Error al confirmar compra");
        }

        alert(`✅ Compra Realizada`);

        cargarCarrito();

    } catch (error) {
        console.error(error);
        alert("❌ " + error.message);
    } finally {
        if (btn) {
            btn.disabled = false;
            btn.innerText = "Confirmar Compra";
        }
    }
}

document.addEventListener("DOMContentLoaded", () => {
    cargarCarrito();
});