<div class="admin-stats">
    <div class="stat-card">
        <div class="stat-number"><?= $stats['paintings'] ?></div>
        <div class="stat-label">Tableaux disponibles</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $stats['sold'] ?></div>
        <div class="stat-label">Tableaux vendus</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $stats['orders'] ?></div>
        <div class="stat-label">Commandes</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= formatPrice($stats['revenue']) ?></div>
        <div class="stat-label">Chiffre d'affaires</div>
    </div>
</div>

<div class="admin-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <h2>Dernières commandes</h2>
        <a href="/admin/commandes" class="btn btn-sm btn-outline">Tout voir</a>
    </div>

    <?php if (empty($recentOrders)): ?>
        <p style="color: var(--text-light);">Aucune commande pour le moment.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Client</th>
                    <th>Total</th>
                    <th>Paiement</th>
                    <th>Statut</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentOrders as $order): ?>
                    <tr>
                        <td><a href="/admin/commandes/<?= $order['id'] ?>"><?= e($order['order_number']) ?></a></td>
                        <td><?= e($order['customer_firstname'] . ' ' . $order['customer_lastname']) ?></td>
                        <td><?= formatPrice($order['total']) ?></td>
                        <td><span class="badge badge-<?= $order['payment_status'] ?>"><?= e($order['payment_status']) ?></span></td>
                        <td><span class="badge badge-<?= $order['status'] ?>"><?= e($order['status']) ?></span></td>
                        <td><?= date('d/m/Y', strtotime($order['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
