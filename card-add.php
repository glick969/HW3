<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/validation.php';


$values = ['cardName' => '', 'cardType' => '', 'cardValue' => '', 'points' => ''];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = validate_gift_card($_POST);
    $values = $result['values'];
    $errors = $result['errors'];

    if (count($errors) === 0) {
        $statement = $pdo->prepare(
            'INSERT INTO GIFTCARD (cardName, cardType, cardValue, points)
             VALUES (:cardName, :cardType, :cardValue, :points)'
        );
        $statement->execute([
            'cardName' => $values['cardName'],
            'cardType' => $values['cardType'],
            'cardValue' => $values['cardValue'],
            'points' => $values['points'],
        ]);

        
        header('Location: card-list.php?status=added');
        exit;
    }
}

$pageTitle = 'Add Gift Card';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>
    <main class="page">
        <h1>Add Gift Card</h1>

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

        <form action="card-add.php" method="post" class="form">
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
                <a href="card-list.php" class="button-link">Cancel</a>
            </div>
        </form>
    </main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
