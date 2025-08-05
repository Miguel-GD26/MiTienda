<script>
    // Listener para las notificaciones Toast con SweetAlert2
    // Este script se puede reutilizar en cualquier página que lo necesite.

    if (typeof Livewire !== 'undefined') {
        document.addEventListener('livewire:init', () => {
            Livewire.on('alert', (event) => {
                const alertData = event[0];
                if (!alertData) {
                    console.error('Alerta recibida sin datos.');
                    return;
                }

                const type = alertData.type;
                const message = alertData.message;

                // Mapa de colores para cada tipo de alerta
                const colorMap = {
                    success: '#28a745',
                    error:   '#dc3545',
                    warning: '#ffc107',
                    info:    '#0dcaf0',
                    question:'#6c757d'
                };

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);

                        const progressBar = Swal.getTimerProgressBar();
                        if (progressBar) {
                            progressBar.style.backgroundColor = colorMap[type] || colorMap.question;
                        }
                    }
                });

                Toast.fire({
                    icon: type,
                    title: message
                });
            });
        });
    } else {
        console.warn('Livewire no está definido. El listener de SweetAlert no se activará.');
    }
</script>