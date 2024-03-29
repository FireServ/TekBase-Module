SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE IF NOT EXISTS `teklab_voiceserver_backup` (
  `ID` int(11) NOT NULL,
  `kd` varchar(255) NOT NULL,
  `sid` int(11) NOT NULL,
  `date` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `teklab_voiceserver_backup_options` (
  `ID` int(11) NOT NULL,
  `opt` varchar(10) NOT NULL,
  `value` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `teklab_voiceserver_backup_options` (`ID`, `opt`, `value`) VALUES
(1, 'delay', 30),
(2, 'quantity', 50);

ALTER TABLE `teklab_voiceserver_backup`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `teklab_voiceserver_backup_options`
  ADD PRIMARY KEY (`ID`);

ALTER TABLE `teklab_voiceserver_backup`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
  
ALTER TABLE `teklab_voiceserver_backup_options`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;