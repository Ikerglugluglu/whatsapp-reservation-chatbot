<?php
// Layout principal del panel.
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars((string) ($title ?? 'CHAT-BOT')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="public/assets/css/app.css" rel="stylesheet">
</head>
<body class="bg-light">
<?= $content ?>
<?php if (!empty($page_script)): ?>
<script src="<?= htmlspecialchars((string) $page_script) ?>" defer></script>
<?php endif; ?>
</body>
</html>


