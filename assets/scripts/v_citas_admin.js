document.addEventListener('click', e => {

    // ===== MOSTRAR EDICIÓN (ADMIN) =====
    if (e.target.classList.contains('btn_toggle_edit')) {

        const card = e.target.closest('.cita_card');
        if (!card) return;

        // cerrar otros
        document.querySelectorAll('.admin_cita_edit').forEach(edit =>
            edit.classList.add('hidden')
        );
        document.querySelectorAll('.admin_cita_view').forEach(view =>
            view.classList.remove('hidden')
        );

        // abrir este
        
        card.querySelector('.admin_cita_edit').classList.remove('hidden');
    }

    // ===== CANCELAR EDICIÓN =====
    if (e.target.classList.contains('btn_cancel_edit')) {

        const card = e.target.closest('.cita_card');
        if (!card) return;

        card.querySelector('.admin_cita_edit').classList.add('hidden');
        
    }
});
