<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/validation.php';

$giftCard = null;
$errorMessage = '';

$cardId = validate_card_id($_GET['card_id'] ?? '');

if ($cardId === null) {
    http_response_code(400);
    $errorMessage = 'That gift card ID is not valid.';
} else {
    $statement = $pdo->prepare('SELECT cardId, cardName, cardType, cardValue, points FROM GIFTCARD WHERE cardId = :cardId');
    $statement->execute(['cardId' => $cardId]);
    $giftCard = $statement->fetch();

    if ($giftCard === false) {
        http_response_code(404);
        $errorMessage = 'No gift card was found with that ID.';
        $giftCard = null;
    }
}

$status = $_GET['status'] ?? '';
$statusMessage = ($status === 'updated') ? 'Gift card updated.' : '';

$pageTitle = 'Gift Card Details';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>
    <main class="page">
        <h1>Gift Card Details</h1>

        <?php if ($giftCard === null): ?>
            <p class="alert alert-error"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
            <p><a href="card-list.php">Return to Gift Card List</a></p>
        <?php else: ?>
            <?php if ($statusMessage !== ''): ?>
                <p class="alert alert-success"><?= htmlspecialchars($statusMessage, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <img src="images/gift-card.png" alt="<?= htmlspecialchars($giftCard['cardName'], ENT_QUOTES, 'UTF-8') ?>" class="card-image">

            <dl class="card-details">
                <dt>Card ID</dt>
                <dd><?= (int) $giftCard['cardId'] ?></dd>

                <dt>Card Name</dt>
                <dd><?= htmlspecialchars($giftCard['cardName'], ENT_QUOTES, 'UTF-8') ?></dd>

                <dt>Card Type</dt>
                <dd><?= htmlspecialchars($giftCard['cardType'], ENT_QUOTES, 'UTF-8') ?></dd>

                <dt>Card Value</dt>
                <dd>$<?= number_format((float) $giftCard['cardValue'], 2) ?></dd>

                <dt>Points Required</dt>
                <dd><?= number_format((int) $giftCard['points']) ?></dd>
            </dl>

            <p class="page-links">
                <a href="card-update.php?card_id=<?= (int) $giftCard['cardId'] ?>">Update Gift Card</a>
                <a href="card-delete.php?card_id=<?= (int) $giftCard['cardId'] ?>">Delete Gift Card</a>
                <a href="card-list.php">Return to Gift Card List</a>
            </p>
        <?php endif; ?>
    </main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
