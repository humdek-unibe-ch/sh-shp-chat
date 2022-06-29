-- add plugin entry in the plugin table
INSERT IGNORE INTO plugins (name, version) 
VALUES ('chat', 'v1.0.0');

CREATE TABLE IF NOT EXISTS `chat` (
  `id` int(10) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT,
  `id_snd` int(10) UNSIGNED ZEROFILL NOT NULL,
  `id_rcv` int(10) UNSIGNED ZEROFILL DEFAULT NULL,
  `content` longtext NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_rcv_group` int(10) UNSIGNED ZEROFILL NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_snd` (`id_snd`) USING BTREE,
  KEY `id_rcv` (`id_rcv`) USING BTREE,
  KEY `fk_chat_id_rcv_group` (`id_rcv_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `chatRecipiants` (
  `id_users` int(10) UNSIGNED ZEROFILL NOT NULL,
  `id_chat` int(10) UNSIGNED ZEROFILL NOT NULL,
  `id_room_users` int(10) UNSIGNED ZEROFILL DEFAULT NULL,
  `is_new` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_users`,`id_chat`),
  KEY `id_users` (`id_users`),
  KEY `id_chat` (`id_chat`),
  KEY `id_room_users` (`id_room_users`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DELIMITER //
DROP PROCEDURE IF EXISTS add_chat_fks //
CREATE PROCEDURE add_chat_fks()
BEGIN
    IF NOT EXISTS 
	(
		SELECT NULL 
		FROM information_schema.TABLE_CONSTRAINTS
		WHERE
			CONSTRAINT_SCHEMA = DATABASE() AND
			CONSTRAINT_NAME   = 'fk_chat_id_rcv_group' AND
			CONSTRAINT_TYPE   = 'FOREIGN KEY'
	) THEN    
		--
		-- Constraints for table `chat`
		--
		ALTER TABLE `chat`
		  ADD CONSTRAINT `fk_chat_id_rcv_group` FOREIGN KEY (`id_rcv_group`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
		  ADD CONSTRAINT `fk_chat_id_rcv_user` FOREIGN KEY (`id_rcv`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
		  ADD CONSTRAINT `fk_chat_id_send` FOREIGN KEY (`id_snd`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
		--
		-- Constraints for table `chatRecipiants`
		--
		ALTER TABLE `chatRecipiants`
		  ADD CONSTRAINT `chatRecipiants_fk_id_chat` FOREIGN KEY (`id_chat`) REFERENCES `chat` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,		  
		  ADD CONSTRAINT `chatRecipiants_fk_id_users` FOREIGN KEY (`id_users`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
	
    END IF;
END

//

DELIMITER ;

-- Execute the procedure
CALL add_chat_fks();

-- Drop the procedure
DROP PROCEDURE add_chat_fks;

-- add keyword chatSubject
INSERT IGNORE INTO pages (`id`, `keyword`, `url`, `protocol`, `id_actions`, `id_navigation_section`, `parent`, `is_headless`, `nav_position`, `footer_position`, `id_type`) 
VALUES (NULL, 'chatSubject', '/chat/subject/[i:gid]?/[i:uid]?', 'GET|POST', '0000000003', NULL, NULL, '0', NULL, NULL, '0000000003');

-- add full permisitons to admin for chatSubject
INSERT IGNORE INTO `acl_groups` (`id_groups`, `id_pages`, `acl_select`, `acl_insert`, `acl_update`, `acl_delete`) VALUES ((SELECT id FROM groups WHERE name = 'admin'), (SELECT id FROM pages WHERE keyword = 'chatSubject'), '1', '1', '1', '1');

INSERT IGNORE INTO sections (id_styles, name) VALUES(get_style_id('container'), 'chatSubject-container');
INSERT IGNORE INTO sections (id_styles, name) VALUES(get_style_id('chat'), 'chatSubject-chat');
INSERT IGNORE INTO pages_sections (id_pages, id_Sections, position) VALUES((SELECT id FROM pages WHERE keyword = 'chatSubject'), (SELECT id FROM sections WHERE name = 'chatSubject-container'), 1);
INSERT IGNORE INTO sections_hierarchy (parent, child, position) VALUES((SELECT id FROM sections WHERE name = 'chatSubject-container'), (SELECT id FROM sections WHERE name = 'chatSubject-chat'), 1);


-- add keyword chatTherapist
INSERT IGNORE INTO pages (`id`, `keyword`, `url`, `protocol`, `id_actions`, `id_navigation_section`, `parent`, `is_headless`, `nav_position`, `footer_position`, `id_type`) 
VALUES (NULL, 'chatTherapist', '/chat/therapist/[i:gid]?/[i:uid]?', 'GET|POST', '0000000003', NULL, NULL, '0', NULL, NULL, '0000000003');

-- add full permisitons to admin for chatTherapist
INSERT IGNORE INTO `acl_groups` (`id_groups`, `id_pages`, `acl_select`, `acl_insert`, `acl_update`, `acl_delete`) VALUES ((SELECT id FROM groups WHERE name = 'admin'), (SELECT id FROM pages WHERE keyword = 'chatTherapist'), '1', '1', '1', '1');
-- add  permisitons to therapist for chatTherapist
INSERT IGNORE INTO `acl_groups` (`id_groups`, `id_pages`, `acl_select`, `acl_insert`, `acl_update`, `acl_delete`) VALUES ((SELECT id FROM groups WHERE name = 'therapist'), (SELECT id FROM pages WHERE keyword = 'chatTherapist'), '1', '1', '0', '0');

INSERT IGNORE INTO sections (id_styles, name) VALUES(get_style_id('container'), 'chatTherapist-container');
INSERT IGNORE INTO sections (id_styles, name) VALUES(get_style_id('chat'), 'chatTherapist-chat');
INSERT IGNORE INTO pages_sections (id_pages, id_Sections, position) VALUES((SELECT id FROM pages WHERE keyword = 'chatTherapist'), (SELECT id FROM sections WHERE name = 'chatTherapist-container'), 1);
INSERT IGNORE INTO sections_hierarchy (parent, child, position) VALUES((SELECT id FROM sections WHERE name = 'chatTherapist-container'), (SELECT id FROM sections WHERE name = 'chatTherapist-chat'), 1);

-- register hook outputNavRight
INSERT IGNORE INTO `hooks_plugins` (`id_hooks`, `id_plugins`) VALUES ((SELECT id FROM hooks WHERE `name` = 'outputNavRight'), (SELECT id FROM plugins WHERE `name` = 'chat'));