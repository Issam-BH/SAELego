-- Script pour remplir les traductions de couleurs (name_fr)
-- À exécuter après les modifications de structure

UPDATE `color` SET `name_fr` = 'Blanc' WHERE `name_en` = 'White';
UPDATE `color` SET `name_fr` = 'Noir' WHERE `name_en` = 'Black';
UPDATE `color` SET `name_fr` = 'Gris' WHERE `name_en` = 'Gray';
UPDATE `color` SET `name_fr` = 'Gris clair' WHERE `name_en` = 'Light Gray';
UPDATE `color` SET `name_fr` = 'Gris foncé' WHERE `name_en` = 'Dark Gray';
UPDATE `color` SET `name_fr` = 'Rouge' WHERE `name_en` = 'Red';
UPDATE `color` SET `name_fr` = 'Rose' WHERE `name_en` = 'Pink';
UPDATE `color` SET `name_fr` = 'Magenta' WHERE `name_en` = 'Magenta';
UPDATE `color` SET `name_fr` = 'Orange' WHERE `name_en` = 'Orange';
UPDATE `color` SET `name_fr` = 'Jaune' WHERE `name_en` = 'Yellow';
UPDATE `color` SET `name_fr` = 'Vert' WHERE `name_en` = 'Green';
UPDATE `color` SET `name_fr` = 'Vert clair' WHERE `name_en` = 'Light Green';
UPDATE `color` SET `name_fr` = 'Vert foncé' WHERE `name_en` = 'Dark Green';
UPDATE `color` SET `name_fr` = 'Bleu' WHERE `name_en` = 'Blue';
UPDATE `color` SET `name_fr` = 'Bleu clair' WHERE `name_en` = 'Light Blue';
UPDATE `color` SET `name_fr` = 'Bleu foncé' WHERE `name_en` = 'Dark Blue';
UPDATE `color` SET `name_fr` = 'Cyan' WHERE `name_en` = 'Cyan';
UPDATE `color` SET `name_fr` = 'Pourpre' WHERE `name_en` = 'Purple';
UPDATE `color` SET `name_fr` = 'Marron' WHERE `name_en` = 'Brown';
UPDATE `color` SET `name_fr` = 'Doré' WHERE `name_en` = 'Gold';
UPDATE `color` SET `name_fr` = 'Argenté' WHERE `name_en` = 'Silver';

-- Ajouter des translations pour les pays courants (codes ISO)
-- Cette table peut servir pour le sélecteur de pays
CREATE TABLE IF NOT EXISTS `country_names` (
    `country_code` CHAR(2) PRIMARY KEY,
    `name_en` VARCHAR(100) NOT NULL,
    `name_fr` VARCHAR(100) NOT NULL
);

INSERT IGNORE INTO `country_names` VALUES 
('FR', 'France', 'France'),
('GB', 'United Kingdom', 'Royaume-Uni'),
('DE', 'Germany', 'Allemagne'),
('ES', 'Spain', 'Espagne'),
('IT', 'Italy', 'Italie'),
('BE', 'Belgium', 'Belgique'),
('NL', 'Netherlands', 'Pays-Bas'),
('AT', 'Austria', 'Autriche'),
('CH', 'Switzerland', 'Suisse'),
('SE', 'Sweden', 'Suède'),
('NO', 'Norway', 'Norvège'),
('DK', 'Denmark', 'Danemark'),
('FI', 'Finland', 'Finlande'),
('PL', 'Poland', 'Pologne'),
('CZ', 'Czech Republic', 'République Tchèque'),
('US', 'United States', 'États-Unis'),
('CA', 'Canada', 'Canada'),
('AU', 'Australia', 'Australie'),
('JP', 'Japan', 'Japon'),
('CN', 'China', 'Chine');
