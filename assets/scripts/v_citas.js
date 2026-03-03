document.addEventListener('DOMContentLoaded', () => {

    /* =========================================
       VALIDACIÓN NUEVA CITA
    ========================================== */

    const formCita = document.querySelector('#form_cita');

    if (formCita) {
        formCita.addEventListener('submit', e => {

            const fecha = formCita.fecha_cita.value;
            const hora = formCita.hora_cita.value;
            const motivo = formCita.motivo_cita.value.trim();

            let errores = [];

            if (!fecha) {
                errores.push("- Fecha obligatoria");
            } else {
                const hoy = new Date();
                hoy.setHours(0, 0, 0, 0);
                const fechaSeleccionada = new Date(fecha + "T00:00:00");

                if (fechaSeleccionada < hoy) {
                    errores.push("- No puedes seleccionar una fecha pasada");
                }
            }

            if (!hora) {
                errores.push("- Hora obligatoria");
            } else if (!/^(0[8-9]|1\d|2[0-3]):00$/.test(hora)) {
                errores.push("- La hora debe estar entre las 08:00 y las 23:00");
            }

            if (motivo.length < 5) {
                errores.push("- El motivo debe tener al menos 5 caracteres");
            }

            if (errores.length > 0) {
                e.preventDefault();
                alert(errores.join("\n"));
            }
        });
    }

    /* =========================================
       VALIDACIÓN MODIFICAR CITA
    ========================================= */

    document.querySelectorAll('.form_modificar_cita')
        .forEach(form => {

            form.addEventListener('submit', function (e) {

                const fechaInput = form.querySelector('input[name="modif_fecha"]');
                const motivoArea = form.querySelector('textarea[name="modif_motivo"]');

                let errores = [];

                /* ===== LIMPIAR ERRORES PREVIOS ===== */

                form.querySelectorAll('.small_error').forEach(s => {
                    s.textContent = '';
                    s.style.visibility = 'hidden';
                });

                form.querySelectorAll('.field_error').forEach(f => {
                    f.classList.remove('field_error');
                });

                /* ===== VALIDACIONES ===== */

                const fecha = fechaInput.value.trim();
                const motivo = motivoArea.value.trim();

                const hoy = new Date().toISOString().split("T")[0];

                if (!fecha) {
                    errores.push("- Fecha obligatoria");
                    mostrarError(fechaInput, "Fecha obligatoria");
                } else if (fecha < hoy) {
                    errores.push("- No puedes seleccionar una fecha pasada");
                    mostrarError(fechaInput, "Fecha pasada no permitida");
                }

                if (motivo.length < 5) {
                    errores.push("- El motivo debe tener al menos 5 caracteres");
                    mostrarError(motivoArea, "Mínimo 5 caracteres");
                }

                if (errores.length > 0) {
                    e.preventDefault();
                    alert(errores.join("\n"));
                }

            });

        });
    /* =========================================
       VALIDACIÓN EN TIEMPO REAL
    ========================================= */

    document.addEventListener('input', validarCampoTiempoReal);
    document.addEventListener('change', validarCampoTiempoReal);

    function validarCampoTiempoReal(e) {

        const zone = e.target.closest('.modify_zone');
        if (!zone) return;

        const field = e.target;
        const small = zone.querySelector('.small_error');

        let error = "";

        /* ===== VALIDAR SEGÚN CAMPO ===== */

        if (field.name === "modif_fecha") {

            if (!field.value) {
                error = "Fecha obligatoria";
            } else {
                const hoy = new Date();
                hoy.setHours(0, 0, 0, 0);

                const fechaSeleccionada = new Date(field.value + "T00:00:00");

                if (fechaSeleccionada < hoy) {
                    error = "Fecha pasada no permitida";
                }
            }

        }

        if (field.name === "modif_motivo") {

            if (field.value.trim().length < 5) {
                error = "Mínimo 5 caracteres";
            }

        }

        /* ===== APLICAR RESULTADO ===== */

        if (error) {
            field.classList.add('field_error');
            small.textContent = error;
            small.style.visibility = 'visible';
        } else {
            field.classList.remove('field_error');
            small.textContent = '';
            small.style.visibility = 'hidden';
        }

    }

    /* =========================================
       SLIDER EDITAR
    ========================================== */

    const citasContainer = document.getElementById('citas_container');

    if (citasContainer) {

        citasContainer.addEventListener('click', e => {

            const btnEditar = e.target.closest('.btn_toggle_edit');
            if (btnEditar) {
                const card = btnEditar.closest('.cita_card');

                document.querySelectorAll('.cita_card')
                    .forEach(c => c.classList.remove('editing'));

                card.classList.add('editing');
            }

            const btnCancel = e.target.closest('.btn_cancel_edit');
            if (btnCancel) {
                const card = btnCancel.closest('.cita_card');
                card.classList.remove('editing');
            }

        });
    }

    /* =========================================
       CONFIRMAR BORRADO
    ========================================== */

    document.querySelectorAll('.btn_delete[name="borrar_cita"]')
        .forEach(btn => {
            btn.addEventListener('click', function (e) {
                if (!confirm('¿Seguro que quieres borrar esta cita?')) {
                    e.preventDefault();
                }
            });
        });

    /* =========================================
       TOGGLE CITAS PASADAS
    ========================================== */

    const toggleBtn = document.getElementById('show_past_citas');
    const pastCitas = document.getElementById('past_citas');

    if (toggleBtn && pastCitas && citasContainer) {

        toggleBtn.addEventListener('click', () => {

            const mostrandoPasadas = pastCitas.classList.contains('visible');

            if (!mostrandoPasadas) {
                pastCitas.classList.add('visible');
                citasContainer.classList.add('hidden');
                toggleBtn.textContent = "Ver citas actuales";
            } else {
                pastCitas.classList.remove('visible');
                citasContainer.classList.remove('hidden');
                toggleBtn.textContent = "Ver citas pasadas";
            }

        });
    }

});