<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <span><?= count($posts) ?> article(s)</span>
    <a href="/admin/blog/ajouter" class="btn btn-primary">Ajouter un article</a>
</div>

<?php if (empty($posts)): ?>
    <div class="empty-state">
        <h2>Aucun article</h2>
        <p>Commencez par rédiger votre premier article de blog.</p>
    </div>
<?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th></th>
                <th>Titre</th>
                <th>Catégorie</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $p): ?>
                <tr>
                    <td>
                        <?php if ($p['image']): ?>
                            <img src="/uploads/<?= e($p['image']) ?>" alt="" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                        <?php else: ?>
                            <div style="width: 60px; height: 60px; background: var(--bg-warm); border-radius: 4px;"></div>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= e($p['title']) ?></strong></td>
                    <td><?= e($p['category_name'] ?? '-') ?></td>
                    <td>
                        <?php if ($p['published']): ?>
                            <span class="badge badge-available">Publié</span>
                        <?php else: ?>
                            <span class="badge">Brouillon</span>
                        <?php endif; ?>
                    </td>
                    <td><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
                    <td style="white-space: nowrap;">
                        <a href="/admin/blog/modifier/<?= $p['id'] ?>" class="btn btn-sm btn-outline">Modifier</a>
                        <form method="POST" action="/admin/blog/supprimer/<?= $p['id'] ?>" style="display: inline;" onsubmit="return confirm('Supprimer cet article ?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
