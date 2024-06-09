CREATE TABLE `admin_users` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `admin_users` (`id`, `username`, `password_hash`) VALUES
(1, 'admin', '$2y$10$W9hvVqLady2ivV791Nz9zOeqvjASvUTYxlcA9kW25EROz1RgjVsai');


CREATE TABLE IF NOT EXISTS `settings` (
`id` int(32) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `value` varchar(400) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;

INSERT INTO `settings` (`id`, `name`, `value`) VALUES
(1, 'faucet_name', 'Only Faucet Script'),
(2, 'maintenance', 'on'),
(3, 'zerads_id', ''),
(4, 'ptc_status', 'off'),
(5, 'timer', '60'),
(6, 'min_reward', '10000'),
(7, 'max_reward', '20000'),
(8, 'zerochain_api', ''),
(9, 'zerochain_privatekey', ''),
(11, 'claim_enabled', 'yes'),
(14, 'vpn_shield', 'no'),
(15, 'referral_percent', '20'),
(16, 'reverse_proxy', 'no'),
(22, 'iphub_api_key', ''),
(23, 'min_withdrawal_gateway', '100000'),
(26, 'hcaptcha_pub_key', ''),
(27, 'hcaptcha_sec_key', ''), 
(28, 'faucet_currency', 'Zerocoin'),
(31, 'level_system', 'off'),
(32, 'total_claims', '0'),
(33, 'bonusmaxlevel', ''),
(34, 'bonuxlevelxp', ''),
(35, 'bonuslevelvalue', ''),
(36, 'xpreward', '1');

CREATE TABLE IF NOT EXISTS `white_list` (
  `id` int(32) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `transactions` (
`id` int(32) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(32) NOT NULL,
  `type` varchar(50) NOT NULL,
  `amount` decimal(10,8) NOT NULL,
  `timestamp` int(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `users` (
`id` int(32) UNSIGNED NOT NULL AUTO_INCREMENT,
  `address` varchar(75) NOT NULL,
  `ip_address` varchar(50) NOT NULL,
  `balance` decimal(10,8) NOT NULL,
  `refearn` decimal(10,8) NOT NULL,
  `joined` int(32) NOT NULL,
  `level` int(32) DEFAULT 0,
  `xp` int(32) DEFAULT 0,
  `last_activity` int(32) NOT NULL,
  `referred_by` int(32) NOT NULL,
  `last_claim` int(32) NOT NULL,
  `credits` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `withdraw_history` (
  `id` int(32) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid` int(32) NOT NULL,
  `address` varchar(100) NOT NULL,
  `amount` decimal(10,8) NOT NULL,
  `txid` text NOT NULL,
  `timestamp` int(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

