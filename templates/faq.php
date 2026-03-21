<div class="section">
    <div class="container">
        <h1 class="section-title">Questions fréquentes</h1>

        <?php if (!empty($faqs)): ?>
        <div class="faq-list">
            <?php foreach ($faqs as $faq): ?>
            <div class="faq-item">
                <button class="faq-question" type="button" aria-expanded="false">
                    <span><?= e($faq['question']) ?></span>
                    <svg class="faq-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="faq-answer" hidden>
                    <div class="faq-answer-inner"><?= nl2br(e($faq['answer'])) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <p>Aucune question pour le moment.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($faqs)): ?>
<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(function ($faq) {
        return [
            '@type' => 'Question',
            'name' => $faq['question'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $faq['answer'],
            ],
        ];
    }, $faqs),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
</script>
<?php endif; ?>

<script>
document.querySelectorAll('.faq-question').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var item = this.closest('.faq-item');
        var answer = item.querySelector('.faq-answer');
        var expanded = this.getAttribute('aria-expanded') === 'true';
        document.querySelectorAll('.faq-item.open').forEach(function(openItem) {
            if (openItem !== item) {
                openItem.classList.remove('open');
                openItem.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
                openItem.querySelector('.faq-answer').hidden = true;
            }
        });
        if (expanded) {
            item.classList.remove('open');
            this.setAttribute('aria-expanded', 'false');
            answer.hidden = true;
        } else {
            item.classList.add('open');
            this.setAttribute('aria-expanded', 'true');
            answer.hidden = false;
        }
    });
});
</script>
