<div class="admin-section">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
        <h2>Questions fréquentes</h2>
        <button class="btn btn-primary btn-sm" onclick="document.getElementById('add-faq-modal').style.display='flex'">Ajouter une question</button>
    </div>

    <?php if (!empty($faqs)): ?>
    <div id="faq-sortable">
        <?php foreach ($faqs as $faq): ?>
        <div class="faq-admin-item" data-id="<?= $faq['id'] ?>" draggable="true">
            <div class="faq-admin-handle">&#9776;</div>
            <div class="faq-admin-content">
                <strong><?= e($faq['question']) ?></strong>
                <p style="color:#6B6B6B;font-size:0.9rem;margin-top:4px;"><?= e(mb_substr($faq['answer'], 0, 120)) ?>...</p>
            </div>
            <div class="faq-admin-actions">
                <span class="badge <?= $faq['active'] ? 'badge-available' : 'badge-sold' ?>"><?= $faq['active'] ? 'Active' : 'Inactive' ?></span>
                <button class="btn btn-outline btn-sm" onclick="openEditFaq(<?= $faq['id'] ?>, <?= e(json_encode($faq['question'])) ?>, <?= e(json_encode($faq['answer'])) ?>, <?= $faq['active'] ?>)">Modifier</button>
                <form method="POST" action="/admin/faq/supprimer/<?= $faq['id'] ?>" style="display:inline;" onsubmit="return confirm('Supprimer cette question ?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty-state">
        <p>Aucune question FAQ pour le moment.</p>
    </div>
    <?php endif; ?>
</div>

<div id="add-faq-modal" class="faq-modal-overlay" style="display:none;">
    <div class="faq-modal">
        <h3>Ajouter une question</h3>
        <form method="POST" action="/admin/faq/ajouter">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Question</label>
                <input type="text" name="question" required>
            </div>
            <div class="form-group">
                <label>Réponse</label>
                <textarea name="answer" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="active" value="1" checked> Active</label>
            </div>
            <div style="display:flex;gap:12px;justify-content:flex-end;">
                <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('.faq-modal-overlay').style.display='none'">Annuler</button>
                <button type="submit" class="btn btn-primary btn-sm">Ajouter</button>
            </div>
        </form>
    </div>
</div>

<div id="edit-faq-modal" class="faq-modal-overlay" style="display:none;">
    <div class="faq-modal">
        <h3>Modifier la question</h3>
        <form method="POST" id="edit-faq-form">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Question</label>
                <input type="text" name="question" id="edit-faq-question" required>
            </div>
            <div class="form-group">
                <label>Réponse</label>
                <textarea name="answer" id="edit-faq-answer" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="active" value="1" id="edit-faq-active"> Active</label>
            </div>
            <div style="display:flex;gap:12px;justify-content:flex-end;">
                <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('.faq-modal-overlay').style.display='none'">Annuler</button>
                <button type="submit" class="btn btn-primary btn-sm">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditFaq(id, question, answer, active) {
    document.getElementById('edit-faq-form').action = '/admin/faq/modifier/' + id;
    document.getElementById('edit-faq-question').value = question;
    document.getElementById('edit-faq-answer').value = answer;
    document.getElementById('edit-faq-active').checked = active == 1;
    document.getElementById('edit-faq-modal').style.display = 'flex';
}

(function() {
    var container = document.getElementById('faq-sortable');
    if (!container) return;
    var dragItem = null;

    container.addEventListener('dragstart', function(e) {
        dragItem = e.target.closest('.faq-admin-item');
        if (dragItem) {
            dragItem.style.opacity = '0.5';
            e.dataTransfer.effectAllowed = 'move';
        }
    });

    container.addEventListener('dragend', function(e) {
        if (dragItem) dragItem.style.opacity = '1';
        dragItem = null;
    });

    container.addEventListener('dragover', function(e) {
        e.preventDefault();
        var target = e.target.closest('.faq-admin-item');
        if (target && target !== dragItem) {
            var rect = target.getBoundingClientRect();
            var mid = rect.top + rect.height / 2;
            if (e.clientY < mid) {
                container.insertBefore(dragItem, target);
            } else {
                container.insertBefore(dragItem, target.nextSibling);
            }
        }
    });

    container.addEventListener('drop', function(e) {
        e.preventDefault();
        var items = container.querySelectorAll('.faq-admin-item');
        var ids = [];
        items.forEach(function(item) { ids.push(item.dataset.id); });

        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/faq/reorder';
        form.style.display = 'none';

        var csrf = document.querySelector('input[name="csrf_token"]');
        if (csrf) {
            var csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = csrf.value;
            form.appendChild(csrfInput);
        }

        var idsInput = document.createElement('input');
        idsInput.type = 'hidden';
        idsInput.name = 'ids';
        idsInput.value = ids.join(',');
        form.appendChild(idsInput);

        document.body.appendChild(form);
        form.submit();
    });
})();
</script>
