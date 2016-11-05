-- Scrape
-- TODO possibly create some grouping if a set of pages need to be scraped in a single "session" (ie all pages of pagination for a player's levels)
CREATE TABLE `page_scrape` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `url` text NOT NULL,
  `html` longtext NOT NULL,
  `response_code` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Player
CREATE TABLE `player` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `nintendo_id` varchar(255) DEFAULT NULL,
  `name` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nintendo_id` (`nintendo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- TODO may (probably) need to change relationship from many:1 to many:many (use relationship table between snapshot and page_scrape)
CREATE TABLE `player_snapshot` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `page_scrape_id` int(10) unsigned DEFAULT NULL,
  `player_id` int(10) unsigned DEFAULT NULL,
  -- TODO data columns here based on what's on the page
  PRIMARY KEY (`id`),
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Level
CREATE TABLE `level` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `level_code` varchar(19) NOT NULL,
  `player_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `level_code` (`level_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `level_snapshot` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `page_scrape_id` int(10) unsigned DEFAULT NULL,
  `level_id` int(10) unsigned DEFAULT NULL,
  -- TODO data columns here based on what's on the page
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
