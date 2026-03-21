<section class="section">
    <div class="container">
        <div class="product-page">
            <?php
            $allMedia = [['type' => 'image', 'src' => '/uploads/' . $painting['image']]];
            if (!empty($painting['video'])) {
                $allMedia[] = ['type' => 'video', 'src' => '/uploads/' . $painting['video']];
            }
            foreach ($gallery ?? [] as $img) $allMedia[] = ['type' => 'image', 'src' => '/uploads/' . $img['image']];
            ?>
            <div>
                <div class="product-image <?= $painting['status'] === 'sold' ? 'product-image-sold' : '' ?>" onclick="openGallery(0)" style="cursor:zoom-in;">
                    <img id="main-product-img" src="<?= $allMedia[0]['src'] ?>" alt="<?= e($painting['title']) ?>">
                    <?php if ($painting['status'] === 'sold'): ?>
                        <div class="sold-banner sold-banner-large">VENDU</div>
                    <?php endif; ?>
                </div>
                <?php if (count($allMedia) > 1): ?>
                <div style="display:flex;gap:8px;margin-top:10px;overflow-x:auto;">
                    <?php foreach ($allMedia as $i => $media): ?>
                        <?php if ($media['type'] === 'video'): ?>
                            <div onclick="switchMedia(<?= $i ?>)" id="thumb-<?= $i ?>" style="width:70px;height:70px;border-radius:4px;cursor:pointer;border:2px solid transparent;opacity:0.7;background:#2D2D2D;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="#fff"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                            </div>
                        <?php else: ?>
                            <img src="/uploads/thumbs/<?= e(basename($media['src'])) ?>" alt="" style="width:70px;height:70px;object-fit:cover;border-radius:4px;cursor:pointer;border:2px solid <?= $i === 0 ? 'var(--gold)' : 'transparent' ?>;opacity:<?= $i === 0 ? '1' : '0.7' ?>;flex-shrink:0;" onclick="switchMedia(<?= $i ?>)" id="thumb-<?= $i ?>">
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div id="img-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.93);z-index:99999;align-items:center;justify-content:center;cursor:zoom-out;" onclick="closeModal()">
                <div id="modal-content" style="max-width:95vw;max-height:90vh;display:flex;align-items:center;justify-content:center;" onclick="event.stopPropagation()"></div>
                <button style="position:absolute;top:16px;right:20px;background:none;border:none;color:#fff;font-size:32px;cursor:pointer;line-height:1;" onclick="closeModal()">&times;</button>
                <?php if (count($allMedia) > 1): ?>
                <button style="position:absolute;left:16px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,0.15);border:none;color:#fff;font-size:28px;cursor:pointer;width:44px;height:44px;border-radius:50%;" onclick="event.stopPropagation();galleryNav(-1)">&#8249;</button>
                <button style="position:absolute;right:16px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,0.15);border:none;color:#fff;font-size:28px;cursor:pointer;width:44px;height:44px;border-radius:50%;" onclick="event.stopPropagation();galleryNav(1)">&#8250;</button>
                <?php endif; ?>
            </div>
            <script>
            var galleryMedia = <?= json_encode($allMedia) ?>;
            var currentIdx = 0;
            function switchMedia(i) {
                currentIdx = i;
                var media = galleryMedia[i];
                var mainImg = document.getElementById('main-product-img');
                var parent = mainImg.parentElement;
                var oldVideo = parent.querySelector('video');
                if (oldVideo) oldVideo.remove();
                if (media.type === 'video') {
                    mainImg.style.display = 'none';
                    var v = document.createElement('video');
                    v.src = media.src;
                    v.controls = true;
                    v.autoplay = true;
                    v.style.cssText = 'width:100%;border-radius:var(--radius);';
                    parent.insertBefore(v, mainImg);
                } else {
                    mainImg.style.display = 'block';
                    mainImg.src = media.src;
                }
                document.querySelectorAll('[id^="thumb-"]').forEach(function(t, idx) {
                    t.style.borderColor = idx === i ? 'var(--gold)' : 'transparent';
                    t.style.opacity = idx === i ? '1' : '0.7';
                });
            }
            function openGallery(i) {
                currentIdx = i;
                renderModal(i);
                document.getElementById('img-modal').style.display = 'flex';
            }
            function closeModal() {
                document.getElementById('img-modal').style.display = 'none';
                var mc = document.getElementById('modal-content');
                var v = mc.querySelector('video');
                if (v) v.pause();
            }
            function renderModal(i) {
                var mc = document.getElementById('modal-content');
                var media = galleryMedia[i];
                if (media.type === 'video') {
                    mc.innerHTML = '<video src="' + media.src + '" controls autoplay style="max-width:95vw;max-height:90vh;border-radius:4px;"></video>';
                } else {
                    mc.innerHTML = '<img src="' + media.src + '" style="max-width:95vw;max-height:90vh;object-fit:contain;border-radius:4px;">';
                }
            }
            function galleryNav(dir) {
                currentIdx = (currentIdx + dir + galleryMedia.length) % galleryMedia.length;
                renderModal(currentIdx);
                switchMedia(currentIdx);
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
