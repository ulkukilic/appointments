function deleteData(selector = '.delete-btn', onSuccessCallback = null) {
    document.querySelectorAll(selector).forEach(button => {
        button.addEventListener('click', function () {
            const url = this.dataset.url;
            const row = this.closest('tr');

            Swal.fire({
                title: window.translations.deleteConfirmTitle,
                text: window.translations.deleteConfirmText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: window.translations.deleteConfirmYes,
                cancelButtonText: window.translations.deleteConfirmNo
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire(window.translations.deleteSuccess, data.message, 'success');
                            row.remove();
                            if (typeof onSuccessCallback == 'function') {
                                onSuccessCallback();
                            }
                        } else {
                            Swal.fire(window.translations.deleteError, data.message, 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire(window.translations.deleteError, window.translations.deleteErrorMessage, 'error');
                    });
                }
            });


        });
    });
}

function statusToggle(url,selector = '.status-toggle') {
    document.querySelectorAll(selector).forEach(el => {
        el.addEventListener('click', function () {
            const token = this.dataset.token;
            const badge = this;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ token })
            })
            .then(res => res.json())
            .then(data => {


                if (data.status === 'success') {

                    const cssClass = data.newStatus === 1 ? 'bg-success' : 'bg-danger';


                    badge.classList.remove('bg-success', 'bg-danger');
                    badge.classList.add(cssClass);
                } else {
                    Swal.fire('Hata', data.message, 'error');
                }


            })
            .catch(() => {
                Swal.fire('Error', 'An error occurred during processing.', 'error');
            });
        });
    });
}
