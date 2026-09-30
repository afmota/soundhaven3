document.addEventListener('DOMContentLoaded', () => {
    const inputCapaUrl = document.getElementById('inputCapaUrl');
    const imgPreview = document.getElementById('imgPreviewCapa');

    // Atualiza preview da capa
    inputCapaUrl.addEventListener('input', () => {
        imgPreview.src = inputCapaUrl.value || 'assets/images/placeholder.jpg';
    });

    // Lógica do diálogo da gravadora
    const btnNova = document.getElementById('btnNovaGravadora');
    const dialogo = document.getElementById('dialogoGravadora');
    
    if (btnNova) {
        btnNova.onclick = () => dialogo.style.display = 'flex';
    }

    document.getElementById('btnCancelarGravadora').onclick = () => {
        dialogo.style.display = 'none';
    };
});