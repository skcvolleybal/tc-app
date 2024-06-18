# tc-app
De applicatie voor de TC van SKC.

# Installation instructions
0. Ensure Node.js and Node Package Manager are installed
1. Run `npm install` to pull all required Node packages
2. Run `composer install` and `composer dump-autoload` to pull PHP packages and activate the class autoloader  
3. Duplicate the .env.example file and rename it to .env. Open the file and fill in the right variables for your environment. 
4. TC-app does not by itself create the three required tables in the database (tcapp_players, tcapp_player_types, tcapp_teams). Make sure the database you specified in your .env has these tables. Run the following commands to create the database tables:

```
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


CREATE TABLE `tcapp_players` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `interesse_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `training_id` int(11) NOT NULL,
  `facebook_url` varchar(128) DEFAULT NULL,
  `information` text DEFAULT NULL,
  `date_modified` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

CREATE TABLE `tcapp_player_types` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `tcapp_player_types` (`id`, `name`) VALUES
(1, 'Spelverdeler'),
(2, 'Midden'),
(3, 'Passer-loper'),
(4, 'Diagonaal'),
(5, 'Libero'),
(6, 'Nog Niets'),
(7, 'Trainingslid'),
(8, 'Interesse'),
(9, 'Uitgeschreven'),

CREATE TABLE `tcapp_teams` (
  `id` int(11) NOT NULL,
  `type` enum('training','team') NOT NULL,
  `name` varchar(64) NOT NULL,
  `training_info` varchar(512) DEFAULT NULL,
  `sequence` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

INSERT INTO `tcapp_teams` (`id`, `type`, `name`, `training_info`, `sequence`) VALUES
(1, 'team', 'Geen Team', NULL, 1),
(2, 'training', 'Geen Trainingsgroep', NULL, 15),
(1000, 'team', 'Heren 1', '', 18),
(1001, 'team', 'Heren 2', '', 19),
(1002, 'team', 'Heren 3', '', 20),
(1003, 'team', 'Heren 4', '', 21),
(1004, 'team', 'Heren 5', '', 22),
(1005, 'team', 'Heren 6', '', 23),
(1006, 'team', 'Heren 7', '', 24),
(1007, 'team', 'Heren 8', '', 25),
(1008, 'team', 'Dames 1', '', 2),
(1009, 'team', 'Dames 2', '', 3),
(1011, 'team', 'Dames 4', '', 5),
(1012, 'team', 'Dames 5', '', 6),
(1013, 'team', 'Dames 6', '', 7),
(1014, 'team', 'Dames 7', '', 8),
(1015, 'team', 'Dames 8', '', 9),
(1016, 'team', 'Dames 9', '', 10),
(1017, 'team', 'Dames 10', '', 11),
(1018, 'team', 'Dames 11', '', 12),
(1019, 'team', 'Dames 12', '', 13),
(1020, 'team', 'Dames 13', '', 14),
(1021, 'team', 'Dames 14', '', 15),
(1022, 'team', 'Dames 15', '', 16),
(1037, 'team', 'Dames 3', '', 4),
(1041, 'training', 'Heren A', '', 10),
(1042, 'training', 'Heren B', '', 11),
(1043, 'training', 'Heren C', '', 12),
(1044, 'training', 'Heren D', '', 13),
(1045, 'training', 'Dames A', '', 1),
(1046, 'training', 'Dames B', '', 2),
(1047, 'training', 'Dames C', '', 3),
(1048, 'training', 'Dames D', '', 4),
(1049, 'training', 'Dames H', '', 8),
(1050, 'training', 'Dames G', '', 7),
(1051, 'training', 'Dames E', '', 5),
(1052, 'training', 'Dames F', '', 6),
(1053, 'training', 'Heren E', '', 14),
(1054, 'team', 'Heren 9', '', 26),
(1056, 'training', 'Dames I', NULL, 9),
(1057, 'training', 'Heren F', NULL, 16),
(1060, 'team', 'Dames Laag Over', NULL, 27);

ALTER TABLE `tcapp_players`
  ADD PRIMARY KEY (`id`),
  ADD KEY `team_id` (`team_id`),
  ADD KEY `training_id` (`training_id`),
  ADD KEY `type_id` (`type_id`);


ALTER TABLE `tcapp_player_types`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `tcapp_teams`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `tcapp_players`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1798;

ALTER TABLE `tcapp_player_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

ALTER TABLE `tcapp_teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1061;

ALTER TABLE `tcapp_players`
  ADD CONSTRAINT `FK_TEAM_ID` FOREIGN KEY (`team_id`) REFERENCES `tcapp_teams` (`id`),
  ADD CONSTRAINT `FK_TRAINING_ID` FOREIGN KEY (`training_id`) REFERENCES `tcapp_teams` (`id`),
  ADD CONSTRAINT `FK_TYPE_ID` FOREIGN KEY (`type_id`) REFERENCES `tcapp_player_types` (`id`);
COMMIT;

```
