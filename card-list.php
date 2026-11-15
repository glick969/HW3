<?php
require_once __DIR__ . '/includes/db.php';


$giftCards = $pdo->query('SELECT cardId, cardName, points FROM GIFTCARD ORDER BY cardName')->fetchAll();

$statusMessages = [
    'added' => 'Gift card added.',
    'deleted' => 'Gift card deleted.',
];
$status = $_GET['status'] ?? '';
$statusMessage = is_string($status) ? ($statusMessages[$status] ?? '') : '';

$pageTitle = 'Gift Card List';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>
    <main class="page">
        <h1>Gift Card List</h1>

        <?php if ($statusMessage !== ''): ?>
            <p class="alert alert-success"><?= htmlspecialchars($statusMessage, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <p class="page-links">
            <a href="card-add.php">Add Gift Card</a>
        </p>

        <?php if (count($giftCards) === 0): ?>
            <p>There are no gift cards yet. <a href="card-add.php">Add the first one.</a></p>
        <?php else: ?>
            <section class="card-list">
                <?php foreach ($giftCards as $giftCard): ?>
                    <?php
                    $detailsUrl = 'card-details.php?card_id=' . (int) $giftCard['cardId'];
                    $cardName = htmlspecialchars($giftCard['cardName'], ENT_QUOTES, 'UTF-8');
                    ?>
                    <article class="card">
                        <a href="<?= $detailsUrl ?>">
                            <img src="images/gift-card.png" alt="<?= $cardName ?>">
                        </a>
                        <h2><a href="<?= $detailsUrl ?>"><?= $cardName ?></a></h2>
                        <p>Points Required: <?= number_format((int) $giftCard['points']) ?></p>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
