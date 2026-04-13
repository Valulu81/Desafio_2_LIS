// ── AGREGAR AL CARRITO (desde services.php)
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-agregar');
    if (!btn) return;

    const id = btn.dataset.id;
    btn.disabled = true;
    btn.innerHTML = 'Agregando...';

    fetch('../public/add-to-cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const badge = document.getElementById('cart-count');
                if (badge) badge.textContent = data.totalItems;
                mostrarAlerta('Servicio agregado al carrito', 'success');
            } else {
                mostrarAlerta(data.message, 'danger');
            }
        })
        .catch(() => mostrarAlerta('Error al agregar el servicio', 'danger'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-cart-plus me-1"></i> Agregar al carrito';
        });
});

// ── ACTUALIZAR CANTIDAD
function actualizarCantidad(id, nuevaCantidad) {
    // validar rango sin llamar al servidor
    if (nuevaCantidad < 1 || nuevaCantidad > 10) return;

    fetch('../public/update-cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id + '&quantity=' + nuevaCantidad
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // actualiza el span de cantidad
                const qtySpan = document.getElementById('qty-' + id);
                if (qtySpan) qtySpan.textContent = nuevaCantidad;

                // actualiza botones con la nueva cantidad
                const fila = document.getElementById('fila-' + id);
                if (fila) {
                    const btns = fila.querySelectorAll('button');
                    btns[0].setAttribute('onclick', `actualizarCantidad(${id}, ${nuevaCantidad - 1})`);
                    btns[1].setAttribute('onclick', `actualizarCantidad(${id}, ${nuevaCantidad + 1})`);
                }

                actualizarTotales(data);
            } else {
                mostrarAlerta(data.message, 'danger');
            }
        })
        .catch(() => mostrarAlerta('Error al actualizar la cantidad', 'danger'));
}

// ── ELIMINAR DEL CARRITO
function eliminarDelCarrito(id) {
    if (!confirm('¿Eliminar este servicio del carrito?')) return;

    const fila = document.getElementById('fila-' + id);
    if (!fila) return; // si ya no existe, no hace nada

    // deshabilita todos los botones de esa fila para evitar doble click
    fila.querySelectorAll('button').forEach(b => b.disabled = true);

    fetch('../public/remove-from-cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                fila.remove();
                actualizarTotales(data);
                if (data.totalItems === 0) location.reload();
            } else {
                mostrarAlerta(data.message ?? 'Error al eliminar', 'danger');
                fila.querySelectorAll('button').forEach(b => b.disabled = false);
            }
        })
        .catch(() => {
            mostrarAlerta('Error al eliminar el servicio', 'danger');
            fila.querySelectorAll('button').forEach(b => b.disabled = false);
        });
}

// ── VACIAR CARRITO
function vaciarCarrito() {
    if (!confirm('¿Vaciar todo el carrito?')) return;

    // eliminamos uno por uno usando remove-from-cart en cascada
    const filas = document.querySelectorAll('[id^="fila-"]');
    const ids = [...filas].map(f => f.id.replace('fila-', ''));

    const promesas = ids.map(id =>
        fetch('../public/remove-from-cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + id
        })
    );

    Promise.all(promesas).then(() => location.reload());
}

// ── COTIZAR
function procesarCotizacion() {
    fetch('../public/process-quote.php', { method: 'POST' })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                mostrarAlerta('¡Cotización creada! Código: ' + data.codigo, 'success');
                setTimeout(() => {
                    location.href = '../public/index.php?action=quotes';
                }, 2000);
            } else {
                mostrarAlerta(data.message, 'danger');
            }
        })
        .catch(() => mostrarAlerta('Error al procesar la cotización', 'danger'));
}

// ── ACTUALIZAR TOTALES EN PANTALLA
function actualizarTotales(data) {
    const badge = document.getElementById('cart-count');
    if (badge) badge.textContent = data.totalItems;

    const countText = document.getElementById('cart-count-text');
    if (countText) countText.textContent = data.totalItems + ' items';

    const elSubtotal = document.getElementById('subtotal');
    const elDescuento = document.getElementById('descuento');
    const elIva = document.getElementById('iva');
    const elTotal = document.getElementById('total');

    if (elSubtotal) elSubtotal.textContent = '$' + data.subtotal;
    if (elDescuento) elDescuento.textContent = '-$' + data.descuento;
    if (elIva) elIva.textContent = '$' + data.iva;
    if (elTotal) elTotal.textContent = '$' + data.total;
}

// ── MOSTRAR ALERTA
function mostrarAlerta(mensaje, tipo) {
    const alerta = document.getElementById('alert-msg');
    if (!alerta) return;

    alerta.className = 'alert alert-' + tipo;
    alerta.textContent = mensaje;
    alerta.classList.remove('d-none');

    setTimeout(() => alerta.classList.add('d-none'), 3000);
}

function eliminarDelCarrito(id) {
    if (!confirm('¿Eliminar este servicio del carrito?')) return;

    const fila = document.getElementById('fila-' + id);
    if (!fila) return; // si ya no existe, no hace nada

    // deshabilita todos los botones de esa fila para evitar doble click
    fila.querySelectorAll('button').forEach(b => b.disabled = true);

    fetch('../public/remove-from-cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                fila.remove();
                actualizarTotales(data);
                if (data.totalItems === 0) location.reload();
            } else {
                mostrarAlerta(data.message ?? 'Error al eliminar', 'danger');
                fila.querySelectorAll('button').forEach(b => b.disabled = false);
            }
        })
        .catch(() => {
            mostrarAlerta('Error al eliminar el servicio', 'danger');
            fila.querySelectorAll('button').forEach(b => b.disabled = false);
        });
}
function vaciarCarrito() {
    if (!confirm('¿Vaciar todo el carrito?')) return;

    const filas = document.querySelectorAll('[id^="fila-"]');
    const ids = [...filas].map(f => f.id.replace('fila-', ''));

    if (ids.length === 0) return;

    Promise.all(
        ids.map(id =>
            fetch('../public/remove-from-cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + id
            }).then(r => r.json())
        )
    ).then(() => location.reload())
        .catch(() => mostrarAlerta('Error al vaciar el carrito', 'danger'));
}