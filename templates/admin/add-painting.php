<form method="POST" action="/admin/tableaux/ajouter" enctype="multipart/form-data" class="admin-form">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="image">Photo du tableau *</label>
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" required>
        <div id="image-preview" class="image-preview"></div>
    </div>

    <div class="form-group">
        <label for="title">Titre *</label>
        <input type="text" id="title" name="title" required>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"></textarea>
        <button type="button" class="btn btn-sm btn-outline" style="margin-top: 8px;" onclick="generateDescription()">Générer avec l'IA</button>
    </div>

    <div class="form-group">
        <label for="price">Prix (EUR) *</label>
        <input type="number" id="price" name="price" step="0.01" min="0" required>
    </div>

    <div class="form-group">
        <label for="technique">Technique</label>
        <input type="text" id="technique" name="technique" placeholder="Ex: Huile sur toile, Acrylique...">
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="width_cm">Largeur (cm)</label>
            <input type="number" id="width_cm" name="width_cm" min="0">
        </div>
        <div class="form-group">
            <label for="height_cm">Hauteur (cm)</label>
            <input type="number" id="height_cm" name="height_cm" min="0">
        </div>
    </div>

    <div class="form-group">
        <label for="video">Vidéo (optionnel, max 100 Mo)</label>
        <input type="file" id="video" name="video" accept="video/mp4,video/quicktime,video/webm">
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="featured" value="1"> Mettre en vedette
        </label>
    </div>

    <button type="submit" class="btn btn-primary">Ajouter le tableau</button>
</form>
