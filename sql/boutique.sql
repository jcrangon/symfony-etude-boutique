--
-- Déchargement des données de la table `carousel`
--

INSERT INTO `carousel` (`id`, `image`, `position`, `emplacement`) VALUES
(1, '2.jpg', 2, 'home'),
(2, '1.jpg', 1, 'home'),
(3, '3.jpg', 3, 'home');

-- --------------------------------------------------------

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`id`, `nom`) VALUES
(1, 't-shirt'),
(2, 'chemise'),
(3, 'pull');

--
-- Déchargement des données de la table `produit`
--

INSERT INTO `produit` (`id`, `categorie_id`, `reference`, `titre`, `description`, `couleur`, `taille`, `sexe`, `photo`, `prix`, `stock`) VALUES
(1, 1, '11-d-23', 'Tshirt Col V', 'Tee-shirt en coton flammé liseré contrastant', 'bleu', 'M', 'm', '100_bleu.jpg', 20, 53),
(2, 1, '66-f-15', 'Tshirt Col V rouge', 'c\\\'est vraiment un super tshirt en soir&eacute;e !', 'rouge', 'L', 'm', '102_rouge.png.jpg', 15, 230),
(3, 1, '88-g-77', 'Tshirt Col rond vert', 'Il vous faut ce tshirt Made In France !!!', 'vert', 'L', 'm', '103_vert.png', 29, 63),
(4, 1, '55-b-38', 'Tshirt jaune', 'e jaune reviens &agrave; la mode, non? :-)', 'jaune', 'S', 'm', '101_jaune.png', 20, 3),
(5, 1, '31-p-33', 'Tshirt noir original', 'voici un tshirt noir tr&egrave;s original :p', 'noir', 'XL', 'm', '2332_full_t-shirt.jpg', 25, 80),
(6, 2, '56-a-65', 'Chemise Blanche', 'Les chemises c\\\'est bien mieux que les tshirts', 'blanc', 'L', 'm', '105_chemiseblanchem.jpg', 49, 73),
(7, 2, '63-s-63', 'Chemise Noir', 'Comme vous pouvez le voir c\\\'est une chemise noir...', 'blanc', 'M', 'm', '106_chemisenoirm.jpg', 59, 120),
(8, 3, '77-p-79', 'Pull gris', 'Pull gris pour l\\\'hiver', 'gris', 'XL', 'f', '104_pullgrism2.jpg', 79, 99),
(9, 1, '15-kjf-85', 'T-shirt homme à personnaliser', 'Superbe tshirt noir personnalisable!', 'noir', 'L', 'm', '1648068059_tshirt_noir_l.webp', 16, 15);

