<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <span><?= count($users) ?> utilisateur(s)</span>
</div>

<div class="admin-card">
    <h3>Ajouter un utilisateur</h3>
    <form method="POST" action="/admin/utilisateurs/ajouter" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
        <?= csrf_field() ?>
        <div class="form-group" style="flex: 1; min-width: 150px;">
            <label for="name">Nom</label>
            <input type="text" id="name" name="name" required placeholder="Prénom Nom">
        </div>
        <div class="form-group" style="flex: 1; min-width: 200px;">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="email@exemple.com">
        </div>
        <div class="form-group" style="flex: 1; min-width: 150px;">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required minlength="6" placeholder="Min. 6 caractères">
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
    </form>
</div>

<div class="admin-card">
    <h3>Utilisateurs existants</h3>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Créé le</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><strong><?= e($u['name']) ?></strong></td>
                    <td><?= e($u['email']) ?></td>
                    <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                    <td style="white-space: nowrap;">
                        <form method="POST" action="/admin/utilisateurs/password/<?= $u['id'] ?>" style="display: inline;">
                            <?= csrf_field() ?>
                            <input type="password" name="password" placeholder="Nouveau mdp" style="width: 120px; padding: 6px 8px; border: 1px solid var(--border); border-radius: var(--radius); font-size: 0.8rem;">
                            <button type="submit" class="btn btn-sm btn-outline">Changer</button>
                        </form>
                        <?php if ($u['id'] != ($_SESSION['admin_id'] ?? 0)): ?>
                            <form method="POST" action="/admin/utilisateurs/supprimer/<?= $u['id'] ?>" style="display: inline;" onsubmit="return confirm('Supprimer cet utilisateur ?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
