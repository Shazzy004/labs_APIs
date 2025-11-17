document.addEventListener('DOMContentLoaded', function() {
    // --- Referencias a elementos del DOM ---
    const frmProductos = document.getElementById('frmProductos');
    const frmBuscar = document.getElementById('frmBuscar');
    const btnGuardar = document.getElementById('btnGuardar');
    const btnLimpiar = document.getElementById('btnLimpiar');
    const btnMostrarTodos = document.getElementById('btnMostrarTodos');
    const tbody = document.getElementById('listaProductos');

    // --- Función para renderizar la tabla ---
    // Centraliza la creación de las filas de la tabla para no repetir código.
    const renderTabla = (productos) => {
        let html = '';
        if (productos.length === 0) {
            html = '<tr><td colspan="5" class="text-center">No se encontraron productos.</td></tr>';
        } else {
            productos.forEach(prod => {
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
        }
        tbody.innerHTML = html;
    };

    // --- Funciones de Fetch ---
    const ejecutarFetch = async (formData) => {
        try {
            const response = await fetch('registrar.php', { method: 'POST', body: formData });
            return await response.json();
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Error de Conexión', text: 'No se pudo comunicar con el servidor.' });
        }
    };
    
    const listarProductos = async () => {
        const formData = new FormData();
        formData.append('accion', 'Listar');
        const result = await ejecutarFetch(formData);
        if (result && result.success) {
            renderTabla(result.data);
        }
    };

    // --- Lógica de Eventos ---

    // 1. Guardar o Editar un producto
    frmProductos.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const accion = document.getElementById('producto_id').value ? 'Editar' : 'Guardar';
        const formData = new FormData(frmProductos);
        formData.append('accion', accion);

        const result = await ejecutarFetch(formData);

        if (result && result.success) {
            // ¡IMPLEMENTACIÓN DEL SWITCH EN JAVASCRIPT!
            // Se utiliza la clave 'accion' devuelta por el PHP.
            switch (result.accion) {
                case 'Guardar':
                case 'Editar':
                    const titulo = result.accion === 'Guardar' ? '¡Registrado!' : '¡Actualizado!';
                    Swal.fire({ icon: 'success', title: titulo, text: result.message, showConfirmButton: false, timer: 1500 });
                    frmProductos.reset();
                    document.getElementById('producto_id').value = '';
                    btnGuardar.textContent = 'Guardar';
                    listarProductos(); // Recargamos la lista completa
                    break;
                default:
                    Swal.fire({ icon: 'info', title: 'Acción inesperada', text: 'La operación se completó, pero la respuesta no fue la esperada.' });
                    break;
            }
        } else if (result) {
            let errorMessage = result.message + (result.errors ? '<br>' + Object.values(result.errors).join('<br>') : '');
            Swal.fire({ icon: 'error', title: 'Error de Validación', html: errorMessage });
        }
    });

    // 2. Cargar datos para editar
    tbody.addEventListener('click', async function(e) {
        if (e.target.classList.contains('btn-editar')) {
            const id = e.target.getAttribute('data-id');
            const formData = new FormData();
            formData.append('accion', 'CargarDatos');
            formData.append('id', id);

            const result = await ejecutarFetch(formData);

            if (result && result.success) {
                document.getElementById('producto_id').value = result.data.id;
                document.getElementById('codigo').value = result.data.codigo;
                document.getElementById('producto').value = result.data.producto;
                document.getElementById('precio').value = result.data.precio;
                document.getElementById('cantidad').value = result.data.cantidad;
                btnGuardar.textContent = 'Actualizar';
                window.scrollTo(0, 0); // Lleva la vista al formulario
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron cargar los datos del producto.' });
            }
        }
    });
    
    // 3. Buscar productos
    frmBuscar.addEventListener('submit', async function(e) {
        e.preventDefault();
        const termino = document.getElementById('termino_busqueda').value;
        const formData = new FormData();
        formData.append('accion', 'Buscar');
        formData.append('termino', termino);

        const result = await ejecutarFetch(formData);
        if (result && result.success) {
            renderTabla(result.data);
        }
    });

    // 4. Limpiar formulario principal
    btnLimpiar.addEventListener('click', () => {
        frmProductos.reset();
        document.getElementById('producto_id').value = '';
        btnGuardar.textContent = 'Guardar';
    });

    // 5. Mostrar todos los productos (limpiar búsqueda)
    btnMostrarTodos.addEventListener('click', () => {
        document.getElementById('termino_busqueda').value = '';
        listarProductos();
    });

    // --- Carga inicial de datos ---
    listarProductos();
});