-- Active: 1792846640284@@127.0.0.1@5432@gargote
INSERT INTO categorie (libelle) VALUES ('Goûter') ON CONFLICT (libelle) DO NOTHING;
INSERT INTO plat (nom, prix, id_categorie, description)
SELECT 'Mofo gasy', 200, id, 'Un délicieux mofo gasy'
FROM categorie WHERE libelle = 'Goûter';

INSERT INTO plat (nom, prix, id_categorie, description)
SELECT 'Mokary', 200, id, 'Un délicieux mokary'
FROM categorie WHERE libelle = 'Goûter';

INSERT INTO plat (nom, prix, id_categorie, description)
SELECT 'Koba', 500, id, 'Pâte d''arachide, banane et farine de riz, cuite en feuille de bananier'
FROM categorie WHERE libelle = 'Goûter';

INSERT INTO plat (nom, prix, id_categorie, description)
SELECT 'Godrogodro', 1000, id, 'Un délicieux godrogodro'
FROM categorie WHERE libelle = 'Goûter';

INSERT INTO plat (nom, prix, id_categorie, description)
SELECT 'Menakely', 500, id, 'Un délicieux menakely'
FROM categorie WHERE libelle = 'Goûter';


UPDATE plat SET description = 'Un délicieux vary amin''anana' WHERE nom = 'Vary amin''anana';
UPDATE plat SET description = 'Un délicieux ravitoto sy henakisoa' WHERE nom = 'Ravitoto sy henakisoa';
UPDATE plat SET description = 'Un délicieux akoho sy voanio' WHERE nom = 'Akoho sy voanio';
UPDATE plat SET description = 'Un délicieux romazava' WHERE nom = 'Romazava';
UPDATE plat SET description = 'Un délicieux henakisoa sy tsaramaso' WHERE nom = 'Henakisoa sy tsaramaso';
UPDATE plat SET disponible = FALSE WHERE nom = 'Godrogodro';
UPDATE plat SET disponible = FALSE WHERE nom = 'Menakely';

INSERT INTO plat_du_jour (date_jour, id_plat) VALUES
    (CURRENT_DATE - 6, (SELECT id FROM plat WHERE nom = 'Vary amin''anana')),
    (CURRENT_DATE - 5, (SELECT id FROM plat WHERE nom = 'Ravitoto sy henakisoa')),
    (CURRENT_DATE - 4, (SELECT id FROM plat WHERE nom = 'Akoho sy voanio')),
    (CURRENT_DATE - 3, (SELECT id FROM plat WHERE nom = 'Romazava')),
    (CURRENT_DATE - 2, (SELECT id FROM plat WHERE nom = 'Henakisoa sy tsaramaso')),
    (CURRENT_DATE - 1, (SELECT id FROM plat WHERE nom = 'Soupe chinoise')),
    (CURRENT_DATE, (SELECT id FROM plat WHERE nom = 'Mi sao'));

INSERT INTO plat (nom, prix, id_categorie, description)
SELECT 'pain', 0, id, 'Un délicieux pain'
FROM categorie WHERE libelle = 'Goûter';

INSERT INTO plat (nom, prix, id_categorie, description)
SELECT 'Menakely', 500, id, 'Un délicieux beignet'
FROM categorie WHERE libelle = 'Goûter';