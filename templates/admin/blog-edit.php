<form method="POST" action="<?= isset($post) ? '/admin/blog/modifier/' . $post['id'] : '/admin/blog/ajouter' ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        <div>
            <div class="form-group">
                <label for="title">Titre</label>
                <input type="text" id="title" name="title" value="<?= e($post['title'] ?? '') ?>" required oninput="generateSlug(this.value)">
            </div>

            <div class="form-group">
                <label for="slug">Slug</label>
                <input type="text" id="slug" name="slug" value="<?= e($post['slug'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="excerpt">Extrait</label>
                <textarea id="excerpt" name="excerpt" rows="3"><?= e($post['excerpt'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="content">Contenu (HTML)</label>
                <textarea id="content" name="content" rows="20" style="font-family: monospace; font-size: 0.85rem;"><?= e($post['content'] ?? '') ?></textarea>
            </div>
        </div>

        <div>
            <div class="form-group">
                <label for="category_id">Catégorie</label>
                <select id="category_id" name="category_id">
                    <option value="">-- Aucune --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($post['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="image">Image</label>
                <?php if (!empty($post['image'])): ?>
                    <div style="margin-bottom: 8px;">
                        <img src="/uploads/<?= e($post['image']) ?>" alt="" style="max-width: 100%; border-radius: 4px;">
                    </div>
                <?php endif; ?>
                <input type="file" id="image" name="image" accept="image/*">
            </div>

            <div class="form-group">
                <label for="meta_description">Meta description (SEO)</label>
                <textarea id="meta_description" name="meta_description" rows="3" maxlength="320"><?= e($post['meta_description'] ?? '') ?></textarea>
                <small style="color: var(--text-light);">Max 320 caractères</small>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" name="published" value="1" <?= ($post['published'] ?? 0) ? 'checked' : '' ?>>
                    Publié
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;"><?= isset($post) ? 'Mettre à jour' : 'Créer l\'article' ?></button>

            <?php if (isset($post)): ?>
                <a href="/blog/<?= e($post['slug']) ?>" target="_blank" class="btn btn-outline" style="width: 100%; margin-top: 8px; text-align: center;">Voir l'article</a>
            <?php endif; ?>
        </div>
    </div>
</form>

<script>
function generateSlug(text) {
    <?php if (!isset($post)): ?>
    var slug = text.toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');
    document.getElementById('slug').value = slug;
    <?php endif; ?>
}
</script>
