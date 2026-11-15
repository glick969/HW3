# AirAsia Rewards Redemption System - HW3

IS 6465 Web Application Development

HW3 turns the Gift Card pages from my HW2 prototype into a working PHP app. Gift cards are stored in a SQLite database and managed through PDO, so you can list, view, add, update, and delete them. Login, sessions, customers, and redemption are part of HW4, so they aren't in this version.

## Running it

From the project folder in Codespaces:

```bash
php -S 0.0.0.0:8000
```

Then open the forwarded port and go to `/card-list.php`. The validation tests are at `/tests/validation-tests.php`.

## Resetting the database

`database/rewards.db` is already built from the schema and seed files. To reset it back to the original 20 gift cards and 5 accounts:

```bash
rm database/rewards.db
php -r '$db = new PDO("sqlite:database/rewards.db"); $db->exec(file_get_contents("database/schema.sql")); $db->exec(file_get_contents("database/seed.sql"));'
```

If the `sqlite3` command is installed, this works too:

```bash
sqlite3 database/rewards.db < database/schema.sql
sqlite3 database/rewards.db < database/seed.sql
```

## Project structure

```
airasia-hw3/
├── card-list.php          list of all gift cards
├── card-details.php       one gift card (card-details.php?card_id=1)
├── card-add.php           form to add a gift card
├── card-update.php        form to edit a gift card
├── card-delete.php        confirm and delete a gift card
├── includes/
│   ├── header.php         page setup and stylesheet link
│   ├── nav.php            links to Gift Card List and Add Gift Card
│   ├── footer.php         closing page content
│   ├── db.php             PDO connection to SQLite
│   └── validation.php     validation functions
├── database/
│   ├── schema.sql         creates the ACCOUNT and GIFTCARD tables
│   ├── seed.sql           5 accounts and 20 gift cards
│   └── rewards.db         the SQLite database
├── tests/
│   └── validation-tests.php
├── css/styles.css
├── images/gift-card.png
├── README.md
├── test-report.md
└── ai-use.txt
```

## Database

Table and column names come from the AirAsia Requirements Document.

- `GIFTCARD`: cardId, cardName, cardType, cardValue, points
- `ACCOUNT`: accountId, userId, accountType, points

ACCOUNT isn't used by any page yet. It's there for redemption in HW4. `userId` will link to the USER table once that's added in HW4.

The requirements document doesn't have an image column for gift cards, so every card uses the same placeholder image, `images/gift-card.png`.

## Validation rules

All of these are checked on the server in `includes/validation.php`, even though the forms also have `required` and number limits.

- Card Name: required, 100 characters max (matches varchar(100))
- Card Type: has to be Retail, Dining, Travel, or Entertainment
- Card Value: a dollar amount over $0 with up to 2 decimals, $10,000 max
- Points Required: a whole number over 0, 1,000,000 max
- card_id: a whole number of 1 or more. Bad IDs get a 400 message and IDs that don't exist get a 404 message.

Every query that uses a form or URL value is a prepared statement, and every value shown on a page goes through `htmlspecialchars()`.

## Use Case Traceability

| Use case | PHP page or pages | Database table | Test evidence |
|---|---|---|---|
| View a List of Gift Cards | card-list.php | GIFTCARD | Opened card-list.php and confirmed all 20 seeded cards showed an image, name, and points, with each name and image linking to card-details.php?card_id=... (T02). |
| View Details of One Gift Card | card-details.php | GIFTCARD | Opened card-details.php?card_id=1 and confirmed it showed Amazon $50 Gift Card with all its attributes and the Update, Delete, and Return links (T03). |
| Add a Gift Card | card-add.php, card-list.php | GIFTCARD | Submitted valid Gift Card data and confirmed the new record appeared on card-list.php (T04). |
| Update a Gift Card | card-update.php, card-details.php | GIFTCARD | Changed card 6 to Starbucks $30 Gift Card and confirmed card-details.php showed the new values (T05). |
| Delete a Gift Card | card-delete.php, card-list.php | GIFTCARD | Opened the delete page, confirmed nothing was deleted until I clicked Delete, then confirmed the card was gone from card-list.php (T06). |
