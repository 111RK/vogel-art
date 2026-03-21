<section class="section">
    <div class="container">
        <div class="product-page">
            <?php
            $allImages = ['/uploads/' . $painting['image']];
            foreach ($gallery ?? [] as $img) $allImages[] = '/uploads/' . $img['image'];
            ?>
            <div>
                <div class="product-image <?= $painting['status'] === 'sold' ? 'product-image-sold' : '' ?>" onclick="openGallery(0)" style="cursor:zoom-in;">
                    <img id="main-product-img" src="<?= $allImages[0] ?>" alt="<?= e($painting['title']) ?>">
                    <?php if ($painting['status'] === 'sold'): ?>
                        <div class="sold-banner sold-banner-large">VENDU</div>
                    <?php endif; ?>
                </div>
                <?php if (count($allImages) > 1): ?>
                <div style="display:flex;gap:8px;margin-top:10px;overflow-x:auto;">
                    <?php foreach ($allImages as $i => $src): ?>
                        <img src="/uploads/thumbs/<?= e(basename($src)) ?>" alt="" style="width:70px;height:70px;object-fit:cover;border-radius:4px;cursor:pointer;border:2px solid <?= $i === 0 ? 'var(--gold)' : 'transparent' ?>;opacity:<?= $i === 0 ? '1' : '0.7' ?>;" onclick="switchImage(<?= $i ?>)" id="thumb-<?= $i ?>">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div id="img-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.93);z-index:99999;align-items:center;justify-content:center;cursor:zoom-out;" onclick="this.style.display='none'">
                <img id="modal-img" src="" alt="" style="max-width:95vw;max-height:90vh;object-fit:contain;border-radius:4px;">
                <button style="position:absolute;top:16px;right:20px;background:none;border:none;color:#fff;font-size:32px;cursor:pointer;line-height:1;" onclick="event.stopPropagation();document.getElementById('img-modal').style.display='none'">&times;</button>
                <?php if (count($allImages) > 1): ?>
                <button style="position:absolute;left:16px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,0.15);border:none;color:#fff;font-size:28px;cursor:pointer;width:44px;height:44px;border-radius:50%;" onclick="event.stopPropagation();galleryNav(-1)">&#8249;</button>
                <button style="position:absolute;right:16px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,0.15);border:none;color:#fff;font-size:28px;cursor:pointer;width:44px;height:44px;border-radius:50%;" onclick="event.stopPropagation();galleryNav(1)">&#8250;</button>
                <?php endif; ?>
            </div>
            <script>
            var galleryImages = <?= json_encode($allImages) ?>;
            var currentImg = 0;
            function switchImage(i) {
                currentImg = i;
                document.getElementById('main-product-img').src = galleryImages[i];
                document.querySelectorAll('[id^="thumb-"]').forEach(function(t, idx) {
                    t.style.borderColor = idx === i ? 'var(--gold)' : 'transparent';
                    t.style.opacity = idx === i ? '1' : '0.7';
                });
            }
            function openGallery(i) {
                currentImg = i;
                document.getElementById('modal-img').src = galleryImages[i];
                document.getElementById('img-modal').style.display = 'flex';
            }
            function galleryNav(dir) {
                currentImg = (currentImg + dir + galleryImages.length) % galleryImages.length;
                document.getElementById('modal-img').src = galleryImages[currentImg];
            }
            </script>
            <div class="product-info">
                <h1><?= e($painting['title']) ?></h1>
                <p class="price"><?= formatPrice($painting['price']) ?></p>

                <?php if ($painting['status'] === 'available'): ?>
                    <span class="badge badge-available">Disponible</span>
                <?php else: ?>
                    <span class="badge badge-sold">Vendu</span>
                <?php endif; ?>

                <?php if ($painting['description']): ?>
                    <div class="description">
                        <p><?= nl2br(e($painting['description'])) ?></p>
                    </div>
                <?php endif; ?>

                <div class="details">
                    <?php if ($painting['technique']): ?>
                        <p><strong>Technique :</strong> <?= e($painting['technique']) ?></p>
                    <?php endif; ?>
                    <?php if ($painting['width_cm'] && $painting['height_cm']): ?>
                        <p><strong>Dimensions :</strong> <?= $painting['width_cm'] ?> x <?= $painting['height_cm'] ?> cm</p>
                    <?php endif; ?>
                    <p><strong>Pièce unique</strong></p>
                </div>

                <?php if ($painting['status'] === 'available'): ?>
                    <form method="POST" action="/panier/ajouter">
                        <?= csrf_field() ?>
                        <input type="hidden" name="painting_id" value="<?= $painting['id'] ?>">
                        <button type="submit" class="btn btn-primary">Ajouter au panier</button>
                    </form>
                <?php else: ?>
                    <p style="color: var(--text-light); font-style: italic; margin-top: 12px;">Cette oeuvre a trouvé son propriétaire.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($related)): ?>
            <div class="section">
                <h2 class="section-title">Vous aimerez aussi</h2>
                <div class="paintings-grid">
                    <?php foreach ($related as $item): ?>
                        <a href="/tableau/<?= e($item['slug']) ?>" class="painting-card <?= $item['status'] === 'sold' ? 'painting-sold' : '' ?>">
                            <div class="image-wrapper">
                                <img src="/uploads/thumbs/<?= e($item['image']) ?>" alt="<?= e($item['title']) ?>" loading="lazy">
                                <?php if ($item['status'] === 'sold'): ?>
                                    <div class="sold-banner">VENDU</div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h3><?= e($item['title']) ?></h3>
                                <p class="price"><?= formatPrice($item['price']) ?></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
