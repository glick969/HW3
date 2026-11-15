<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/validation.php';

$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$giftCard = null;
$errorMessage = '';

// The ID comes from the URL when the page opens and from a hidden field when deletion is confirmed.
$cardId = validate_card_id($isPost ? ($_POST['card_id'] ?? '') : ($_GET['card_id'] ?? ''));

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

// Delete only after the user confirms with the POST form. A GET request only shows the confirmation.
if ($giftCard !== null && $isPost && ($_POST['confirm'] ?? '') === 'yes') {
    $statement = $pdo->prepare('DELETE FROM GIFTCARD WHERE cardId = :cardId');
    $statement->execute(['cardId' => $cardId]);

    header('Location: card-list.php?status=deleted');
    exit;
}

$pageTitle = 'Delete Gift Card';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>
    <main class="page">
        <h1>Delete Gift Card</h1>

        <?php if ($giftCard === null): ?>
            <p class="alert alert-error"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
            <p><a href="card-list.php">Return to Gift Card List</a></p>
        <?php else: ?>
            <p class="alert alert-warning">
                Are you sure you want to delete
                <strong><?= htmlspecialchars($giftCard['cardName'], ENT_QUOTES, 'UTF-8') ?></strong>?
                This cannot be undone.
            </p>

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

            <form action="card-delete.php" method="post" class="form-actions">
                <input type="hidden" name="card_id" value="<?= $cardId ?>">
                <input type="hidden" name="confirm" value="yes">
                <button type="submit" class="button-danger">Delete Gift Card</button>
                <a href="card-details.php?card_id=<?= $cardId ?>" class="button-link">Cancel</a>
            </form>
        <?php endif; ?>
    </main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
