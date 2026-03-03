document.addEventListener('DOMContentLoaded', () => {

    const adminCitasContainer = document.getElementById('admin_citas_container');

    if (adminCitasContainer) {

        adminCitasContainer.addEventListener('click', e => {

            const btnEditar = e.target.closest('.btn_toggle_edit');
            if (btnEditar) {
                const card = btnEditar.closest('.admin_cita_card');

                document.querySelectorAll('.admin_cita_card')
                    .forEach(c => c.classList.remove('editing'));

                card.classList.add('editing');
                return;
            }

            const btnCancel = e.target.closest('.btn_cancel_edit');
            if (btnCancel) {
                const card = btnCancel.closest('.admin_cita_card');
                card.classList.remove('editing');
            }

        });
    }

    /* ===== CONFIRMAR BORRADO ===== */

    const deleteButtons = document.querySelectorAll(
        '.btn_delete[name="borrar_cita"]'
    );

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirm('¿Seguro que quieres borrar esta cita?')) {
                e.preventDefault();
            }
        });
    });

    /* ===== TOGGLE CITAS PASADAS ===== */

    const toggleBtn = document.getElementById('show_past_citas');
    const pastCitas = document.getElementById('past_citas');

    if (toggleBtn && pastCitas && adminCitasContainer) {

        toggleBtn.addEventListener('click', () => {

            const mostrandoPasadas = pastCitas.classList.contains('visible');

            if (!mostrandoPasadas) {
                pastCitas.classList.add('visible');
                adminCitasContainer.classList.add('hidden');
                toggleBtn.textContent = "Ver citas actuales";
            } else {
                pastCitas.classList.remove('visible');
                adminCitasContainer.classList.remove('hidden');
                toggleBtn.textContent = "Ver citas pasadas";
            }

        });
    }

});