<?php
// Validation test harness.
// Tests the functions in includes/validation.php only.
// Does not connect to the database, so no records are added, changed, or deleted

require_once __DIR__ . '/../includes/validation.php';

$validCard = [
    'cardName' => 'Amazon $50 Gift Card',
    'cardType' => 'Retail',
    'cardValue' => '50.00',
    'points' => '5000',
];

// Each gift card test: name, input, whether it should pass, and which field should fail
$cardTests = [
    ['Valid Gift Card data', $validCard, true, null],
    ['Blank Card Name', array_merge($validCard, ['cardName' => '']), false, 'cardName'],
    ['Card Name with only spaces', array_merge($validCard, ['cardName' => '    ']), false, 'cardName'],
    ['Card Name over 100 characters', array_merge($validCard, ['cardName' => str_repeat('A', 101)]), false, 'cardName'],
    ['Altered Card Type (not in the list)', array_merge($validCard, ['cardType' => 'Groceries']), false, 'cardType'],
    ['Card Type with wrong capitalization', array_merge($validCard, ['cardType' => 'retail']), false, 'cardType'],
    ['Zero Card Value', array_merge($validCard, ['cardValue' => '0']), false, 'cardValue'],
    ['Negative Card Value', array_merge($validCard, ['cardValue' => '-25']), false, 'cardValue'],
    ['Card Value that is not a number', array_merge($validCard, ['cardValue' => 'abc']), false, 'cardValue'],
    ['Points Required with a decimal', array_merge($validCard, ['points' => '2.5']), false, 'points'],
    ['Negative Points Required', array_merge($validCard, ['points' => '-100']), false, 'points'],
    ['Points Required that is not a number', array_merge($validCard, ['points' => 'five thousand']), false, 'points'],
];

// Each card ID test: name, input, expected result (an int or null for invalid)
$idTests = [
    ['Valid card ID', '12', 12],
    ['Card ID with letters', 'abc', null],
    ['Card ID of zero', '0', null],
    ['Negative card ID', '-3', null],
    ['Card ID with SQL text', '1 OR 1=1', null],
    ['Card ID sent as an array', ['1'], null],
];

// Shows a test input in one short, readable line
function describe_input($input): string
{
    if (is_array($input)) {
        $parts = [];
        foreach ($input as $key => $value) {
            $parts[] = (is_string($key) ? $key . ': ' : '') . describe_input($value);
        }
        return '[' . implode(', ', $parts) . ']';
    }

    $text = (string) $input;
    if (mb_strlen($text) > 30) {
        return '"' . mb_substr($text, 0, 12) . '..." (' . mb_strlen($text) . ' characters)';
    }
    return '"' . $text . '"';
}

$rows = [];

foreach ($cardTests as [$name, $input, $shouldPass, $expectedField]) {
    $errors = validate_gift_card($input)['errors'];

    if ($shouldPass) {
        $passed = count($errors) === 0;
        $expected = 'Passes validation';
    } else {
        $passed = isset($errors[$expectedField]);
        $expected = 'Fails validation (' . $expectedField . ')';
    }

    $actual = count($errors) === 0
        ? 'Passed validation'
        : 'Failed: ' . implode(' ', $errors);

    $rows[] = [$name, describe_input($input), $expected, $actual, $passed];
}

foreach ($idTests as [$name, $input, $expectedId]) {
    $result = validate_card_id($input);
    $passed = $result === $expectedId;
    $expected = $expectedId === null ? 'Invalid ID' : 'Valid ID ' . $expectedId;
    $actual = $result === null ? 'Invalid ID' : 'Valid ID ' . $result;

    $rows[] = [$name, describe_input($input), $expected, $actual, $passed];
}

$passCount = count(array_filter($rows, fn($row) => $row[4]));
$totalCount = count($rows);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation Tests | AirAsia Rewards</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header class="site-header">
        <p class="site-title">AirAsia Rewards</p>
    </header>

    <main class="page">
        <h1>Validation Tests</h1>
        <p>These tests check <code>includes/validation.php</code> without touching the database.</p>
        <p><strong><?= $passCount ?> of <?= $totalCount ?> tests passed.</strong></p>

        <table class="test-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Test</th>
                    <th>Input</th>
                    <th>Expected</th>
                    <th>Actual</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $index => [$name, $input, $expected, $actual, $passed]): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></td>
                        <td><code><?= htmlspecialchars($input, ENT_QUOTES, 'UTF-8') ?></code></td>
                        <td><?= htmlspecialchars($expected, ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($actual, ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="<?= $passed ? 'pass' : 'fail' ?>"><?= $passed ? 'PASS' : 'FAIL' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p><a href="../card-list.php">Return to Gift Card List</a></p>
    </main>
</body>
</html>
