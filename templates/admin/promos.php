<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <span><?= count($promos) ?> code(s) promo</span>
</div>

<div class="admin-card">
    <h3>Créer un code promo</h3>
    <form method="POST" action="/admin/promos/ajouter" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
        <?= csrf_field() ?>
        <div class="form-group" style="min-width: 120px;">
            <label>Code</label>
            <input type="text" name="code" required placeholder="EX: BIENVENUE20" style="text-transform: uppercase;">
        </div>
        <div class="form-group" style="min-width: 100px;">
            <label>Type</label>
            <select name="type">
                <option value="percent">% Réduction</option>
                <option value="fixed">EUR Montant fixe</option>
            </select>
        </div>
        <div class="form-group" style="width: 100px;">
            <label>Valeur</label>
            <input type="number" step="0.01" min="0" name="value" required placeholder="10">
        </div>
        <div class="form-group" style="width: 120px;">
            <label>Commande min.</label>
            <input type="number" step="0.01" min="0" name="min_order" value="0" placeholder="0">
        </div>
        <div class="form-group" style="width: 100px;">
            <label>Utilisations max</label>
            <input type="number" min="0" name="max_uses" placeholder="Illimité">
        </div>
        <div class="form-group" style="width: 140px;">
            <label>Expire le</label>
            <input type="date" name="expires_at">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Créer</button>
    </form>
</div>

<?php if (!empty($promos)): ?>
<div class="admin-card">
    <h3>Codes existants</h3>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Réduction</th>
                <th>Min. commande</th>
                <th>Utilisations</th>
                <th>Expire</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($promos as $p): ?>
                <tr>
                    <td><strong style="font-family: monospace; font-size: 1rem;"><?= e($p['code']) ?></strong></td>
                    <td>
                        <?php if ($p['type'] === 'percent'): ?>
                            <?= $p['value'] ?>%
                        <?php else: ?>
                            <?= formatPrice($p['value']) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $p['min_order'] > 0 ? formatPrice($p['min_order']) : '-' ?></td>
                    <td><?= $p['used_count'] ?> / <?= $p['max_uses'] ?: '∞' ?></td>
                    <td><?= $p['expires_at'] ? date('d/m/Y', strtotime($p['expires_at'])) : 'Jamais' ?></td>
                    <td>
                        <span class="badge <?= $p['active'] ? 'badge-available' : 'badge-sold' ?>"><?= $p['active'] ? 'Actif' : 'Inactif' ?></span>
                    </td>
                    <td style="white-space: nowrap;">
                        <form method="POST" action="/admin/promos/toggle/<?= $p['id'] ?>" style="display: inline;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline"><?= $p['active'] ? 'Désactiver' : 'Activer' ?></button>
                        </form>
                        <form method="POST" action="/admin/promos/supprimer/<?= $p['id'] ?>" style="display: inline;" onsubmit="return confirm('Supprimer ce code ?')">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
