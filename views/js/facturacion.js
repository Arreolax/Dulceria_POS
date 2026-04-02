let clientes = [];
let tickets = [];

async function cargarClientes() {
  try {
    const res = await fetch("../api/clients.php");

    // Si la respuesta no es 200 OK, lanzamos un error
    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);

    clientes = await res.json();

    const select = document.getElementById("select-cliente");
    select.innerHTML = `<option value="">Selecciona cliente</option>`;

    clientes.forEach(c => {
      select.innerHTML += `<option value="${c.id}">${c.razon_social}</option>`;
    });
  } catch (error) {
    console.error("Error al cargar los clientes:", error);
  }
}

async function cargarTickets() {
  try {
    const res = await fetch("../api/tickets-invoices.php");

    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);

    const data = await res.json();

    if (!data.success) {
      console.warn("El servidor rechazó la carga de tickets:", data.mensaje);
      return;
    }

    tickets = data.resultado.tickets;

    const select = document.getElementById("select-ticket");
    select.innerHTML = `<option value="">Selecciona ticket</option>`;

    tickets.forEach(t => {
      select.innerHTML += `<option value="${t.id_ticket}">Folio ${t.folio}</option>`;
    });
  } catch (error) {
    console.error("Error al cargar los tickets:", error);
  }
}

document.getElementById("select-cliente").addEventListener("change", (e) => {
  const cliente = clientes.find(c => c.id == e.target.value);
  if (!cliente) return;

  document.getElementById("fact-rfc").value = cliente.rfc || "";
  document.getElementById("fact-razon").value = cliente.razon_social || "";
  document.getElementById("fact-correo").value = cliente.email || "";
  document.getElementById("fact-cp").value = cliente.codigo_postal || "";
  document.getElementById("fact-regimen").value = cliente.regimen_fiscal || "";
  document.getElementById("fact-usocfdi").value = cliente.uso_cfdi || "";
});

document.getElementById("select-ticket").addEventListener("change", (e) => {
  const ticket = tickets.find(t => t.id_ticket == e.target.value);
  if (!ticket) return;

  document.getElementById("ticket-folio").value = ticket.folio;
  document.getElementById("ticket-total").value = "$" + ticket.total_venta;
  document.getElementById("ticket-fecha").value = ticket.fecha_venta;
  document.getElementById("ticket-metodo").value = ticket.metodo_pago;
  document.getElementById("ticket-cliente").value = ticket.nombre_cliente;

  let html = `
    <h4 style="margin-bottom:.5rem;">Productos</h4>
    <table style="width:100%; border-collapse:collapse; font-size:.9rem;">
      <thead>
        <tr style="background:#f3f4f6;">
          <th style="text-align:left; padding:.4rem;">Producto</th>
          <th>Cant.</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
  `;

  ticket.productos.forEach(p => {
    html += `
      <tr>
        <td style="padding:.3rem;">${p.producto}</td>
        <td style="text-align:center;">${p.cantidad}</td>
        <td style="text-align:right;">$${p.subtotal}</td>
      </tr>
    `;
  });

  html += `
      </tbody>
    </table>
  `;

  document.getElementById("fact-resumen").innerHTML = html;
});

document.getElementById("btn-generar-fact").addEventListener("click", async () => {
  const id_cliente = document.getElementById("select-cliente").value;
  const id_ticket = document.getElementById("select-ticket").value;

  if (!id_cliente || !id_ticket) {
    alert("Selecciona cliente y ticket");
    return;
  }

  const res = await fetch("../api/facturacion/generar.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      id_cliente: Number(id_cliente),
      id_ticket: Number(id_ticket)
    })
  });

  const data = await res.json();
  const resumen = document.getElementById("fact-resumen");

  if (!res.ok) {
    resumen.innerHTML = `<span style="color:red;">${data.message || 'Error'}</span>`;
    return;
  }

  resumen.innerHTML = `
  <h3>Factura</h3>
  <p><b>Folio:</b> ${data.factura.folio}</p>
  <p><b>UUID:</b> ${data.factura.uuid}</p>

  <h4>Cliente</h4>
  <p><b>Nombre:</b> ${data.factura.razon_social}</p>

  <h4>Ticket</h4>
  <p><b>ID Ticket:</b> ${data.factura.id_ticket}</p>

  <h4>Productos</h4>
  <ul>
    ${data.detalles.map(d => `
      <li>${d.producto} - ${d.cantidad} x $${d.precio_unitario}</li>
    `).join('')}
  </ul>
    <p><b>Total:</b> $${data.factura.total}</p>

  <br>
  <button onclick="descargarPDF('${data.factura.id_factura}')">PDF</button>
  <button onclick="descargarXML('${data.factura.id_factura}')">XML</button>
`;
});

function descargarPDF(folio) {
  window.open(`../api/facturacion/descargar.php?id_factura=${folio}&formato=pdf`, '_blank');
}

function descargarXML(folio) {
  window.open(`../api/facturacion/descargar.php?id_factura=${folio}&formato=xml`, '_blank');
}

document.getElementById("btnNuevoCliente").addEventListener("click", () => {
  document.getElementById("modalCliente").style.display = "flex";
});

function cerrarModal() {
  document.getElementById("modalCliente").style.display = "none";
}

// Guardar cliente
document.getElementById("formCliente").addEventListener("submit", async (e) => {
  e.preventDefault();

  const btn = document.getElementById("btnGuardarCliente");

  btn.disabled = true;
  btn.textContent = "Guardando...";

  const body = {
    rfc: document.getElementById("cliente-rfc").value,
    razon_social: document.getElementById("cliente-razon").value,
    uso_cfdi: document.getElementById("cliente-uso").value,
    regimen_fiscal: document.getElementById("cliente-regimen").value,
    email: document.getElementById("cliente-email").value,
    direccion: document.getElementById("cliente-direccion").value,
    codigo_postal: document.getElementById("cliente-cp").value
  };

  try {
    const res = await fetch("../api/clients.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify(body)
    });

    const data = await res.json();

    if (!res.ok || !data.success) {
      alert(data.mensaje || "Error al guardar");
      return;
    }

    alert("Cliente creado ✅");

    cerrarModal();
    await cargarClientes();

    document.getElementById("select-cliente").value = data.data.id;

  } catch (err) {
    alert("Error de conexión");
  } finally {
    btn.disabled = false;
    btn.textContent = "Guardar Cliente";
  }
});

cargarClientes();
cargarTickets();