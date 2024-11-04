
-- MySQL dump 10.13  Distrib 8.0.17, for el7 (x86_64)
--∑√Œ ”√ªß/√‹¬Î:oa/oa!Cfit
-- Host: localhost    Database: cfitpms
-- ------------------------------------------------------
-- Server version       8.0.17

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Temporary view structure for view `view_projectcodename`
--

DROP TABLE IF EXISTS `view_projectcodename`;
/*!50001 DROP VIEW IF EXISTS `view_projectcodename`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_projectcodename` AS SELECT
 1 AS `id`,
 1 AS `name`,
 1 AS `status`,
 1 AS `bearDept`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `view_projectcodename`
--

/*!50001 DROP VIEW IF EXISTS `view_projectcodename`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `view_projectcodename` AS select `zt_project`.`id` AS `id`,concat(`zt_project`.`code`,'_',`zt_project`.`name`) AS `name`,`zt_project`.`status` AS `status`,`zt_projectplan`.`bearDept` AS `bearDept` from (`zt_project` left join `zt_projectplan` on((`zt_project`.`id` = `zt_projectplan`.`project`))) where ((`zt_project`.`type` = 'project') and (`zt_project`.`deleted` = '0')) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-09-23 10:45:54

DROP VIEW IF EXISTS `view_depName`;
CREATE  VIEW `view_depName` AS  SELECT zt_dept.id, zt_dept.`name` FROM zt_dept;