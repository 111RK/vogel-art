<form method="POST" action="/admin/tableaux/modifier/<?= $painting['id'] ?>" enctype="multipart/form-data" class="admin-form">
    <?= csrf_field() ?>

    <div class="form-group">
        <label>Photo actuelle</label>
        <img src="/uploads/thumbs/<?= e($painting['image']) ?>" alt="" style="width: 200px; border-radius: 4px; margin-bottom: 8px;">
        <label for="image">Changer la photo</label>
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
        <div id="image-preview" class="image-preview"></div>
    </div>

    <div class="form-group">
        <label for="title">Titre *</label>
        <input type="text" id="title" name="title" value="<?= e($painting['title']) ?>" required>
    </div>

    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4"><?= e($painting['description']) ?></textarea>
        <button type="button" class="btn btn-sm btn-outline" style="margin-top: 8px;" onclick="generateDescription()">Générer avec l'IA</button>
    </div>

    <div class="form-group">
        <label for="price">Prix (EUR) *</label>
        <input type="number" id="price" name="price" step="0.01" min="0" value="<?= $painting['price'] ?>" required>
    </div>

    <div class="form-group">
        <label for="technique">Technique</label>
        <input type="text" id="technique" name="technique" value="<?= e($painting['technique'] ?? '') ?>">
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="width_cm">Largeur (cm)</label>
            <input type="number" id="width_cm" name="width_cm" min="0" value="<?= $painting['width_cm'] ?? '' ?>">
        </div>
        <div class="form-group">
            <label for="height_cm">Hauteur (cm)</label>
            <input type="number" id="height_cm" name="height_cm" min="0" value="<?= $painting['height_cm'] ?? '' ?>">
        </div>
    </div>

    <div class="form-group">
        <label for="status">Statut</label>
        <select id="status" name="status">
            <option value="available" <?= $painting['status'] === 'available' ? 'selected' : '' ?>>Disponible</option>
            <option value="sold" <?= $painting['status'] === 'sold' ? 'selected' : '' ?>>Vendu</option>
            <option value="hidden" <?= $painting['status'] === 'hidden' ? 'selected' : '' ?>>Masqué</option>
        </select>
    </div>

    <div class="form-group">
        <label>
            <input type="checkbox" name="featured" value="1" <?= $painting['featured'] ? 'checked' : '' ?>> Mettre en vedette
        </label>
    </div>

    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
</form>

<div class="admin-card" style="margin-top: 24px;">
    <h3>Photos supplémentaires</h3>
    <?php if (!empty($gallery)): ?>
        <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 16px;">
            <?php foreach ($gallery as $img): ?>
                <div style="position: relative;">
                    <img src="/uploads/thumbs/<?= e($img['image']) ?>" alt="" style="width: 120px; height: 120px; object-fit: cover; border-radius: 4px;">
                    <form method="POST" action="/admin/tableaux/<?= $painting['id'] ?>/photo-supprimer/<?= $img['id'] ?>" style="position: absolute; top: 4px; right: 4px;">
                        <?= csrf_field() ?>
                        <button type="submit" style="background: rgba(196,69,54,0.9); color: #fff; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; font-size: 14px; line-height: 1;" onclick="return confirm('Supprimer cette photo ?')">&times;</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="POST" action="/admin/tableaux/<?= $painting['id'] ?>/photos" enctype="multipart/form-data" style="display: flex; align-items: flex-end; gap: 12px;">
        <?= csrf_field() ?>
        <div class="form-group" style="flex: 1; margin-bottom: 0;">
            <label>Ajouter des photos</label>
            <input type="file" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple>
        </div>
        <button type="submit" class="btn btn-sm btn-primary">Uploader</button>
    </form>
</div>
