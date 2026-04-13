
// --- CREAR SERVICIO ---
document.getElementById('btnCrear')?.addEventListener('click', () => {
    const data = new FormData();
    data.append('title', document.getElementById('crearTitle').value);
    data.append('category', document.getElementById('crearCategory').value);
    data.append('price', document.getElementById('crearPrice').value);
    data.append('image_url', document.getElementById('crearImage').value);

    fetch('../controllers/ServiceController.php?action=create', {
        method: 'POST',
        body: data
    })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                location.reload();
            } else {
                const el = document.getElementById('crearError');
                el.classList.remove('d-none');
                el.textContent = res.errors?.join(', ') ?? res.message;
            }
        });
});

// --- LLENA CON LOS DATOS DEL SERVICIO ---
document.getElementById('modalEditarServicio')?.addEventListener('show.bs.modal', (event) => {
    const btn = event.relatedTarget;
    document.getElementById('editarId').value = btn.dataset.id;
    document.getElementById('editarTitle').value = btn.dataset.title;
    document.getElementById('editarPrice').value = btn.dataset.price;
    document.getElementById('editarImage').value = btn.dataset.image;

    const sel = document.getElementById('editarCategory');
    [...sel.options].forEach(o => o.selected = o.value === btn.dataset.category);
});

// --- GUARDAR EDICION ---
document.getElementById('btnEditar')?.addEventListener('click', () => {
    const data = new FormData();
    data.append('id', document.getElementById('editarId').value);
    data.append('title', document.getElementById('editarTitle').value);
    data.append('category', document.getElementById('editarCategory').value);
    data.append('price', document.getElementById('editarPrice').value);
    data.append('image_url', document.getElementById('editarImage').value);

    fetch('../controllers/ServiceController.php?action=update', {
        method: 'POST',
        body: data
    })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                location.reload();
            } else {
                const el = document.getElementById('editarError');
                el.classList.remove('d-none');
                el.textContent = res.errors?.join(', ') ?? res.message;
            }
        });
});

// --- BORRAR SERVICIO ---
document.querySelectorAll('.btn-borrar').forEach(btn => {
    btn.addEventListener('click', () => {
        if (!confirm('¿Seguro que deseas eliminar este servicio?')) return;

        const data = new FormData();
        data.append('id', btn.dataset.id);

        fetch('../controllers/ServiceController.php?action=delete', {
            method: 'POST',
            body: data
        })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    document.getElementById('card-' + btn.dataset.id).remove();
                } else {
                    alert(res.message);
                }
            });
    });
});



// --- AGREGAR AL CARRITO ---
document.querySelectorAll('.btn-agregar-carrito').forEach(btn => {
    btn.addEventListener('click', () => {
        const data = new FormData();
        data.append('id', btn.dataset.id);
        data.append('title', btn.dataset.title);
        data.append('price', btn.dataset.price);
        data.append('image_url', btn.dataset.image_url);
        data.append('quantity', btn.dataset.quantity || 1);

        fetch('add-to-cart.php', {
            method: 'POST',
            body: data
        })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    const badge = document.querySelector('.navbar .badge');
                    if (badge) badge.textContent = res.totalItems;
                } else {
                    alert(res.message);
                }
            });
    });
});


// PARA EL CARRITO
document.addEventListener("DOMContentLoaded", () => {
    fetch("../views/cart.php")
        .then(res => res.json())
        .then(data => {
            document.getElementById("subtotal").textContent = "€ " + data.subtotal.toFixed(2);
            document.getElementById("discount").textContent = "€ " + data.discount.toFixed(2);
            document.getElementById("tax").textContent = "€ " + data.tax.toFixed(2);
            document.getElementById("total").textContent = "€ " + data.total.toFixed(2);
        })
        .catch(err => console.error("Error cargando resumen:", err));
});

