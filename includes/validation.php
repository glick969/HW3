<?php
// Reusable server-side validation for Gift Card data.
// These functions only check values. They never read or change the database.

const GIFT_CARD_TYPES = ['Retail', 'Dining', 'Travel', 'Entertainment'];
const CARD_NAME_MAX_LENGTH = 100;   // cardName is varchar(100)
const CARD_VALUE_MAX = 10000;       // largest card value allowed, in dollars
const CARD_POINTS_MAX = 1000000;    // largest points value allowed

/**
 * Returns a trimmed string, or an empty string if the value is missing
 * or is not a string (for example, an array sent by an altered form).
 */
function clean_text($value): string
{
    return is_string($value) ? trim($value) : '';
}

/**
 * Checks a Gift Card ID from the URL or a form.
 * Returns the ID as an int, or null if it is not a positive whole number.
 */
function validate_card_id($value): ?int
{
    if (!is_string($value) && !is_int($value)) {
        return null;
    }

    $cardId = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    return $cardId === false ? null : $cardId;
}

/**
 * Checks submitted Gift Card values.
 *
 * Returns an array with two keys:
 *   'values' - the normalized values (safe to save when there are no errors)
 *   'errors' - field name => error message (empty when everything is valid)
 */
function validate_gift_card(array $input): array
{
    $errors = [];

    $cardName = clean_text($input['cardName'] ?? '');
    $cardType = clean_text($input['cardType'] ?? '');
    $cardValue = clean_text($input['cardValue'] ?? '');
    $points = clean_text($input['points'] ?? '');

    // Card Name: required, 1 to 100 characters.
    if ($cardName === '') {
        $errors['cardName'] = 'Enter a card name.';
    } elseif (mb_strlen($cardName) > CARD_NAME_MAX_LENGTH) {
        $errors['cardName'] = 'Card name must be ' . CARD_NAME_MAX_LENGTH . ' characters or fewer.';
    }

    // Card Type: must exactly match one of the allowed types.
    if (!in_array($cardType, GIFT_CARD_TYPES, true)) {
        $errors['cardType'] = 'Select a valid card type.';
    }

    // Card Value: positive dollar amount with at most 2 decimal places.
    if (!preg_match('/^\d+(\.\d{1,2})?$/', $cardValue) || (float) $cardValue <= 0) {
        $errors['cardValue'] = 'Enter a card value greater than $0, like 25 or 25.00.';
    } elseif ((float) $cardValue > CARD_VALUE_MAX) {
        $errors['cardValue'] = 'Card value cannot be more than $' . number_format(CARD_VALUE_MAX) . '.';
    }

    // Points Required: positive whole number.
    if (!preg_match('/^\d+$/', $points) || (int) $points <= 0) {
        $errors['points'] = 'Enter points as a whole number greater than 0.';
    } elseif ((int) $points > CARD_POINTS_MAX) {
        $errors['points'] = 'Points cannot be more than ' . number_format(CARD_POINTS_MAX) . '.';
    }

    return [
        'values' => [
            'cardName' => $cardName,
            'cardType' => $cardType,
            'cardValue' => isset($errors['cardValue']) ? $cardValue : (float) $cardValue,
            'points' => isset($errors['points']) ? $points : (int) $points,
        ],
        'errors' => $errors,
    ];
}
