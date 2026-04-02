const SAT_FORMA_PAGO = {
    '01': 'Efectivo',
    '02': 'Cheque nominativo',
    '03': 'Transferencia electrónica de fondos',
    '04': 'Tarjeta de crédito',
    '28': 'Tarjeta de débito',
    '99': 'Por definir',
};

const SAT_METODO_PAGO = {
    'PUE': 'Pago en una sola exhibición',
    'PPD': 'Pago en parcialidades o diferido',
};

function obtenerFormaPago(clave) {
    return SAT_FORMA_PAGO[clave] || `Desconocido (${clave})`;
}

function obtenerMetodoPago(clave) {
    return SAT_METODO_PAGO[clave] || `Desconocido (${clave})`;
}

function capitalizar(texto) {
  if (!texto) return "";
  return texto.charAt(0).toUpperCase() + texto.slice(1);
}

async function cargarFacturas() {
    try {

        const resp = await fetch("../api/invoices.php");
        const data = await resp.json();

        if (!data.success) {
            alert(data.mensaje);
            return;
        }
        console.log(data);

        const container = document.querySelector("#FacturasView");
        container.innerHTML = "";

        const facturas = data.resultado.invoices;

        if (facturas.length === 0) {
            const noFacturas = document.createElement("tr");
            noFacturas.innerHTML = `
                <td colspan="10" style="color:red; font-weight:bold; padding:50px; font-style: italic; text-align:center; font-weight:bold; font-size:24px">
                    ⚠️ Facturas No Encontradas ⚠️
                </td>`;
            container.appendChild(noFacturas);
            return;
        }

        facturas.forEach((f) => {
            const tr = document.createElement("tr");
            tr.style.textAlign = "center";
            tr.style.borderBottom = "1px solid #ddd";

            tr.innerHTML = `
  <td style="padding:10px;">${f.folio_factura}</td>
  <td style="padding:10px;">${f.fecha_emision}</td>
  <td style="padding:10px;">${f.razon_social}</td>
<td style="padding:10px;">${obtenerMetodoPago(f.metodo_pago)}</td>
  <td style="padding:10px;">${obtenerFormaPago(f.forma_pago)}</td>
  <td style="padding:10px;">${f.uso_cfdi}</td>
  <td style="padding:10px;">$${f.subtotal}</td>
  <td style="padding:10px;">$${f.impuestos}</td>
  <td style="padding:10px;"><strong>$${f.total}</strong></td>
  <td style="padding:10px; color: green;">${capitalizar(f.estado)}</td>
  <td style="padding:10px;">
    <button onclick="descargarPDF('${f.id_factura}')">PDF</button>
    <button onclick="descargarXML('${f.id_factura}')">XML</button>
  </td>
`;

            container.appendChild(tr);
        });

    } catch (err) {
        console.error(err);
        alert("Error al cargar ventas");
    }
}

function descargarPDF(folio) {
    window.open(`../api/facturacion/descargar.php?id_factura=${folio}&formato=pdf`, '_blank');
}

function descargarXML(folio) {
    window.open(`../api/facturacion/descargar.php?id_factura=${folio}&formato=xml`, '_blank');
}



document.addEventListener("DOMContentLoaded", () => {
    cargarFacturas();
});