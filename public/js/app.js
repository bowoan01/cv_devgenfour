document.addEventListener('DOMContentLoaded', function () {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
        });
    }

    document.querySelectorAll('[data-confirm]').forEach(function (button) {
        button.addEventListener('click', function (event) {
            if (!confirm(button.getAttribute('data-confirm'))) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
    });
});

window.Devgenfour = {
    submitForm: function (form, table) {
        const formData = new FormData(form);
        const method = form.getAttribute('method') || 'POST';
        const action = form.getAttribute('action');

        return fetch(action, {
            method: method.toUpperCase(),
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(async response => {
                if (!response.ok) {
                    const error = await response.json();
                    throw error;
                }
                return response.json();
            })
            .then(data => {
                if (table) {
                    table.ajax.reload();
                }
                const offcanvas = bootstrap.Offcanvas.getInstance(document.querySelector('.offcanvas.show'));
                if (offcanvas) {
                    offcanvas.hide();
                }
                const modalEl = document.querySelector('.modal.show');
                if (modalEl) {
                    bootstrap.Modal.getInstance(modalEl).hide();
                }
                Devgenfour.flash(data.message || 'Saved successfully');
                form.reset();
                return data;
            })
            .catch(error => {
                Devgenfour.flash(error.message || 'Something went wrong', 'danger');
                throw error;
            });
    },
    flash: function (message, type = 'success') {
        const container = document.createElement('div');
        container.classList.add('container', 'mt-3');
        container.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`;
        document.body.prepend(container);
        setTimeout(() => container.remove(), 5000);
    }
};
