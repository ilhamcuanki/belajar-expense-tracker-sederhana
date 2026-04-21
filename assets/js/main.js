document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('form.validate-on-submit');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let valid = true;
            this.querySelectorAll('input, select, textarea').forEach(input => {
                if (input.hasAttribute('required') && !input.value.trim()) {
                    input.classList.add('error-border');
                    valid = false;
                } else {
                    input.classList.remove('error-border');
                }
            });
            if (!valid) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi.');
            }
        });
    });

    // Auto-format input jumlah (opsional UX)
    const jumlahInputs = document.querySelectorAll('input[name="jumlah"]');
    jumlahInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9.,]/g, '');
        });
    });
});