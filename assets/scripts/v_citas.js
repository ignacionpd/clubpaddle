document.addEventListener('DOMContentLoaded', () => {

    /* ==========================
       VALIDACIÓN NUEVA CITA
    ========================== */

    const formCita = document.querySelector('#form_cita');

    if (formCita) {
        formCita.addEventListener('submit', e => {

            const fecha = formCita.fecha_cita.value;
            const hora = formCita.hora_cita.value;
            const motivo = formCita.motivo_cita.value.trim();

            let errores = [];

            if (!fecha) errores.push("Fecha obligatoria");

            if (!hora) {
                errores.push("Hora obligatoria");
            } else if (!/^(0[8-9]|1\d|2[0-3]):00$/.test(hora)) {
                errores.push("La hora debe estar entre las 08:00 y las 23:00");
            }

            if (motivo.length < 5) errores.push("Motivo muy corto");

            if (errores.length > 0) {
                e.preventDefault();
                alert(errores.join("\n"));
            }
        });
    }

    /* ==========================
       TOGGLE EDITAR CITA
    ========================== */

    document.querySelectorAll('.cita_card').forEach(card => {

        const btnEdit = card.querySelector('.btn_toggle_edit');
        const btnCancel = card.querySelector('.btn_cancel_edit');
        const view = card.querySelector('.cita_view');
        const edit = card.querySelector('.cita_edit');

        if (!btnEdit || !btnCancel || !view || !edit) return;

        btnEdit.addEventListener('click', () => {
            view.classList.add('hidden');
            edit.classList.add('active');
        });

        btnCancel.addEventListener('click', () => {
            edit.classList.remove('active');
            view.classList.remove('hidden');
        });
    });

    /* ==========================
       CONFIRMACIÓN BORRAR CITA
    ========================== */

    document.querySelectorAll('.form_delete_cita').forEach(form => {
        form.addEventListener('submit', e => {
            if (!confirm('¿Seguro que deseas eliminar esta cita?')) {
                e.preventDefault();
            }
        });
    });

    /* ==========================
        VALIDACIÓN ON BLUR
    ========================== */

    document.addEventListener('blur', e => {

        if (e.target.matches('textarea[name="motivo_cita"]')) {

            const textarea = e.target;
            const small = textarea.closest('.input_zone')
                .querySelector('.input_error');

            if (textarea.value.trim().length < 5) {
                small.textContent = "El motivo debe tener al menos 5 caracteres.";
                small.style.visibility = 'visible';
            } else {
                small.textContent = "";
                small.style.visibility = 'hidden';
            }
        }

    }, true);
});

