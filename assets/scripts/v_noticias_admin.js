
/**************** FORM CREAR NOTICIA  *******************************/

document.addEventListener('DOMContentLoaded', () => {

    const formCrear = document.getElementById('form_crear_noticia');
    if (!formCrear) return;

    const titulo = document.getElementById('titulo');
    const texto = document.getElementById('texto');
    const imagen = document.getElementById('imagen');
    const preview = document.getElementById('preview_imagen');

    /* ===== PREVIEW IMAGEN ===== */
    imagen.addEventListener('change', () => {

        if (!imagen.files || !imagen.files[0]) {
            preview.classList.add('hidden');
            return;
        }

        const file = imagen.files[0];

        if (!file.type.startsWith('image/')) {
            alert('El archivo debe ser una imagen');
            imagen.value = '';
            preview.classList.add('hidden');
            return;
        }

        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    });

    /* ===== VALIDACIÓN SUBMIT ===== */
    formCrear.addEventListener('submit', e => {

        let errores = false;

        if (titulo.value.trim().length < 5) {
            mostrarError(titulo, 'El título debe tener al menos 5 caracteres');
            errores = true;
        }

        if (texto.value.trim().length < 10) {
            mostrarError(texto, 'El contenido es demasiado corto');
            errores = true;
        }

        if (!imagen.files.length) {
            mostrarError(imagen, 'La imagen es obligatoria');
            errores = true;
        }

        if (errores) {
            e.preventDefault();
        }
    });

    function mostrarError(input, mensaje) {
        const small = input.nextElementSibling;
        if (!small) return;
        small.textContent = mensaje;
        small.style.visibility = 'visible';
    }
});






/**********  lISTAS DE NOTICIAS  **********/

const noticiasContainer = document.getElementById('admin_noticias_container');

noticiasContainer.addEventListener('click', e => {

    /* ===== MOSTRAR EDICIÓN ===== */
    const btnEditar = e.target.closest('.btn_edit');
    if (btnEditar) {

        const card = btnEditar.closest('.admin_noticia_card');
        if (!card) return;

        // Cerrar otras ediciones abiertas
        document
            .querySelectorAll('.admin_noticia_edit:not(.hidden)')
            .forEach(el => el.classList.add('hidden'));

        const edit = card.querySelector('.admin_noticia_edit');
        if (!edit) return;

        edit.classList.remove('hidden');
        return;
    }

    /* ===== CANCELAR EDICIÓN ===== */
    const btnCancel = e.target.closest('.btn_cancel_edit');
    if (btnCancel) {

        const card = btnCancel.closest('.admin_noticia_card');
        if (!card) return;

        const edit = card.querySelector('.admin_noticia_edit');
        if (!edit) return;

        edit.classList.add('hidden');
        return;
    }

    /* ===== CONFIRMAR BORRADO ===== */
    const btnDelete = e.target.closest('.btn_delete');
    if (btnDelete) {

        if (!confirm("¿Seguro que deseas borrar esta noticia?")) {
            e.preventDefault();
        }
    }
});

