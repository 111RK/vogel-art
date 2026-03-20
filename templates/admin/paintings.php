<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <span><?= count($paintings) ?> tableau(x)</span>
    <a href="/admin/tableaux/ajouter" class="btn btn-primary">Ajouter un tableau</a>
</div>

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
                <tr>
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
                    <td>
                        <a href="/admin/tableaux/modifier/<?= $p['id'] ?>" class="btn btn-sm btn-outline">Modifier</a>
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
