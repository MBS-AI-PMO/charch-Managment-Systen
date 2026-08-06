/**
 * Centered SweetAlert-style modal (Alpine store).
 * Usage: Alpine.store('alert').show('Profile updated successfully.', 'success')
 * Types: success | error | info
 */
export function registerAlertStore(Alpine) {
    Alpine.store('alert', {
        open: false,
        message: '',
        type: 'success',
        title: '',
        animKey: 0,

        show(message, type = 'success', title = null) {
            if (!message) return;

            this.message = String(message);
            this.type = type;
            this.title = title ?? (
                type === 'success' ? 'Success' :
                type === 'error' ? 'Something went wrong' :
                'Notice'
            );
            this.animKey = Date.now();
            this.open = true;
        },

        close() {
            this.open = false;
        },
    });

    window.swal = (message, type = 'success', title = null) => {
        Alpine.store('alert').show(message, type, title);
    };

    // Back-compat: old toast() calls open the same centered alert
    window.toast = (message, type = 'success') => {
        Alpine.store('alert').show(message, type);
    };
}
