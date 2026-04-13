

document.addEventListener('click', function(e) {
    const btn = e.target.closest('.btn-agregar');
    if (!btn) return;
    const id = btn.dataset.id;
    btn.disabled = true;
    btn.textContent = 'Agregando...';

    fetch('../public/add-to-cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id  // Le mandamos el ID del servicio
    })
    .then(function(response) {
        return response.json(); // Convertimos la respuesta a JSON
    })
    .then(function(data) {
        if (data.success) {
            const badge = document.getElementById('cart-count');
            if (badge) badge.textContent = data.totalItems;
            mostrarAlerta('Servicio agregado al carrito', 'success');
        } else {
            mostrarAlerta(data.message, 'danger');
        }
    })
    .catch(function(error) {
        mostrarAlerta('Error al agregar el servicio', 'danger');
    })
    .finally(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-cart-plus me-1"></i> Agregar al carrito';
    });
});

// ── ACTUALIZAR CANTIDAD 

function actualizarCantidad(id, cantidad) {
    fetch('../public/update-cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id + '&quantity=' + cantidad
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            actualizarTotales(data);
        } else {
            mostrarAlerta(data.message, 'danger');
        }
    })
    .catch(function() {
        mostrarAlerta('Error al actualizar la cantidad', 'danger');
    });
}

// ── ELIMINAR DEL CARRITO 
function eliminarDelCarrito(id) {
    fetch('../public/remove-from-cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            const fila = document.getElementById('fila-' + id);
            if (fila) fila.remove();
            actualizarTotales(data);

            if (data.totalItems === 0) {
                location.reload();
            }
        }
    })
    .catch(function() {
        mostrarAlerta('Error al eliminar el servicio', 'danger');
    });
}

// ── ACTUALIZAR TOTALES EN PANTALLA 
function actualizarTotales(data) {
    const badge = document.getElementById('cart-count');
    if (badge) badge.textContent = data.totalItems;

    const elSubtotal  = document.getElementById('subtotal');
    const elDescuento = document.getElementById('descuento');
    const elIva       = document.getElementById('iva');
    const elTotal     = document.getElementById('total');

    if (elSubtotal)  elSubtotal.textContent  = '$' + data.subtotal;
    if (elDescuento) elDescuento.textContent = '$' + data.descuento;
    if (elIva)       elIva.textContent       = '$' + data.iva;
    if (elTotal)     elTotal.textContent     = '$' + data.total;
}

function mostrarAlerta(mensaje, tipo) {
    const alerta = document.getElementById('alert-msg');
    if (!alerta) return;

    alerta.className = 'alert alert-' + tipo; // alert-success o alert-danger
    alerta.textContent = mensaje;
    alerta.classList.remove('d-none'); // Lo hacemos visible
    setTimeout(function() {
        alerta.classList.add('d-none');
    }, 3000);
}