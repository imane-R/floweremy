-- Inserting categories
INSERT INTO category (id, name, slug) VALUES 
(1, 'Roses', 'roses'),
(2, 'Tulipes', 'tulipes'),
(3, 'Orchidées', 'orchidees'),
(4, 'Lys', 'lys'),
(5, 'Tournesols', 'tournesols');

-- Inserting products for Roses category
INSERT INTO product (id, name, stripe_id, price, stock, color, size, weight, description, category_id, image) VALUES 
(1, 'Roses Rouges Passion', 'prod_0001', 29.99, 50, 'Rouge', 'Moyen', 1.2, 'Les roses rouges sont synonymes d\'amour et de passion. Leur couleur vibrante et leur parfum enivrant en font un choix idéal pour exprimer vos sentiments les plus profonds.', 1, 'roses_rouges_passion.jpg'),
(2, 'Roses Blanches Pures', 'prod_0002', 27.99, 40, 'Blanc', 'Moyen', 1.1, 'Les roses blanches représentent la pureté et l\'innocence. Ce bouquet est parfait pour des occasions comme les mariages ou les baptêmes.', 1, 'roses_blanches_pures.jpg'),
(3, 'Roses Jaunes Solaires', 'prod_0003', 28.99, 45, 'Jaune', 'Moyen', 1.3, 'Les roses jaunes symbolisent la joie et l\'amitié. Offrez ce bouquet pour illuminer la journée de quelqu\'un.', 1, 'roses_jaunes_solaires.jpg'),
(4, 'Roses Roses Douces', 'prod_0004', 26.99, 35, 'Rose', 'Moyen', 1.2, 'Les roses roses évoquent la douceur et l\'affection. Elles sont parfaites pour exprimer des sentiments d\'amour tendre.', 1, 'roses_roses_douces.jpg'),
(5, 'Roses Multicolores Joyeuses', 'prod_0005', 32.99, 30, 'Multicolore', 'Moyen', 1.4, 'Ce bouquet de roses multicolores est un véritable feu d\'artifice de couleurs, parfait pour célébrer la vie et les moments heureux.', 1, 'roses_multicolores_joyeuses.jpg');

-- Inserting products for Tulipes category
INSERT INTO product (id, name, stripe_id, price, stock, color, size, weight, description, category_id, image) VALUES 
(6, 'Tulipes Roses Délicates', 'prod_0006', 24.99, 40, 'Rose', 'Moyen', 1.0, 'Les tulipes roses sont le symbole de la tendresse et de l\'amour discret. Leur couleur douce apporte calme et sérénité.', 2, 'tulipes_roses_delicates.jpg'),
(7, 'Tulipes Jaunes Éclatantes', 'prod_0007', 25.99, 35, 'Jaune', 'Moyen', 1.1, 'Les tulipes jaunes symbolisent l\'énergie et la positivité. Elles sont idéales pour apporter une touche de soleil à votre intérieur.', 2, 'tulipes_jaunes_eclatantes.jpg'),
(8, 'Tulipes Blanches Pures', 'prod_0008', 23.99, 30, 'Blanc', 'Moyen', 0.9, 'Les tulipes blanches sont un symbole de pureté et d\'élégance. Ce bouquet est parfait pour des moments solennels.', 2, 'tulipes_blanches_pures.jpg'),
(9, 'Tulipes Rouges Vibrantes', 'prod_0009', 26.99, 50, 'Rouge', 'Moyen', 1.0, 'Les tulipes rouges représentent la passion et le courage. Offrez ce bouquet pour exprimer vos sentiments ardents.', 2, 'tulipes_rouges_vibrantes.jpg'),
(10, 'Tulipes Violettes Royales', 'prod_0010', 27.99, 40, 'Violet', 'Moyen', 1.1, 'Les tulipes violettes sont associées à la royauté et au mystère. Elles apportent une touche de sophistication à n\'importe quel espace.', 2, 'tulipes_violettes_royales.jpg');

-- Inserting products for Orchidées category
INSERT INTO product (id, name, stripe_id, price, stock, color, size, weight, description, category_id, image) VALUES 
(11, 'Orchidées Blanches Élégantes', 'prod_0011', 49.99, 20, 'Blanc', 'Grand', 2.0, 'Les orchidées blanches sont synonymes d\'élégance et de pureté. Elles sont parfaites pour des occasions spéciales comme les mariages ou les anniversaires.', 3, 'orchidees_blanches_elegantes.jpg'),
(12, 'Orchidées Roses Élégantes', 'prod_0012', 47.99, 15, 'Rose', 'Grand', 1.8, 'Les orchidées roses sont un symbole de beauté et de grâce. Ce bouquet est idéal pour exprimer votre amour et votre affection.', 3, 'orchidees_roses_elegantes.jpg'),
(13, 'Orchidées Jaunes Exotiques', 'prod_0013', 48.99, 25, 'Jaune', 'Grand', 2.1, 'Les orchidées jaunes sont rares et exotiques. Elles représentent la force et la détermination, parfaites pour un message fort.', 3, 'orchidees_jaunes_exotiques.jpg'),
(14, 'Orchidées Violettes Mystérieuses', 'prod_0014', 50.99, 10, 'Violet', 'Grand', 2.2, 'Les orchidées violettes incarnent le mystère et la sophistication. Elles sont idéales pour une touche de luxe.', 3, 'orchidees_violettes_mysterieuses.jpg'),
(15, 'Orchidées Multicolores Éblouissantes', 'prod_0015', 52.99, 18, 'Multicolore', 'Grand', 2.3, 'Ce bouquet d\'orchidées multicolores est un véritable spectacle visuel. Chaque fleur apporte une touche unique de couleur et de beauté.', 3, 'orchidees_multicolores_eblouissantes.jpg');

-- Inserting products for Lys category
INSERT INTO product (id, name, stripe_id, price, stock, color, size, weight, description, category_id, image) VALUES 
(16, 'Lys Blancs Purificateurs', 'prod_0016', 34.99, 30, 'Blanc', 'Grand', 1.8, 'Les lys blancs symbolisent la pureté et la spiritualité. Ce bouquet est parfait pour des occasions solennelles et des célébrations religieuses.', 4, 'lys_blancs_purificateurs.jpg'),
(17, 'Lys Roses Envoûtants', 'prod_0017', 36.99, 28, 'Rose', 'Grand', 1.9, 'Les lys roses sont le symbole de la passion douce et de l\'amour. Leur parfum envoûtant ajoutera une touche de romantisme à n\'importe quel moment.', 4, 'lys_roses_envoutants.jpg'),
(18, 'Lys Jaunes Solaires', 'prod_0018', 33.99, 35, 'Jaune', 'Grand', 1.7, 'Les lys jaunes sont un rayon de soleil dans un bouquet. Ils représentent la joie, le bonheur et la prospérité.', 4, 'lys_jaunes_solaires.jpg'),
(19, 'Lys Orange Intenses', 'prod_0019', 35.99, 25, 'Orange', 'Grand', 2.0, 'Les lys orange symbolisent la confiance et la passion. Ce bouquet est idéal pour exprimer des sentiments puissants et affirmés.', 4, 'lys_orange_intenses.jpg'),
(20, 'Lys Multicolores Magiques', 'prod_0020', 38.99, 20, 'Multicolore', 'Grand', 2.1, 'Ce bouquet de lys multicolores est un véritable enchantement. Chaque fleur est un mélange unique de couleurs, créant un bouquet magique.', 4, 'lys_multicolores_magiques.jpg');

-- Inserting products for Tournesols category
INSERT INTO product (id, name, stripe_id, price, stock, color, size, weight, description, category_id, image) VALUES 
(21, 'Tournesols Ensoleillés', 'prod_0021', 22.99, 45, 'Jaune', 'Grand', 2.5, 'Les tournesols sont des fleurs qui respirent la joie et l\'énergie positive. Ce bouquet est parfait pour égayer la journée de quelqu\'un.', 5, 'tournesols_ensoleilles.jpg'),
(22, 'Tournesols Géants', 'prod_0022', 24.99, 40, 'Jaune', 'Très Grand', 3.0, 'Les tournesols géants sont imposants et majestueux. Ils symbolisent la force, l\'énergie et la chaleur.', 5, 'tournesols_geants.jpg'),
(23, 'Tournesols Miniatures', 'prod_0023', 19.99, 50, 'Jaune', 'Petit', 1.5, 'Les tournesols miniatures sont adorables et apportent une touche de soleil même dans les petits espaces.', 5, 'tournesols_miniatures.jpg'),
(24, 'Tournesols Orange Vibrants', 'prod_0024', 23.99, 30, 'Orange', 'Grand', 2.8, 'Les tournesols orange sont rares et vibrants. Ils apportent une énergie dynamique et une chaleur intense.', 5, 'tournesols_orange_vibrants.jpg'),
(25, 'Tournesols Sauvages', 'prod_0025', 26.99, 25, 'Multicolore', 'Grand', 3.1, 'Ce bouquet de tournesols sauvages est un hommage à la nature dans toute sa splendeur. Chaque fleur est unique et apporte une touche rustique.', 5, 'tournesols_sauvages.jpg');
