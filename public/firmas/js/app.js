document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('signature-pad');
    const wrapper = document.getElementById('signature-wrapper');
    const clearBtn = document.getElementById('clear-btn');
    const saveBtn = document.getElementById('save-btn');
    const alertBox = document.getElementById('alert');

    // Inicializar Signature Pad
    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)',
        penColor: 'rgb(0, 0, 0)',
        minWidth: 2,
        maxWidth: 4,
        throttle: 16
    });

    // Función para ajustar el tamaño del canvas al contenedor
    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = wrapper.offsetWidth * ratio;
        canvas.height = wrapper.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear(); // Limpiar al redimensionar para evitar desajustes
    }

    // Ajustar inicialmente y en cambio de tamaño
    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();

    // Botón Limpiar
    clearBtn.addEventListener('click', function() {
        signaturePad.clear();
        showAlert('', '');
    });

    // Botón Guardar
    saveBtn.addEventListener('click', function() {
        if (signaturePad.isEmpty()) {
            showAlert('Por favor, firme antes de guardar.', 'error');
            return;
        }

        const dataURL = signaturePad.toDataURL('image/png');
        
        // Deshabilitar botón durante el envío
        saveBtn.disabled = true;
        saveBtn.innerText = 'Guardando...';

        fetch('save.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `oni=${employeeONI}&image=${encodeURIComponent(dataURL)}`
        })
        .then(async response => {
            const text = await response.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error("Respuesta no válida del servidor: " + text);
            }
        })
        .then(data => {
            if (data.success) {
                showAlert('Firma guardada correctamente.', 'success');
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            } else {
                showAlert('Error del Servidor: ' + data.message, 'error');
                saveBtn.disabled = false;
                saveBtn.innerText = 'Guardar Firma';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('ERROR CRÍTICO: ' + error.message, 'error');
            saveBtn.disabled = false;
            saveBtn.innerText = 'Guardar Firma';
        });
    });

    function showAlert(message, type) {
        if (!message) {
            alertBox.style.display = 'none';
            return;
        }
        alertBox.innerText = message;
        alertBox.className = 'alert alert-' + type;
        alertBox.style.display = 'block';
    }
});
