document.addEventListener('DOMContentLoaded', function () {
    var imageInput = document.getElementById('image');
    if (imageInput) {
        imageInput.addEventListener('change', function (e) {
            var preview = document.getElementById('image-preview');
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function (ev) {
                    preview.innerHTML = '<img src="' + ev.target.result + '" alt="Preview">';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    var flashMessages = document.querySelectorAll('.flash');
    flashMessages.forEach(function (msg) {
        setTimeout(function () {
            msg.style.opacity = '0';
            msg.style.transition = 'opacity 0.3s';
            setTimeout(function () { msg.remove(); }, 300);
        }, 4000);
    });
});

function generateDescription() {
    var title = document.getElementById('title').value;
    var technique = document.getElementById('technique').value;
    var imageInput = document.getElementById('image');
    var descField = document.getElementById('description');
    var btn = event.target;

    if (!title) {
        alert('Veuillez renseigner un titre.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = 'Génération en cours... <span class="loading-spinner"></span>';

    var widthCm = document.getElementById('width_cm');
    var heightCm = document.getElementById('height_cm');

    var formData = new FormData();
    formData.append('title', title);
    formData.append('technique', technique);
    if (widthCm) formData.append('width_cm', widthCm.value);
    if (heightCm) formData.append('height_cm', heightCm.value);

    fetch('/admin/api/generate-description', {
        method: 'POST',
        body: formData
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        if (data.description) {
            descField.value = data.description;
        } else if (data.error) {
            alert(data.error);
        }
    })
    .catch(function () {
        alert('Erreur lors de la génération.');
    })
    .finally(function () {
        btn.disabled = false;
        btn.textContent = 'Générer avec l\'IA';
    });
}

function improveText(fieldId) {
    var field = document.getElementById(fieldId);
    var text = field.value;
    var btn = event.target;

    if (!text.trim()) {
        alert('Veuillez d\'abord saisir un texte.');
        return;
    }

    btn.disabled = true;
    btn.innerHTML = 'Amélioration en cours... <span class="loading-spinner"></span>';

    var formData = new FormData();
    formData.append('text', text);
    formData.append('field', fieldId);

    fetch('/admin/api/improve-text', {
        method: 'POST',
        body: formData
    })
    .then(function (res) { return res.json(); })
    .then(function (data) {
        if (data.text) {
            field.value = data.text;
        } else if (data.error) {
            alert(data.error);
        }
    })
    .catch(function () {
        alert('Erreur lors de l\'amélioration.');
    })
    .finally(function () {
        btn.disabled = false;
        btn.textContent = 'Améliorer avec l\'IA';
    });
}
