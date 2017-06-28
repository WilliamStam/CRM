<?php
$sql = array(
	"DROP TABLE `companies`;",
	"CREATE TABLE `companies` ( `ID` int(6) NOT NULL,  `cID` int(6) DEFAULT NULL,  `userID` int(6) DEFAULT NULL,  `date_in` datetime DEFAULT NULL,  `date_created` datetime DEFAULT NULL,  `_deleted` tinyint(1) DEFAULT 0,  `data` text DEFAULT NULL) ;",
	"ALTER TABLE `companies`  ADD PRIMARY KEY (`ID`),  ADD KEY `userID` (`userID`),  ADD KEY `cID` (`cID`),  ADD KEY `_deleted` (`_deleted`);",
	"ALTER TABLE `companies`  MODIFY `ID` int(6) NOT NULL AUTO_INCREMENT;"


);

?>
