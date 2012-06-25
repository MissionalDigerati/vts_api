

DROP TABLE IF EXISTS `cake_api_video_translator`.`clips`;
DROP TABLE IF EXISTS `cake_api_video_translator`.`master_recordings`;
DROP TABLE IF EXISTS `cake_api_video_translator`.`translation_requests`;


CREATE TABLE `cake_api_video_translator`.`clips` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`translation_request_id` int(11) NOT NULL,
	`audio_file_location` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
	`video_file_location` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
	`completed_file_location` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
	`status` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'PENDING' NOT NULL,
	`created` datetime DEFAULT NULL,
	`modified` datetime DEFAULT NULL,
	`completed` datetime DEFAULT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=MyISAM;

CREATE TABLE `cake_api_video_translator`.`master_recordings` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`translation_request_id` int(11) NOT NULL,
	`title` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
	`language` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
	`final_filename` text CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
	`status` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT 'PENDING',
	`modified` datetime DEFAULT NULL,
	`created` datetime DEFAULT NULL,
	`completed` datetime DEFAULT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=MyISAM;

CREATE TABLE `cake_api_video_translator`.`translation_requests` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`token` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
	`created` datetime DEFAULT NULL,
	`modified` datetime DEFAULT NULL,
	`expires_at` datetime DEFAULT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=latin1,
	COLLATE=latin1_swedish_ci,
	ENGINE=MyISAM;

