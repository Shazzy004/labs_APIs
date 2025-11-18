document.addEventListener('DOMContentLoaded', function() {
    const frm = document.getElementById('frmProductos');
    const btnGuardar = document.getElementById('btnGuardar');
    const btnLimpiar = document.getElementById('btnLimpiar');

    // Función para listar todos los productos
    const listarProductos = async () => {
        const formData = new FormData();
        formData.append('accion', 'Listar');

        try {
            const response = await fetch('registrar.php', { method: 'POST', body: formData });
            const result = await response.json();

            if (result.success) {
                let html = '';
                result.data.forEach(prod => {
                    html += `
                        <tr>
                            <td>${prod.codigo}</td>
                            <td>${prod.producto}</td>
                            <td>${prod.precio}</td>
                            <td>${prod.cantidad}</td>
                            <td>
                                <button class="btn btn-sm btn-warning btn-editar" data-id="${prod.id}">Editar</button>
                            </td>
                        </tr>
                    `;
                });
                document.getElementById('listaProductos').innerHTML = html;
            } else {
                console.error('Error al listar:', result.message);
            }
        } catch (error) {
            console.error('Error de red:', error);
        }
    };

    // Evento para guardar o editar un producto
    frm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const productoId = document.getElementById('producto_id').value;
        const accion = productoId ? 'Editar' : 'Guardar';

        const formData = new FormData(frm);
        formData.append('accion', accion);

        try {
            const response = await fetch('registrar.php', { method: 'POST', body: formData });
            const result = await response.json();

            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: result.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                frm.reset();
                document.getElementById('producto_id').value = '';
                btnGuardar.textContent = 'Guardar';
                listarProductos(); // Recargamos la lista
            } else {
                let errorMessage = result.message;
                if (result.errors) {
                    errorMessage += '<br>' + Object.values(result.errors).join('<br>');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: errorMessage
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error de Conexión',
                text: 'No se pudo comunicar con el servidor.'
            });
        }
    });

    // Evento para limpiar el formulario
    btnLimpiar.addEventListener('click', () => {
        frm.reset();
        document.getElementById('producto_id').value = '';
        btnGuardar.textContent = 'Guardar';
    });

    // Evento para cargar datos en el formulario para editar (usando delegación de eventos)
    document.getElementById('listaProductos').addEventListener('click', async function(e) {
        if (e.target.classList.contains('btn-editar')) {
            const id = e.target.getAttribute('data-id');
            const formData = new FormData();
            formData.append('accion', 'Buscar');
            formData.append('id', id);

            const response = await fetch('registrar.php', { method: 'POST', body: formData });
            const result = await response.json();

            if (result.success) {
                document.getElementById('producto_id').value = result.data.id;
                document.getElementById('codigo').value = result.data.codigo;
                document.getElementById('producto').value = result.data.producto;
                document.getElementById('precio').value = result.data.precio;
                document.getElementById('cantidad').value = result.data.cantidad;
                btnGuardar.textContent = 'Actualizar';
                window.scrollTo(0, 0); // Lleva la vista al inicio de la página
            }
        }
    });

    // Cargar la lista de productos al iniciar la página
    listarProductos();
});