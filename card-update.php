<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/validation.php';

$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$giftCard = null;
$errorMessage = '';
$errors = [];

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

if ($giftCard !== null) {
    $values = [
        'cardName' => $giftCard['cardName'],
        'cardType' => $giftCard['cardType'],
        'cardValue' => number_format((float) $giftCard['cardValue'], 2, '.', ''),
        'points' => (string) $giftCard['points'],
    ];

    if ($isPost) {
        $result = validate_gift_card($_POST);
        $values = $result['values'];
        $errors = $result['errors'];

        if (count($errors) === 0) {
            $statement = $pdo->prepare(
                'UPDATE GIFTCARD
                 SET cardName = :cardName, cardType = :cardType, cardValue = :cardValue, points = :points
                 WHERE cardId = :cardId'
            );
            $statement->execute([
                'cardName' => $values['cardName'],
                'cardType' => $values['cardType'],
                'cardValue' => $values['cardValue'],
                'points' => $values['points'],
                'cardId' => $cardId,
            ]);

            header('Location: card-details.php?card_id=' . $cardId . '&status=updated');
            exit;
        }
    }
}

$pageTitle = 'Update Gift Card';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>
    <main class="page">
        <h1>Update Gift Card</h1>

        <?php if ($giftCard === null): ?>
            <p class="alert alert-error"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
            <p><a href="card-list.php">Return to Gift Card List</a></p>
        <?php else: ?>
            <?php if (count($errors) > 0): ?>
                <div class="alert alert-error">
                    <p>Please fix the following and try again:</p>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="card-update.php" method="post" class="form">
                <input type="hidden" name="card_id" value="<?= $cardId ?>">

                <div class="form-group">
                    <label for="cardName">Card Name</label>
                    <input type="text" id="cardName" name="cardName" maxlength="100" required
                           value="<?= htmlspecialchars((string) $values['cardName'], ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['cardName'])): ?>
                        <p class="field-error"><?= htmlspecialchars($errors['cardName'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="cardType">Card Type</label>
                    <select id="cardType" name="cardType" required>
                        <option value="">Select Card Type</option>
                        <?php foreach (GIFT_CARD_TYPES as $type): ?>
                            <option value="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>"
                                <?= $values['cardType'] === $type ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['cardType'])): ?>
                        <p class="field-error"><?= htmlspecialchars($errors['cardType'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="cardValue">Card Value ($)</label>
                    <input type="number" id="cardValue" name="cardValue" min="0.01" max="10000" step="0.01" required
                           value="<?= htmlspecialchars((string) $values['cardValue'], ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['cardValue'])): ?>
                        <p class="field-error"><?= htmlspecialchars($errors['cardValue'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="points">Points Required</label>
                    <input type="number" id="points" name="points" min="1" max="1000000" step="1" required
                           value="<?= htmlspecialchars((string) $values['points'], ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['points'])): ?>
                        <p class="field-error"><?= htmlspecialchars($errors['points'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-actions">
                    <button type="submit">Submit</button>
                    <a href="card-details.php?card_id=<?= $cardId ?>" class="button-link">Cancel</a>
                </div>
            </form>
        <?php endif; ?>
    </main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
