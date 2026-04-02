async function cargarVentas() {
    try {

        const resp = await fetch("../api/sale.php");
        const data = await resp.json();

        if (!data.success) {
            alert(data.mensaje);
            return;
        }
        console.log(data);

        const container = document.querySelector("#VentasView");
        container.innerHTML = "";

        const ventas = data.resultado.ventas;

        if (ventas.length === 0) {
            const noVentas = document.createElement("tr");
            noVentas.innerHTML = `
                <td colspan="10" style="color:red; font-weight:bold; padding:50px; font-style: italic; text-align:center; font-weight:bold; font-size:24px">
                    ⚠️ Ventas No Encontradas ⚠️
                </td>`;
            container.appendChild(noVentas);
            return;
        }

        ventas.forEach((s) => {
            const tr = document.createElement("tr");
            tr.style.textAlign = "left";
            tr.style.borderBottom = "1px solid #ddd";
            tr.style.padding = "10px";

            tr.innerHTML = `
            <td>
                ${s.fecha_venta}
            </td>
            <td>
                ${s.folio_venta}
            </td>
            <td>
                ${s.nombre_cliente}
            </td>
            <td>
                ${s.productos}
            </td> 
            <td>
                ${s.estatus_venta === "PAGADA" ?
                    `<span class="tk-status tk-paid">${s.estatus_venta}</span>`
                    : s.estatus_venta === "PROCESANDO" ? `<span class="tk-status tk-pending">${s.estatus_venta}</span>`
                        : s.estatus_venta === "CREADA" ? `<span class="tk-status tk-created">${s.estatus_venta}</span>`
                            : `<span class="tk-status tk-cancelled">${s.estatus_venta}</span>`}
            </td> 
            <td style="font-weight:bold">
                $${s.total_venta}
            </td> 
            <td>
            ${s.estatus_venta === "PAGADA" ?
                    `<button onclick="generarTicket()" style="letter-spacing:1px; width:150px;" class="tk-btn tk-btn-delete"> 
                        Generar Ticket 
                    </button>`
                : s.estatus_venta === "CANCELADA" ?
                    ``
                : `<button  onclick="cancelarVenta(${s.id_venta})" style="letter-spacing:1px; width:150px;" class="tk-btn tk-btn-delete"> 
                    CANCELAR
                </button>`}
            </td>
            `;

            container.appendChild(tr);
        });

    } catch (err) {
        console.error(err);
        alert("Error al cargar ventas");
    }
}
function cancelarVenta(idVenta) {
    if (!confirm("⚠️ Seguro que quieres cancelar la venta? ⚠️")) return;
    fetch(`../api/cancel_sale.php`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ id: idVenta })
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("⚠️ Venta Cancelada ⚠️");
                cargarVentas();
            } else {
                alert(data.message);
            }
        })
        .catch(err => console.error(err));
}

document.addEventListener("DOMContentLoaded", () => {
    cargarVentas();
});