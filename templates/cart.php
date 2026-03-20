<section class="section">
    <div class="container">
        <h1 class="section-title">Panier</h1>

        <?php if (empty($items)): ?>
            <div class="empty-state">
                <h2>Votre panier est vide</h2>
                <p>Découvrez nos tableaux uniques</p>
                <a href="/boutique" class="btn btn-primary" style="margin-top: 16px;">Voir la boutique</a>
            </div>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th></th>
                        <th>Tableau</th>
                        <th>Prix</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <img src="/uploads/thumbs/<?= e($item['image']) ?>" alt="<?= e($item['title']) ?>" class="cart-item-image">
                            </td>
                            <td>
                                <a href="/tableau/<?= e($item['slug']) ?>"><strong><?= e($item['title']) ?></strong></a>
                                <?php if ($item['technique']): ?>
                                    <br><small><?= e($item['technique']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= formatPrice($item['price']) ?></td>
                            <td>
                                <form method="POST" action="/panier/supprimer">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="painting_id" value="<?= $item['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline">Retirer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-total">
                Total : <strong><?= formatPrice($total) ?></strong>
            </div>

            <div style="text-align: right;">
                <a href="/commande" class="btn btn-primary">Passer commande</a>
            </div>
        <?php endif; ?>
    </div>
</section>
