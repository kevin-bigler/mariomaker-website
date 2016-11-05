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

-- Level
CREATE TABLE `level` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `level_code` varchar(19) NOT NULL,
  `player_id` int(10) unsigned DEFAULT NULL,
  `player_nintendo_id` text DEFAULT NULL,
  `title` text DEFAULT NULL,
  `image_url` text DEFAULT NULL,
  `image_full_url` text DEFAULT NULL,
  `upload_date` date DEFAULT NULL,
  `gameskin` text DEFAULT NULL, -- mode (SMB1, SMB2, SMW, NSMB)
  PRIMARY KEY (`id`),
  UNIQUE KEY `level_code` (`level_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `level_snapshot` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `page_scrape_id` int(10) unsigned DEFAULT NULL,
  `level_id` int(10) unsigned DEFAULT NULL,
  `difficulty_rank` text DEFAULT NULL,
  `clear_rate` DECIMAL(5, 2) DEFAULT NULL,
  `number_stars` int(10) unsigned DEFAULT NULL,
  `number_footprints` int(10) unsigned DEFAULT NULL,
  `number_shares` int(10) unsigned DEFAULT NULL,
  `number_clears` int(10) unsigned DEFAULT NULL,
  `number_attempts` int(10) unsigned DEFAULT NULL,
  `number_comments` int(10) unsigned DEFAULT NULL,
  `tag` text DEFAULT NULL,
  `world_record_player_id` int(10) unsigned DEFAULT NULL,
  `world_record_player_nintendo_id` text DEFAULT NULL,
  `world_record_time` text DEFAULT NULL,
  `first_clear_player_id` int(10) unsigned DEFAULT NULL,
  `first_clear_player_nintendo_id` text DEFAULT NULL,
  `recent_players_nintendo_ids` longtext DEFAULT NULL,
  `cleared_by_nintendo_ids` longtext DEFAULT NULL,
  `starred_by_nintendo_ids` longtext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Player
CREATE TABLE `player` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `nintendo_id` varchar(255) NOT NULL,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
