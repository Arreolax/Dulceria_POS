async function cargarTickets() {
    try {
        const resp = await fetch("../api/ticket.php");
        const data = await resp.json();

        if (!data.success) {
            alert(data.mensaje);
            return;
        }

        const container = document.querySelector("#TicketsView");
        container.innerHTML = "";

        const tickets = data.resultado.tickets;

        if (tickets.length === 0) {
            const noVentas = document.createElement("tr");
            noVentas.innerHTML = `
                <td colspan="7" style="color:red; padding:50px; text-align:center; font-weight:bold; font-size:24px">
                    ⚠️ Tickets No Encontrados ⚠️
                </td>`;
            container.appendChild(noVentas);
            return;
        }

        tickets.forEach((s) => {
            const tr = document.createElement("tr");
            tr.style.borderBottom = "1px solid #ddd";

            const productosHTML = `
                <table style="width:100%; border-collapse:collapse; font-size:13px;">
                    <thead>
                        <tr>
                            <th style="font-size:12px; padding:0px; padding-left: 10px; border:1px solid #ddd;">Producto</th>
                            <th style="font-size:12px; padding:0px; padding-left: 10px; border:1px solid #ddd;">Cant.</th>
                            <th style="font-size:12px; padding:0px; padding-left: 10px; border:1px solid #ddd;">Precio</th>
                            <th style="font-size:12px; padding:0px; padding-left: 10px; border:1px solid #ddd;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${s.productos.map(p => `
                            <tr>
                                <td style="padding:5px; padding-left: 10px; border:1px solid #ddd;">${p.producto}</td>
                                <td style="text-align: center; padding:5px; border:1px solid #ddd; text-align:center;">${p.cantidad} pz.</td>
                                <td style="text-align: center; padding:5px; border:1px solid #ddd;">$${p.precio ?? (p.subtotal / p.cantidad)}</td>
                                <td style="text-align: center; padding:5px; border:1px solid #ddd; font-weight:bold;">$${p.subtotal}</td>
                            </tr>
                        `).join("")}
                    </tbody>
                </table>
            `;

            tr.innerHTML = `
                <td style="padding:10px;">${s.fecha_venta}</td>
                <td style="padding:10px;">${s.folio}</td>
                <td style="padding:10px;">${s.nombre_cliente}</td>
                
                <td style="padding:10px;">
                    ${productosHTML}
                </td>

                <td style="padding:10px;">${s.metodo_pago}</td>

                <td style="padding:10px; font-weight:bold;">
                    $${s.total_venta}
                </td>

                <td style="padding:10px;">
                    <button 
                        style="letter-spacing:1px; width:150px;" class="tk-btn tk-btn-delete">
                        IMPRIMIR
                    </button>
                    <br>
                    <button onclick="facturar(${s.id_ticket})"
                        style="letter-spacing:1px; width:150px;" class="tk-btn tk-btn-delete">
                        FACTURAR
                    </button>
                </td>
            `;

            container.appendChild(tr);
        });

    } catch (err) {
        console.error(err);
        alert("Error al cargar ventas");
    }
}


document.addEventListener("DOMContentLoaded", () => {
    cargarTickets();
});