-- AirAsia Rewards Redemption System - HW3 sample data

INSERT INTO ACCOUNT (userId, accountType, points) VALUES
    (1, 'Standard', 12500),
    (2, 'Premium', 48000),
    (3, 'Standard', 3200),
    (4, 'Platinum', 95000),
    (5, 'Premium', 21750);

INSERT INTO GIFTCARD (cardName, cardType, cardValue, points) VALUES
    ('Amazon $50 Gift Card',         'Retail',        50.00,  5000),
    ('Target $25 Gift Card',         'Retail',        25.00,  2500),
    ('Walmart $100 Gift Card',       'Retail',       100.00, 10000),
    ('Best Buy $75 Gift Card',       'Retail',        75.00,  7500),
    ('Apple $50 Gift Card',          'Retail',        50.00,  5000),
    ('Starbucks $25 Gift Card',      'Dining',        25.00,  2500),
    ('Chipotle $20 Gift Card',       'Dining',        20.00,  2000),
    ('Olive Garden $50 Gift Card',   'Dining',        50.00,  5000),
    ('DoorDash $40 Gift Card',       'Dining',        40.00,  4000),
    ('Panera Bread $15 Gift Card',   'Dining',        15.00,  1500),
    ('AirAsia $100 Flight Voucher',  'Travel',       100.00, 10000),
    ('Uber $50 Gift Card',           'Travel',        50.00,  5000),
    ('Airbnb $100 Gift Card',        'Travel',       100.00, 10000),
    ('Hotels.com $150 Gift Card',    'Travel',       150.00, 15000),
    ('Grab $30 Gift Card',           'Travel',        30.00,  3000),
    ('AMC Theatres $25 Gift Card',   'Entertainment', 25.00,  2500),
    ('Netflix $30 Gift Card',        'Entertainment', 30.00,  3000),
    ('Spotify $30 Gift Card',        'Entertainment', 30.00,  3000),
    ('Steam $50 Gift Card',          'Entertainment', 50.00,  5000),
    ('Fandango $20 Gift Card',       'Entertainment', 20.00,  2000);
