<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Vogel Art Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: var(--bg-warm);
        }
        .login-box {
            background: var(--white);
            padding: 40px;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 400px;
        }
        .login-box h1 {
            text-align: center;
            margin-bottom: 8px;
        }
        .login-box .subtitle {
            text-align: center;
            color: var(--text-light);
            margin-bottom: 24px;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>Vogel <span style="color: var(--gold);">Art</span> Gallery</h1>
        <p class="subtitle">Administration</p>

        <?php if ($msg = flash('error')): ?>
            <div class="flash flash-error"><?= e($msg) ?></div>
        <?php endif; ?>

        <form method="POST" action="/admin/login">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Se connecter</button>
        </form>
    </div>
</body>
</html>
