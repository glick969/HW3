-- AirAsia Rewards Redemption System - HW3 schema (SQLite)
-- Table and column names follow the AirAsia Requirements Document.
-- Running this file drops and recreates the tables.

PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS GIFTCARD;
DROP TABLE IF EXISTS ACCOUNT;

-- ACCOUNT holds each customer's reward points (used for redemption in HW4).
-- userId will reference the USER table, which is added in HW4.
CREATE TABLE ACCOUNT (
    accountId   INTEGER PRIMARY KEY AUTOINCREMENT,
    userId      INTEGER      NOT NULL,
    accountType VARCHAR(100) NOT NULL,
    points      INTEGER      NOT NULL CHECK (points >= 0)
);

-- GIFTCARD holds the gift cards customers can redeem points for.
CREATE TABLE GIFTCARD (
    cardId    INTEGER PRIMARY KEY AUTOINCREMENT,
    cardName  VARCHAR(100) NOT NULL,
    cardType  VARCHAR(100) NOT NULL,
    cardValue REAL         NOT NULL CHECK (cardValue > 0),
    points    INTEGER      NOT NULL CHECK (points > 0)
);
