
async function cargarCategorias() {
    try {

        const resp = await fetch("../api/category.php");
        const data = await resp.json();

        if (!data.success) {
            alert(data.mensaje);
            return;
        }

        console.log(data);

        const container = document.querySelector("#categoriasView");
        if (!container) return;

        container.innerHTML = "";

        data.resultado.categorias.forEach((c) => {
            const div = document.createElement("div");

            div.style.display = "flex";
            div.style.justifyContent = "space-between";
            div.style.alignItems = "center";
            div.style.padding = "20px";
            div.style.borderRadius = "12px";
            div.style.background = "#ffffff";
            div.style.boxShadow = "0 4px 12px rgba(0,0,0,0.08)";
            div.style.border = "1px solid #e5e7eb";
            div.style.marginBottom = "16px";
            div.style.transition = "0.3s ease";

            document.body.appendChild(div);

            div.innerHTML = `
       <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">

    <div style="display: flex; align-items: center; gap: 16px; flex: 1;">

        <img src="${c.imagen}" alt="${c.nombre}" 
            style="width: 100px; height: 100px; object-fit: contain; border-radius: 8px;">

        <div style="display: flex; flex-direction: column; gap: 6px;">
            <h2 style="margin: 0; font-size: 20px; font-weight: 600; color: #111827;">
                ${c.nombre}
            </h2>

            <p style="margin: 0; font-size: 14px; color: #6b7280;">
                ${c.descripcion}
            </p>

            <span style="font-size: 13px; font-weight: 500; color: #2563eb; background: #eff6ff; padding: 4px 10px; border-radius: 20px; width: fit-content;">
                ${c.cantidad} productos
            </span>
        </div>
    </div>

    <button href="products.php"
        style="margin-left: 20px; padding: 10px 18px; background: #5b2eff; color: #ffffff; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; transition: 0.3s ease;"
        onmouseover="this.style.background='#4f46e5'"
        onmouseout="this.style.background='#5b2eff'">
        Ver productos
    </button>
</div>
      `;

            container.appendChild(div);
        });

    } catch (err) {
        console.error(err);
        alert("Error al cargar categorias");
    }
}

document.addEventListener("DOMContentLoaded", () => {
    cargarCategorias();
});