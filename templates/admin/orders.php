<?php
$cancelledCount = 0;
foreach ($orders as $o) if ($o['status'] === 'cancelled') $cancelledCount++;
?>
<?php if ($cancelledCount > 0): ?>
    <div style="margin-bottom: 16px; text-align: right;">
        <form method="POST" action="/admin/commandes/purger" style="display: inline;" onsubmit="return confirm('Supprimer définitivement les <?= $cancelledCount ?> commande(s) annulée(s) ?')">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-sm btn-danger">Supprimer les commandes annulées (<?= $cancelledCount ?>)</button>
        </form>
    </div>
<?php endif; ?>

<?php if (empty($orders)): ?>
    <div class="empty-state">
        <h2>Aucune commande</h2>
        <p>Les commandes apparaîtront ici.</p>
    </div>
<?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>N°</th>
                <th>Client</th>
                <th>Total</th>
                <th>Paiement</th>
                <th>Statut</th>
                <th>Mode</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= e($order['order_number']) ?></td>
                    <td><?= e($order['customer_firstname'] . ' ' . $order['customer_lastname']) ?></td>
                    <td><?= formatPrice($order['total']) ?></td>
                    <td><span class="badge badge-<?= $order['payment_status'] ?>"><?= statusLabel($order['payment_status']) ?></span></td>
                    <td><span class="badge badge-<?= $order['status'] ?>"><?= statusLabel($order['status']) ?></span></td>
                    <td><?= statusLabel($order['payment_method']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                    <td><a href="/admin/commandes/<?= $order['id'] ?>" class="btn btn-sm btn-outline">Détails</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
