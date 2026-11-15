# Test Report - AirAsia Rewards HW3

Tested with PHP's built-in server (`php -S`) starting from a freshly seeded `rewards.db` (20 gift cards, 5 accounts).

## Summary

| Test ID | Test | Status |
|---|---|---|
| T01 | Validation test harness | Pass |
| T02 | View Gift Card List | Pass |
| T03 | View Gift Card Details | Pass |
| T04 | Add Gift Card | Pass |
| T05 | Update Gift Card | Pass |
| T06 | Delete Gift Card | Pass |
| T07 | Invalid or altered input handling | Pass |
| T08 | Invalid Gift Card identifier handling | Pass |

## T01 - Validation test harness

**Steps and input:** Opened `tests/validation-tests.php`. It runs 18 tests on `validate_gift_card()` and `validate_card_id()`, including the 5 required ones: valid data, blank Card Name, altered Card Type (`Groceries`), zero Card Value, and invalid Points Required (`2.5`).

**Expected result:** Every test shows PASS, and no database records change.

**Actual result:** The page showed "18 of 18 tests passed." The test file only loads `includes/validation.php` and never connects to the database, and the GIFTCARD count stayed at 20. To make sure the page can actually fail, I temporarily removed the Card Type check from `validation.php`. The page then showed 16 of 18 with the two Card Type tests marked FAIL. I put the check back and it returned to 18 of 18.

**Status:** Pass

## T02 - View Gift Card List

**Steps and input:** Opened `card-list.php`.

**Expected result:** All gift cards show with an image, Card Name, and Points Required. The image and name link to `card-details.php?card_id=...`. There's a link to `card-add.php`.

**Actual result:** All 20 seeded cards showed, sorted by name, each with the image, name, and points (for example "Amazon $50 Gift Card, Points Required: 5,000"). The image and name both linked to the right ID, like `card-details.php?card_id=1`. The Add Gift Card link was at the top of the page and in the nav.

**Status:** Pass

## T03 - View Gift Card Details

**Steps and input:** Clicked Amazon $50 Gift Card on the list, which opened `card-details.php?card_id=1`.

**Expected result:** Read-only details for card 1 with its image, plus links to Update, Delete, and the Gift Card List.

**Actual result:** The page showed the image and Card ID 1, Amazon $50 Gift Card, Retail, $50.00, and 5,000 points as plain text with no form fields. The links went to `card-update.php?card_id=1`, `card-delete.php?card_id=1`, and `card-list.php`.

**Status:** Pass

## T04 - Add Gift Card

**Steps and input:** Opened `card-add.php` and submitted Card Name `Costco $60 Gift Card`, Card Type `Retail`, Card Value `60`, Points Required `6000`.

**Expected result:** The card is inserted and I'm sent back to `card-list.php`, where it shows up.

**Actual result:** The form sent a POST and the page redirected to `card-list.php?status=added`, which showed "Gift card added." The new card appeared in the list. The database had a new row with cardId 21, Costco $60 Gift Card, Retail, 60, 6000.

**Status:** Pass

## T05 - Update Gift Card

**Steps and input:** Opened `card-update.php?card_id=6` (Starbucks $25 Gift Card) and changed it to Card Name `Starbucks $30 Gift Card`, Card Type `Dining`, Card Value `30.00`, Points Required `3000`.

**Expected result:** The form starts filled with the saved values. After submitting, the record is updated and the details page shows the new values.

**Actual result:** The form opened with Starbucks $25 Gift Card, Dining selected, 25.00, and 2500 filled in. After submitting, it redirected to `card-details.php?card_id=6&status=updated`, which showed "Gift card updated." with Starbucks $30 Gift Card, Dining, $30.00, and 3,000 points.

**Status:** Pass

## T06 - Delete Gift Card

**Steps and input:** From the details page for card 21 (Costco $60 Gift Card), clicked Delete Gift Card to open `card-delete.php?card_id=21`, then clicked the Delete Gift Card button.

**Expected result:** The page names the card and asks for confirmation. Nothing is deleted until the POST confirmation is sent. Then the card is removed and I'm sent back to `card-list.php`.

**Actual result:** The page said "Are you sure you want to delete Costco $60 Gift Card?" and showed its details. Card 21 was still in the database after opening this page. After clicking Delete, it redirected to `card-list.php?status=deleted` with "Gift card deleted." and card 21 was gone from both the list and the database. A POST without the confirm field did not delete anything.

**Status:** Pass

## T07 - Invalid or altered input handling

**Steps and input:** Turned off the browser's form checks (to act like someone editing the page) and sent these to `card-add.php`:

1. Card Name blank, Card Type `Groceries` (added to the dropdown with dev tools), Card Value `0`, Points `2.5`
2. Card Value `-25`, Points `abc`
3. Card Name `<script>alert(1)</script>` with an invalid Card Type
4. Card Name and Card Type sent as arrays (`cardName[]=x`)
5. On `card-update.php?card_id=1`, cleared the Card Name and submitted

**Expected result:** Nothing is saved. Each problem gets a clear error message, and any text typed in is shown back safely instead of running as HTML.

**Actual result:**

1. Four errors showed, both in a list at the top and under each field: "Enter a card name.", "Select a valid card type.", "Enter a card value greater than $0, like 25 or 25.00.", and "Enter points as a whole number greater than 0."
2. The Card Value and Points errors showed.
3. The name was put back in the field as `&lt;script&gt;alert(1)&lt;/script&gt;`, so it showed as text and didn't run.
4. Card Name and Card Type errors showed, with no PHP warnings.
5. "Enter a card name." showed and card 1 kept its name, Amazon $50 Gift Card.

The GIFTCARD count stayed at 20 the whole time.

**Status:** Pass

## T08 - Invalid Gift Card identifier handling

**Steps and input:** Opened these URLs and sent these forms:

- `card-details.php?card_id=abc`, `?card_id=0`, `?card_id=1 OR 1=1`, `?card_id[]=1`, and no card_id at all
- `card-details.php?card_id=9999`
- `card-update.php?card_id=-5` and `card-update.php?card_id=9999`
- `card-delete.php?card_id=xyz` and `card-delete.php?card_id=9999`
- POST to `card-delete.php` with `card_id=9999&confirm=yes`
- POST to `card-update.php` with `card_id=abc`

**Expected result:** No database errors or warnings. Bad IDs show an invalid ID message and IDs that don't exist show a not found message, with a link back to the list. Nothing is changed or deleted.

**Actual result:** Every bad ID (`abc`, `0`, `-5`, `xyz`, `1 OR 1=1`, the array, and the missing ID) showed "That gift card ID is not valid." with HTTP 400. Every ID that doesn't exist (`9999`) showed "No gift card was found with that ID." with HTTP 404. All of them had the Return to Gift Card List link. The server log had no warnings, and the GIFTCARD count didn't change.

**Status:** Pass
