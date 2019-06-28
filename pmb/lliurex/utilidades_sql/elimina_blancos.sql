DROP TABLE IF EXISTS `exemplairesTMP`;
CREATE TABLE IF NOT EXISTS `exemplairesTMP` (
  `expl_id` int(10) unsigned NOT NULL DEFAULT '0',
  `expl_cb` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expl_notice` int(10) unsigned NOT NULL DEFAULT '0',
  `expl_bulletin` int(10) unsigned NOT NULL DEFAULT '0',
  `expl_typdoc` int(5) unsigned NOT NULL DEFAULT '0',
  `expl_cote` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `expl_section` smallint(5) unsigned NOT NULL DEFAULT '0',
  `expl_statut` smallint(5) unsigned NOT NULL DEFAULT '0',
  `expl_location` smallint(5) unsigned NOT NULL DEFAULT '0',
  `expl_codestat` smallint(5) unsigned NOT NULL DEFAULT '0',
  `expl_date_depot` date NOT NULL DEFAULT '0000-00-00',
  `expl_date_retour` date NOT NULL DEFAULT '0000-00-00',
  `expl_note` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `expl_prix` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `expl_owner` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `expl_lastempr` int(10) unsigned NOT NULL DEFAULT '0',
  `last_loan_date` date DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT '2005-01-01 00:00:00',
  `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type_antivol` int(1) unsigned NOT NULL DEFAULT '0',
  `transfert_location_origine` smallint(5) unsigned NOT NULL DEFAULT '0',
  `transfert_statut_origine` smallint(5) unsigned NOT NULL DEFAULT '0',
  `expl_comment` text COLLATE utf8_unicode_ci,
  `expl_nbparts` int(8) unsigned NOT NULL DEFAULT '1',
  `expl_retloc` smallint(5) unsigned NOT NULL DEFAULT '0',
  `expl_abt_num` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO  `exemplairesTMP`
select `expl_id`, trim(expl_cb) , `expl_notice`, `expl_bulletin`, `expl_typdoc`, `expl_cote`, `expl_section`, `expl_statut`, `expl_location`, `expl_codestat`, `expl_date_depot`, `expl_date_retour`, `expl_note`, `expl_prix`, `expl_owner`, `expl_lastempr`, `last_loan_date`, `create_date`, `update_date`, `type_antivol`, `transfert_location_origine`, `transfert_statut_origine`, `expl_comment`, `expl_nbparts`, `expl_retloc`, `expl_abt_num`  FROM `exemplaires`;

drop table `exemplaires`;

rename table `exemplairesTMP` to `exemplaires`;


