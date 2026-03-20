<section class="section">
    <div class="contact-page">
        <h1>Contact</h1>
        <p style="margin-bottom: 24px; color: var(--text-light);">Une question ? N'hésitez pas à nous écrire.</p>

        <form method="POST" action="/contact">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="name">Nom *</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="message">Message *</label>
                <textarea id="message" name="message" rows="6" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Envoyer</button>
        </form>
    </div>
</section>
