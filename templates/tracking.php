<section class="section">
    <div class="container" style="max-width: 700px; margin: 0 auto;">
        <h1 class="section-title">Suivi de commande</h1>

        <?php if (!$order): ?>
            <div class="order-summary" style="padding: 32px;">
                <p style="text-align: center; color: var(--text-light); margin-bottom: 24px;">Entrez votre email et numéro de commande pour suivre votre colis.</p>
                <form method="POST" action="/suivi">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label for="email">Adresse email</label>
                        <input type="email" id="email" name="email" required placeholder="L'email utilisé lors de la commande">
                    </div>
                    <div class="form-group">
                        <label for="order_number">Numéro de commande</label>
                        <input type="text" id="order_number" name="order_number" required placeholder="Ex: VA-20260321-XXXXX">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Rechercher ma commande</button>
                </form>
            </div>

        <?php else: ?>
            <div class="order-summary" style="margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="margin: 0;">Commande <?= e($order['order_number']) ?></h3>
                    <span style="font-size: 0.85rem; color: var(--text-light);"><?= date('d/m/Y', strtotime($order['created_at'])) ?></span>
                </div>

                <?php
                $steps = ['pending' => 'En attente', 'confirmed' => 'Confirmée', 'shipped' => 'Expédiée', 'delivered' => 'Livrée'];
                $currentStep = $order['status'];
                $isCancelled = in_array($currentStep, ['cancelled']);
                $stepKeys = array_keys($steps);
                $currentIndex = array_search($currentStep, $stepKeys);
                if ($currentIndex === false) $currentIndex = -1;
                ?>

                <?php if ($isCancelled): ?>
                    <div style="background: #FBE9E7; border-radius: var(--radius); padding: 16px; text-align: center; margin-bottom: 16px;">
                        <p style="color: var(--error); font-weight: 600; margin: 0;">Commande annulée</p>
                    </div>
                <?php else: ?>
                    <div style="display: flex; justify-content: space-between; margin: 24px 0; position: relative;">
                        <div style="position: absolute; top: 14px; left: 10%; right: 10%; height: 3px; background: var(--border); z-index: 0;"></div>
                        <div style="position: absolute; top: 14px; left: 10%; height: 3px; background: var(--gold); z-index: 1; width: <?= min(100, max(0, $currentIndex / (count($steps) - 1) * 80)) ?>%;"></div>
                        <?php foreach ($steps as $key => $label): ?>
                            <?php
                            $idx = array_search($key, $stepKeys);
                            $done = $idx <= $currentIndex;
                            $active = $key === $currentStep;
                            ?>
                            <div style="text-align: center; z-index: 2; flex: 1;">
                                <div style="width: 30px; height: 30px; border-radius: 50%; margin: 0 auto 6px; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 700; <?= $done ? 'background:var(--gold);color:#fff;' : 'background:var(--border);color:var(--text-light);' ?>">
                                    <?= $done ? '&#10003;' : ($idx + 1) ?>
                                </div>
                                <span style="font-size: 0.7rem; color: <?= $active ? 'var(--gold-dark)' : 'var(--text-light)' ?>; font-weight: <?= $active ? '600' : '400' ?>;"><?= $label ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin: 20px 0;">
                    <div>
                        <p style="font-size: 0.8rem; color: var(--text-light); margin: 0;">Statut paiement</p>
                        <p style="margin: 2px 0;"><span class="badge badge-<?= $order['payment_status'] ?>"><?= statusLabel($order['payment_status']) ?></span></p>
                    </div>
                    <div>
                        <p style="font-size: 0.8rem; color: var(--text-light); margin: 0;">Mode de paiement</p>
                        <p style="margin: 2px 0; font-weight: 500;"><?= statusLabel($order['payment_method']) ?></p>
                    </div>
                    <?php if (!empty($order['shipping_method'])): ?>
                    <div>
                        <p style="font-size: 0.8rem; color: var(--text-light); margin: 0;">Livraison</p>
                        <p style="margin: 2px 0; font-weight: 500;"><?= statusLabel($order['shipping_method']) ?></p>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($order['shipping_tracking'])): ?>
                    <div>
                        <p style="font-size: 0.8rem; color: var(--text-light); margin: 0;">N° de suivi</p>
                        <p style="margin: 2px 0;"><a href="https://parcelsapp.com/fr/tracking/<?= e($order['shipping_tracking']) ?>" target="_blank" style="color: var(--gold-dark); font-weight: 600;"><?= e($order['shipping_tracking']) ?></a></p>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($order['notes']) && strpos($order['notes'], 'Point relais') !== false): ?>
                    <?php
                    preg_match('/Point relais : (.+?) \(ID:/', $order['notes'], $relayMatch);
                    $relayText = $relayMatch[1] ?? '';
                    ?>
                    <?php if ($relayText): ?>
                        <div style="background: var(--bg-warm); border-radius: var(--radius); padding: 12px; margin: 12px 0;">
                            <p style="margin: 0; font-size: 0.9rem;"><strong>Point relais :</strong> <?= e($relayText) ?></p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="order-summary" style="margin-bottom: 24px;">
                <h3 style="margin-bottom: 16px;">Adresse de livraison</h3>
                <p style="margin: 4px 0;"><?= e($order['customer_firstname'] . ' ' . $order['customer_lastname']) ?></p>
                <p style="margin: 4px 0;"><?= e($order['shipping_address']) ?></p>
                <p style="margin: 4px 0;"><?= e($order['shipping_postal'] . ' ' . $order['shipping_city']) ?></p>
            </div>

            <div class="order-summary">
                <h3 style="margin-bottom: 16px;">Détail de la commande</h3>
                <?php foreach ($orderItems as $item): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--border);">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <?php if (!empty($item['image'])): ?>
                                <img src="/uploads/thumbs/<?= e($item['image']) ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            <?php endif; ?>
                            <span><?= e($item['title']) ?></span>
                        </div>
                        <strong><?= formatPrice($item['price']) ?></strong>
                    </div>
                <?php endforeach; ?>
                <?php if (($order['shipping_cost'] ?? 0) > 0): ?>
                    <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--border); color: var(--text-light);">
                        <span>Frais de livraison</span>
                        <span><?= formatPrice($order['shipping_cost']) ?></span>
                    </div>
                <?php endif; ?>
                <div style="display: flex; justify-content: space-between; padding: 16px 0; font-size: 1.1rem;">
                    <span>Total</span>
                    <strong style="color: var(--gold-dark);"><?= formatPrice($order['total']) ?></strong>
                </div>
            </div>

            <div style="text-align: center; margin-top: 24px;">
                <a href="/suivi" class="btn btn-outline">Suivre une autre commande</a>
            </div>
        <?php endif; ?>
    </div>
</section>
