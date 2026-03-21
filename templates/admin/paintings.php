<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <span><?= count($paintings) ?> tableau(x)</span>
    <div style="display: flex; gap: 8px;">
        <?php if (!empty(ILOVEIMG_PUBLIC_KEY) && count($paintings) > 0): ?>
            <button type="button" class="btn btn-sm btn-outline" onclick="upscaleAll()">Upscale toutes les images</button>
        <?php endif; ?>
        <a href="/admin/tableaux/ajouter" class="btn btn-primary">Ajouter un tableau</a>
    </div>
</div>

<div id="upscale-progress" style="display:none; margin-bottom: 16px;"></div>

<?php if (empty($paintings)): ?>
    <div class="empty-state">
        <h2>Aucun tableau</h2>
        <p>Commencez par ajouter votre première création.</p>
    </div>
<?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th></th>
                <th>Titre</th>
                <th>Prix</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($paintings as $p): ?>
                <tr id="row-<?= $p['id'] ?>">
                    <td><img src="/uploads/thumbs/<?= e($p['image']) ?>" alt="" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;"></td>
                    <td>
                        <strong><?= e($p['title']) ?></strong>
                        <?php if ($p['featured']): ?><br><small style="color: var(--gold);">En vedette</small><?php endif; ?>
                    </td>
                    <td><?= formatPrice($p['price']) ?></td>
                    <td>
                        <?php if ($p['status'] === 'available'): ?>
                            <span class="badge badge-available">Disponible</span>
                        <?php elseif ($p['status'] === 'sold'): ?>
                            <span class="badge badge-sold">Vendu</span>
                        <?php else: ?>
                            <span class="badge">Masqué</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
                    <td style="white-space: nowrap;">
                        <a href="/admin/tableaux/modifier/<?= $p['id'] ?>" class="btn btn-sm btn-outline">Modifier</a>
                        <?php if (!empty(ILOVEIMG_PUBLIC_KEY)): ?>
                            <button type="button" class="btn btn-sm btn-outline" onclick="upscaleOne(<?= $p['id'] ?>, this)" title="Améliorer l'image">HD</button>
                        <?php endif; ?>
                        <form method="POST" action="/admin/tableaux/supprimer/<?= $p['id'] ?>" style="display: inline;" onsubmit="return confirm('Supprimer ce tableau ?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<script>
var paintingIds = <?= json_encode(array_column($paintings, 'id')) ?>;

function upscaleOne(id, btn) {
    btn.disabled = true;
    btn.innerHTML = '<span class="loading-spinner"></span>';

    var formData = new FormData();
    formData.append('painting_id', id);

    fetch('/admin/api/upscale-image', { method: 'POST', body: formData })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                btn.textContent = 'OK';
                btn.style.color = 'var(--success)';
                var img = document.querySelector('#row-' + id + ' img');
                if (img) img.src = img.src.split('?')[0] + '?v=' + Date.now();
            } else {
                btn.textContent = 'Err';
                btn.style.color = 'var(--error)';
                alert(data.error);
            }
        })
        .catch(function() {
            btn.textContent = 'Err';
            btn.style.color = 'var(--error)';
        });
}

function upscaleAll() {
    if (!confirm('Upscaler toutes les images ? Cela peut prendre plusieurs minutes.')) return;

    var progress = document.getElementById('upscale-progress');
    progress.style.display = 'block';
    var total = paintingIds.length;
    var done = 0;
    var errors = 0;

    function next() {
        if (done + errors >= total) {
            progress.innerHTML = '<div class="flash flash-success">Terminé : ' + done + '/' + total + ' images améliorées.' + (errors > 0 ? ' (' + errors + ' erreur(s))' : '') + '</div>';
            return;
        }
        var id = paintingIds[done + errors];
        progress.innerHTML = '<div class="flash flash-info">Traitement en cours : ' + (done + errors + 1) + '/' + total + '... <span class="loading-spinner"></span></div>';

        var formData = new FormData();
        formData.append('painting_id', id);

        fetch('/admin/api/upscale-image', { method: 'POST', body: formData })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    done++;
                    var btn = document.querySelector('#row-' + id + ' button[onclick*="upscaleOne"]');
                    if (btn) { btn.textContent = 'OK'; btn.style.color = 'var(--success)'; }
                } else {
                    errors++;
                }
                next();
            })
            .catch(function() { errors++; next(); });
    }
    next();
}
</script>
