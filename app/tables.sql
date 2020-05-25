--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `rates`;
CREATE TABLE `rates` (
  `ID` int(11) NOT NULL,
  `market` varchar(255) DEFAULT NULL,
  `cfrom` varchar(255) DEFAULT NULL,
  `cto` varchar(255) DEFAULT NULL,
  `last` float DEFAULT NULL,
  `created` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `rates`
--
ALTER TABLE `rates`
  ADD PRIMARY KEY (`ID`);