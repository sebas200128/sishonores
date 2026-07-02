-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: honoresbd
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `alumno_padre`
--

DROP TABLE IF EXISTS `alumno_padre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alumno_padre` (
  `id_alumno` int(11) NOT NULL,
  `id_padre` int(11) NOT NULL,
  PRIMARY KEY (`id_alumno`,`id_padre`),
  KEY `id_padre` (`id_padre`),
  CONSTRAINT `alumno_padre_ibfk_1` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`),
  CONSTRAINT `alumno_padre_ibfk_2` FOREIGN KEY (`id_padre`) REFERENCES `padres_familia` (`id_padre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alumno_padre`
--

LOCK TABLES `alumno_padre` WRITE;
/*!40000 ALTER TABLE `alumno_padre` DISABLE KEYS */;
INSERT INTO `alumno_padre` VALUES (561,563),(562,564),(563,565),(564,566),(577,579),(578,580),(579,581),(580,582),(593,595),(594,596),(595,597),(596,598),(609,611),(610,612),(611,613),(612,614),(625,627),(626,628),(627,629),(628,630),(641,643),(642,644),(643,645),(644,646),(657,659),(658,660),(659,661),(660,662),(673,675),(674,676),(675,677),(676,678),(689,691),(690,692),(691,693),(692,694),(705,707),(706,708),(707,709),(708,710),(721,723),(722,724),(723,725),(724,726),(737,739),(738,740),(739,741),(740,742),(753,755),(754,756),(755,757),(756,758),(769,771),(770,772),(771,773),(772,774);
/*!40000 ALTER TABLE `alumno_padre` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `alumnos`
--

DROP TABLE IF EXISTS `alumnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `alumnos` (
  `id_alumno` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_estudiante` varchar(20) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `dni` varchar(8) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `nombre_apoderado` varchar(150) DEFAULT NULL,
  `telefono_apoderado` varchar(15) DEFAULT NULL,
  `email_apoderado` varchar(100) DEFAULT NULL,
  `id_aula` int(11) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_alumno`),
  UNIQUE KEY `codigo_estudiante` (`codigo_estudiante`),
  UNIQUE KEY `dni` (`dni`),
  KEY `id_aula` (`id_aula`),
  CONSTRAINT `alumnos_ibfk_1` FOREIGN KEY (`id_aula`) REFERENCES `aulas_asignadas` (`id_aula`)
) ENGINE=InnoDB AUTO_INCREMENT=785 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alumnos`
--

LOCK TABLES `alumnos` WRITE;
/*!40000 ALTER TABLE `alumnos` DISABLE KEYS */;
INSERT INTO `alumnos` VALUES (561,'EST20260001','Nancy Alicia','Castillo Soto','22985668','2023-06-26','911871313','Rosario Castillo','986974540','rcastillo15@gmail.com',1,1),(562,'EST20260002','Hugo','Silva Vargas','42473453','2023-10-16','964041360','David Silva','970685873','david.silva92@gmail.com',1,1),(563,'EST20260003','Marco','Condori Gomez','82728908','2023-07-27','952656552','Victoria Condori','940330055','vcondori68@gmail.com',1,1),(564,'EST20260004','Ricardo Hernan','Alvarez Flores','70185476','2023-02-17','965901869','Juan Alvarez','958532408','juanalvarez61@hotmail.com',1,1),(577,'EST20260017','Diana Margarita','Rojas Cardenas','69504311','2022-05-04','927550086','Walter Rojas','980930676','walterrojas94@hotmail.com',5,1),(578,'EST20260018','Cesar Javier','Flores Cardenas','24335966','2022-10-17','979831023','Marco Flores','971598541','marcoflores51@hotmail.com',5,1),(579,'EST20260019','Luisa','Flores Rivera','62441760','2022-04-14','961519936','Roberto Flores','921924790','robertoflores10@gmail.com',5,1),(580,'EST20260020','Martha','Silva Campos','51744895','2022-05-15','981237630','Cesar Silva','960010371','cesar.silva09@outlook.com',5,1),(593,'EST20260033','Hernan','Mendoza Cardenas','73255677','2021-04-11','928247235','Luz Mendoza','958014532','luz.mendoza75@outlook.com',9,1),(594,'EST20260034','Manuel','Gonzales Salazar','91675845','2021-11-14','960480316','Luz Gonzales','965406000','luz.gonzales19@outlook.com',9,1),(595,'EST20260035','Victor','Diaz Ramos','11163068','2021-03-04','967423984','Isabel Diaz','950302761','isabel.diaz82@hotmail.com',9,1),(596,'EST20260036','Gabriela','Paredes Delgado','46004665','2021-12-26','951000051','Nancy Paredes','932651136','nparedes31@outlook.com',9,1),(609,'EST20260049','Raul Roberto','Vargas Delgado','38000730','2020-06-18','977672552','Javier Vargas','933222731','javiervargas55@outlook.com',13,1),(610,'EST20260050','Alfredo Julio','Diaz Bustamante','11981873','2020-02-01','984857349','Javier Diaz','925329172','javier.diaz51@outlook.com',13,1),(611,'EST20260051','Margarita','Condori Soto','35269694','2020-06-09','972880206','Victor Condori','915442358','vcondori48@hotmail.com',13,1),(612,'EST20260052','Javier','Flores Leon','72981637','2020-09-06','998916250','Diana Flores','993346558','diana.flores52@outlook.com',13,1),(625,'EST20260065','Yolanda','Rojas Palomino','96045428','2019-12-06','949791078','Carlos Rojas','949540367','crojas11@gmail.com',17,1),(626,'EST20260066','Carmen Rosa','Mendoza Cardenas','33351330','2019-06-15','925323347','Elizabeth Mendoza','940174147','emendoza03@gmail.com',17,1),(627,'EST20260067','Raul','Espinoza Campos','30854048','2019-04-21','955551039','Ana Espinoza','926073315','aespinoza49@hotmail.com',17,1),(628,'EST20260068','Alejandro','Huaman Gomez','53219940','2019-02-20','992473624','Hector Huaman','917792515','hectorhuaman50@gmail.com',17,1),(641,'EST20260081','Gloria','Huaman Flores','12258175','2018-04-27','947633984','Francisca Huaman','921891168','francisca.huaman58@hotmail.com',21,1),(642,'EST20260082','Julia','Vargas Herrera','19793716','2018-06-11','953136869','Oscar Vargas','973060101','oscar.vargas93@hotmail.com',21,1),(643,'EST20260083','Francisco','Condori Flores','10277793','2018-01-11','977622251','Elsa Condori','946004116','econdori95@outlook.com',21,1),(644,'EST20260084','Silvia','Alvarez Soto','45118005','2018-01-20','934792708','Patricia Alvarez','977575589','patriciaalvarez67@outlook.com',21,1),(657,'EST20260097','Rosario Julia','Sanchez Rivera','47903611','2017-12-10','940351188','Javier Sanchez','931189805','javiersanchez59@gmail.com',25,1),(658,'EST20260098','David Walter','Chavez Rivera','22890461','2017-03-19','965915164','Fernando Chavez','984686085','fchavez22@outlook.com',25,1),(659,'EST20260099','Manuel','Paredes Cardenas','53624663','2017-01-08','920920544','Javier Paredes','921851011','j.paredes75@hotmail.com',25,1),(660,'EST20260100','Yolanda Elizabeth','Vargas Mendoza','34835405','2017-03-01','912353764','Elizabeth Vargas','931547025','evargas59@outlook.com',25,1),(673,'EST20260113','Elizabeth','Rojas Mamani','27922320','2016-09-26','923704681','Luisa Rojas','931741426','luisarojas53@gmail.com',29,1),(674,'EST20260114','Juan Angel','Mendoza Villanueva','28841843','2016-02-19','917183315','Edith Mendoza','967691342','edith.mendoza07@gmail.com',29,1),(675,'EST20260115','Alicia','Espinoza Villanueva','37547558','2016-08-26','915174266','Hernan Espinoza','933175139','hernanespinoza72@outlook.com',29,1),(676,'EST20260116','Rosario Maria','Ramos Cardenas','62462876','2016-10-01','978856930','Ana Ramos','962475448','anaramos38@outlook.com',29,1),(689,'EST20260129','Antonia Patricia','Chavez Herrera','53250525','2015-11-02','992600885','Blanca Chavez','929181176','blancachavez88@gmail.com',33,1),(690,'EST20260130','Diana','Huaman Pinto','47707214','2015-01-23','911180036','Julia Huaman','964738955','julia.huaman31@hotmail.com',33,1),(691,'EST20260131','Eduardo Julio','Condori Herrera','17894331','2015-11-09','913484959','Luisa Condori','925175586','lcondori31@hotmail.com',33,1),(692,'EST20260132','Oscar','Mamani Rivera','11640020','2015-10-25','912727796','Hector Mamani','977990718','h.mamani09@hotmail.com',33,1),(705,'EST20260145','Miguel Juan','Rojas Delgado','58820038','2014-11-20','952032840','Elizabeth Rojas','986491274','elizabethrojas69@outlook.com',37,1),(706,'EST20260146','Blanca','Ruiz Ortiz','90053397','2014-06-04','973977069','Fernando Ruiz','920278831','fernandoruiz85@outlook.com',37,1),(707,'EST20260147','Miguel','Espinoza Herrera','99325206','2014-12-06','934141396','Cesar Espinoza','963917801','cesar.espinoza79@gmail.com',37,1),(708,'EST20260148','Jaime','Morales Herrera','44130971','2014-04-16','959202836','Julio Morales','925912675','j.morales93@gmail.com',37,1),(721,'EST20260161','Rosario','Mamani Delgado','26267208','2013-07-08','979382011','Javier Mamani','936088834','javier.mamani20@gmail.com',41,1),(722,'EST20260162','Estela','Rojas Mamani','16342859','2013-04-12','992554818','Martha Rojas','927553415','m.rojas45@outlook.com',41,1),(723,'EST20260163','Antonia','Espinoza Cabrera','81883383','2013-08-08','928686809','Jorge Espinoza','978739423','jorgeespinoza86@hotmail.com',41,1),(724,'EST20260164','Carmen Patricia','Vargas Mendoza','73367092','2013-09-13','995865218','Rosario Vargas','947228816','r.vargas01@hotmail.com',41,1),(737,'EST20260177','Javier','Flores Herrera','86663515','2012-09-17','922808851','Gabriela Flores','991430236','gabriela.flores31@hotmail.com',45,1),(738,'EST20260178','Fernando Carlos','Ruiz Campos','92141146','2012-04-13','913565118','Hugo Ruiz','949067690','hruiz28@gmail.com',45,1),(739,'EST20260179','Ana Blanca','Ramirez Gomez','67431676','2012-01-07','936526065','Luis Ramirez','990693884','luis.ramirez20@hotmail.com',45,1),(740,'EST20260180','Marco','Ruiz Mamani','75408937','2012-09-03','954274714','Silvia Ruiz','949415037','sruiz49@gmail.com',45,1),(753,'EST20260193','Patricia','Espinoza Delgado','13460128','2011-05-27','932330203','Carlos Espinoza','936619834','carlos.espinoza55@hotmail.com',49,1),(754,'EST20260194','Cesar','Morales Aquino','52056462','2011-08-25','981272190','Angel Morales','967760191','amorales12@gmail.com',49,1),(755,'EST20260195','David Pedro','Espinoza Garcia','59522656','2011-01-03','944367518','Jose Espinoza','978054949','j.espinoza73@gmail.com',49,1),(756,'EST20260196','Diego Enrique','Morales Campos','78848474','2011-02-08','974402001','Luisa Morales','949087127','luisamorales99@hotmail.com',49,1),(769,'EST20260209','Jorge','Gutierrez Aquino','53509127','2010-08-25','947013854','Francisco Gutierrez','926302072','franciscogutierrez63@hotmail.com',53,1),(770,'EST20260210','Gabriela','Condori Ortiz','12248292','2010-08-03','982219763','Daniel Condori','941018019','daniel.condori09@hotmail.com',53,1),(771,'EST20260211','Daniel','Condori Garcia','34701999','2010-11-20','914916356','Ana Condori','961153437','acondori80@gmail.com',53,1),(772,'EST20260212','Hector Raul','Alvarez Mamani','32302419','2010-07-16','929418880','Pedro Alvarez','943572460','palvarez56@gmail.com',53,1);
/*!40000 ALTER TABLE `alumnos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `aulas_asignadas`
--

DROP TABLE IF EXISTS `aulas_asignadas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `aulas_asignadas` (
  `id_aula` int(11) NOT NULL AUTO_INCREMENT,
  `id_grado` int(11) NOT NULL,
  `id_seccion` int(11) NOT NULL,
  `anio` year(4) NOT NULL,
  `vacantes` int(11) DEFAULT 30,
  PRIMARY KEY (`id_aula`),
  KEY `id_grado` (`id_grado`),
  KEY `id_seccion` (`id_seccion`),
  CONSTRAINT `aulas_asignadas_ibfk_1` FOREIGN KEY (`id_grado`) REFERENCES `grados` (`id_grado`),
  CONSTRAINT `aulas_asignadas_ibfk_2` FOREIGN KEY (`id_seccion`) REFERENCES `secciones` (`id_seccion`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `aulas_asignadas`
--

LOCK TABLES `aulas_asignadas` WRITE;
/*!40000 ALTER TABLE `aulas_asignadas` DISABLE KEYS */;
INSERT INTO `aulas_asignadas` VALUES (1,1,1,2026,30),(5,2,1,2026,30),(9,3,1,2026,30),(13,4,1,2026,30),(17,5,1,2026,30),(21,6,1,2026,30),(25,7,1,2026,30),(29,8,1,2026,30),(33,9,1,2026,30),(37,10,1,2026,30),(41,11,1,2026,30),(45,12,1,2026,30),(49,13,1,2026,30),(53,14,1,2026,30);
/*!40000 ALTER TABLE `aulas_asignadas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `competencias`
--

DROP TABLE IF EXISTS `competencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `competencias` (
  `id_competencia` int(11) NOT NULL AUTO_INCREMENT,
  `id_curso` int(11) NOT NULL,
  `nombre_competencia` varchar(200) NOT NULL,
  PRIMARY KEY (`id_competencia`),
  KEY `id_curso` (`id_curso`),
  CONSTRAINT `competencias_ibfk_1` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `competencias`
--

LOCK TABLES `competencias` WRITE;
/*!40000 ALTER TABLE `competencias` DISABLE KEYS */;
INSERT INTO `competencias` VALUES (1,9,'Trabajo en equipo'),(2,9,'Tareas Cumplidas'),(3,9,'Examen Final');
/*!40000 ALTER TABLE `competencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cursos`
--

DROP TABLE IF EXISTS `cursos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cursos` (
  `id_curso` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_curso` varchar(100) NOT NULL,
  `codigo_curso` varchar(20) DEFAULT NULL,
  `horas_semanales` int(11) DEFAULT 4,
  `id_nivel` int(11) NOT NULL,
  PRIMARY KEY (`id_curso`),
  KEY `id_nivel` (`id_nivel`),
  CONSTRAINT `cursos_ibfk_1` FOREIGN KEY (`id_nivel`) REFERENCES `niveles` (`id_nivel`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cursos`
--

LOCK TABLES `cursos` WRITE;
/*!40000 ALTER TABLE `cursos` DISABLE KEYS */;
INSERT INTO `cursos` VALUES (1,'Matemática','MAT01',6,2),(2,'Comunicación','COM01',6,2),(3,'Ciencia y Tecnología','CT01',4,2),(4,'Personal Social','PS01',3,2),(5,'Inglés','ING01',3,2),(6,'Arte y Cultura','ART01',2,2),(7,'Educación Física','EF01',2,2),(8,'Religión','REL01',2,2),(9,'Comunicacion','COMUINI03',4,1);
/*!40000 ALTER TABLE `cursos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `docente_curso_aula`
--

DROP TABLE IF EXISTS `docente_curso_aula`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `docente_curso_aula` (
  `id_docente_curso_aula` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_curso` int(11) NOT NULL,
  `id_aula` int(11) NOT NULL,
  `anio` year(4) NOT NULL,
  PRIMARY KEY (`id_docente_curso_aula`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_curso` (`id_curso`),
  KEY `id_aula` (`id_aula`),
  CONSTRAINT `docente_curso_aula_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `docente_curso_aula_ibfk_2` FOREIGN KEY (`id_curso`) REFERENCES `cursos` (`id_curso`),
  CONSTRAINT `docente_curso_aula_ibfk_3` FOREIGN KEY (`id_aula`) REFERENCES `aulas_asignadas` (`id_aula`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `docente_curso_aula`
--

LOCK TABLES `docente_curso_aula` WRITE;
/*!40000 ALTER TABLE `docente_curso_aula` DISABLE KEYS */;
INSERT INTO `docente_curso_aula` VALUES (1,574,9,1,2026);
/*!40000 ALTER TABLE `docente_curso_aula` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `docente_nivel`
--

DROP TABLE IF EXISTS `docente_nivel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `docente_nivel` (
  `id_usuario` int(11) NOT NULL,
  `id_nivel` int(11) NOT NULL,
  PRIMARY KEY (`id_usuario`,`id_nivel`),
  KEY `id_nivel` (`id_nivel`),
  CONSTRAINT `docente_nivel_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `docente_nivel_ibfk_2` FOREIGN KEY (`id_nivel`) REFERENCES `niveles` (`id_nivel`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `docente_nivel`
--

LOCK TABLES `docente_nivel` WRITE;
/*!40000 ALTER TABLE `docente_nivel` DISABLE KEYS */;
INSERT INTO `docente_nivel` VALUES (574,1);
/*!40000 ALTER TABLE `docente_nivel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grados`
--

DROP TABLE IF EXISTS `grados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grados` (
  `id_grado` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_grado` varchar(20) NOT NULL,
  `id_nivel` int(11) NOT NULL,
  `orden` int(11) DEFAULT 0,
  PRIMARY KEY (`id_grado`),
  KEY `id_nivel` (`id_nivel`),
  CONSTRAINT `grados_ibfk_1` FOREIGN KEY (`id_nivel`) REFERENCES `niveles` (`id_nivel`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grados`
--

LOCK TABLES `grados` WRITE;
/*!40000 ALTER TABLE `grados` DISABLE KEYS */;
INSERT INTO `grados` VALUES (1,'3 años',1,1),(2,'4 años',1,2),(3,'5 años',1,3),(4,'1° Grado',2,4),(5,'2° Grado',2,5),(6,'3° Grado',2,6),(7,'4° Grado',2,7),(8,'5° Grado',2,8),(9,'6° Grado',2,9),(10,'1° Año',3,10),(11,'2° Año',3,11),(12,'3° Año',3,12),(13,'4° Año',3,13),(14,'5° Año',3,14);
/*!40000 ALTER TABLE `grados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `niveles`
--

DROP TABLE IF EXISTS `niveles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `niveles` (
  `id_nivel` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_nivel` varchar(50) NOT NULL,
  PRIMARY KEY (`id_nivel`),
  UNIQUE KEY `nombre_nivel` (`nombre_nivel`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `niveles`
--

LOCK TABLES `niveles` WRITE;
/*!40000 ALTER TABLE `niveles` DISABLE KEYS */;
INSERT INTO `niveles` VALUES (1,'Inicial'),(2,'Primaria'),(3,'Secundaria');
/*!40000 ALTER TABLE `niveles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notas`
--

DROP TABLE IF EXISTS `notas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notas` (
  `id_nota` int(11) NOT NULL AUTO_INCREMENT,
  `id_alumno` int(11) NOT NULL,
  `id_competencia` int(11) NOT NULL,
  `id_docente_curso_aula` int(11) NOT NULL,
  `bimestre` tinyint(4) NOT NULL CHECK (`bimestre` in (1,2,3,4)),
  `nota` decimal(5,2) DEFAULT NULL CHECK (`nota` >= 0 and `nota` <= 20),
  `observacion` text DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_nota`),
  KEY `id_alumno` (`id_alumno`),
  KEY `id_competencia` (`id_competencia`),
  KEY `id_docente_curso_aula` (`id_docente_curso_aula`),
  CONSTRAINT `notas_ibfk_1` FOREIGN KEY (`id_alumno`) REFERENCES `alumnos` (`id_alumno`),
  CONSTRAINT `notas_ibfk_2` FOREIGN KEY (`id_competencia`) REFERENCES `competencias` (`id_competencia`),
  CONSTRAINT `notas_ibfk_3` FOREIGN KEY (`id_docente_curso_aula`) REFERENCES `docente_curso_aula` (`id_docente_curso_aula`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notas`
--

LOCK TABLES `notas` WRITE;
/*!40000 ALTER TABLE `notas` DISABLE KEYS */;
INSERT INTO `notas` VALUES (1,561,1,1,1,15.50,'Vas por buen camino','2026-06-26 02:20:29'),(2,561,2,1,1,14.00,'Vas por buen camino','2026-06-26 02:20:29'),(3,561,3,1,1,16.00,'Vas por buen camino','2026-06-26 02:20:29'),(4,562,1,1,1,15.50,'Vas por buen camino','2026-06-26 02:20:29'),(5,562,2,1,1,14.00,'Vas por buen camino','2026-06-26 02:20:29'),(6,562,3,1,1,16.00,'Vas por buen camino','2026-06-26 02:20:29'),(7,564,1,1,1,17.00,'Sigue esforzandote','2026-06-26 02:23:26'),(8,564,2,1,1,14.00,'Sigue esforzandote','2026-06-26 02:23:26'),(9,564,3,1,1,11.00,'Sigue esforzandote','2026-06-26 02:23:26'),(10,563,1,1,1,5.00,'Necesitas prestar atencion en tus clases','2026-06-26 02:23:26'),(11,563,2,1,1,8.00,'Necesitas prestar atencion en tus clases','2026-06-26 02:23:26'),(12,563,3,1,1,10.00,'Necesitas prestar atencion en tus clases','2026-06-26 02:23:26');
/*!40000 ALTER TABLE `notas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `padres_familia`
--

DROP TABLE IF EXISTS `padres_familia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `padres_familia` (
  `id_padre` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `password_generada` varchar(255) NOT NULL,
  PRIMARY KEY (`id_padre`),
  UNIQUE KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `padres_familia_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=787 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `padres_familia`
--

LOCK TABLES `padres_familia` WRITE;
/*!40000 ALTER TABLE `padres_familia` DISABLE KEYS */;
INSERT INTO `padres_familia` VALUES (563,577,'KQhXTiPD'),(564,578,'2G3XxQgm'),(565,579,'FcUL1v6S'),(566,580,'5OYhONVj'),(579,593,'HC0evQ4O'),(580,594,'xHGYLteG'),(581,595,'JgkSBBs5'),(582,596,'Qkp3WoPh'),(595,609,'cPgiAaW8'),(596,610,'IqmgvCyX'),(597,611,'rNLAhpPl'),(598,612,'kXBLiXLV'),(611,625,'x4cLzb3c'),(612,626,'S8O2N8kQ'),(613,627,'8JPlPPGQ'),(614,628,'xjUt2MfY'),(627,641,'yAgBnYco'),(628,642,'qJARNuoO'),(629,643,'2pGtgaPh'),(630,644,'Oz72dkVT'),(643,657,'daZWbbLr'),(644,658,'TklohGpj'),(645,659,'0SlfY1dF'),(646,660,'LSOHZ5F2'),(659,673,'GPOkhoHJ'),(660,674,'KDHZF5gN'),(661,675,'hP8rBAmS'),(662,676,'jYXh4Nib'),(675,689,'B2tqFZsE'),(676,690,'SJydoVtE'),(677,691,'ejWhvdCO'),(678,692,'dhBoQbda'),(691,705,'HW727fex'),(692,706,'cLCIPZJY'),(693,707,'3AU1VOV5'),(694,708,'FGzyn1wY'),(707,721,'0FLGUOUF'),(708,722,'rsBzKLbt'),(709,723,'DTI1a7XW'),(710,724,'BYTHQlY9'),(723,737,'UTCv0dnt'),(724,738,'LVs1airo'),(725,739,'Wl3MJ8Py'),(726,740,'lZ565vc4'),(739,753,'VYXd35aW'),(740,754,'5cGRXLXm'),(741,755,'6UNy5sE0'),(742,756,'qInc3rzs'),(755,769,'WW5mvJgG'),(756,770,'GcA4TsBM'),(757,771,'6lNoYaSO'),(758,772,'5FBpS8yo'),(771,785,'yBd3Trme'),(772,786,'BkjLOhfK'),(773,787,'EDEzz7L8'),(774,788,'szJ7YjoO');
/*!40000 ALTER TABLE `padres_familia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(50) NOT NULL,
  `nivel_acceso` int(11) NOT NULL,
  PRIMARY KEY (`id_rol`),
  UNIQUE KEY `nombre_rol` (`nombre_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'SuperUsuario',10),(2,'Director',9),(3,'Docente',5),(4,'Secretaria',4),(5,'PadreFamilia',2);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `secciones`
--

DROP TABLE IF EXISTS `secciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `secciones` (
  `id_seccion` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_seccion` varchar(10) NOT NULL,
  PRIMARY KEY (`id_seccion`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `secciones`
--

LOCK TABLES `secciones` WRITE;
/*!40000 ALTER TABLE `secciones` DISABLE KEYS */;
INSERT INTO `secciones` VALUES (1,'A');
/*!40000 ALTER TABLE `secciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `dni` varchar(8) NOT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `id_rol` int(11) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `dni` (`dni`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=801 DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Administrador','Sistema','admin@honores.edu.pe','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','12345678',NULL,1,1,'2026-06-20 16:46:28'),(573,'Alejandro','Quispe Mamani','director@honores.edu.pe','$2y$10$/YG2GUBdMg7NMQ1LmytGkOIYcJjtBZeeuBaFNFg5VIl5qw0VN5XHi','09483726','984726153',2,1,'2026-06-20 21:52:38'),(574,'María Elena','Condori Flores','maria.condori@honores.edu.pe','$2y$10$9RQI7grMpxAD3U2JxcpNBefpXV0RWn4jBDvxtD0DSdeiAj87uAUDm','42938475','951827364',3,1,'2026-06-20 21:52:38'),(575,'Patricia Isabel','Ramos Mendoza','secretaria@honores.edu.pe','$2y$10$hvSFnbbdOBHRkkXzLGCs..qbBL/AFOmm/J8HxU.LjrzM/XLY3NRIa','71829384','924736182',4,1,'2026-06-20 21:52:38'),(577,'Rosario Castillo','Apoderado','rcastillo15@gmail.com','$2y$10$lU9u6.l54EqSXaMRsyogtOqGN.KjheV4w78QXaFQFc2cVCU2ETwUO','22985668','986974540',5,1,'2026-06-20 21:52:38'),(578,'David Silva','Apoderado','david.silva92@gmail.com','$2y$10$4jfHvR6fmyu2Y72xUKPagu9.AwrhKi6PVeDminBvkXXLtuuLb21tS','42473453','970685873',5,1,'2026-06-20 21:52:38'),(579,'Victoria Condori','Apoderado','vcondori68@gmail.com','$2y$10$k9Y0RA2oU305WsiTA8AroOIgXUehxar8Hz4bWdBe9iMto8eKu0tAO','82728908','940330055',5,1,'2026-06-20 21:52:38'),(580,'Juan Alvarez','Apoderado','juanalvarez61@hotmail.com','$2y$10$F3VS0bShO4/j24OzoJ/qqeo4l1mj/Mvf/aoQ4r68PoJraCk9asrTa','70185476','958532408',5,1,'2026-06-20 21:52:38'),(593,'Walter Rojas','Apoderado','walterrojas94@hotmail.com','$2y$10$Q6Q1wnrpUCS0222FFHnw2uJ2MF2QwkHkV8TrbPc78OmlxVzLSm42y','69504311','980930676',5,1,'2026-06-20 21:52:38'),(594,'Marco Flores','Apoderado','marcoflores51@hotmail.com','$2y$10$EKrCzqgHMnV81wZcjMQS7.hef6OO.Smf986zPJx1fJ3ioFzRGudTu','24335966','971598541',5,1,'2026-06-20 21:52:38'),(595,'Roberto Flores','Apoderado','robertoflores10@gmail.com','$2y$10$JFv1F1pD72jx48aHOfmLWuRMXTjjiEYQlEf/n/YEDBO4xkia8g47K','62441760','921924790',5,1,'2026-06-20 21:52:38'),(596,'Cesar Silva','Apoderado','cesar.silva09@outlook.com','$2y$10$Rtx2D2bD2pVd.D92o5s0x.eGkWc7yeJhEPvmX4G2w4LI8h2f6ToYe','51744895','960010371',5,1,'2026-06-20 21:52:38'),(609,'Luz Mendoza','Apoderado','luz.mendoza75@outlook.com','$2y$10$c0hgpAOxFJ9Q7H9irK3WG.FBQXsDW3YVYy30g5yDH2zmGQSb1iD7a','73255677','958014532',5,1,'2026-06-20 21:52:38'),(610,'Luz Gonzales','Apoderado','luz.gonzales19@outlook.com','$2y$10$S.F/onYVWW.Nh91KpIsc2OqSUdECAjEs4A/mwxH7ej5LdCijjex8m','91675845','965406000',5,1,'2026-06-20 21:52:38'),(611,'Isabel Diaz','Apoderado','isabel.diaz82@hotmail.com','$2y$10$s2FNhzPgvDkEkzemj7036.UfxTCs4TPjgBQt7Iwp8JUkQXcgJAoRe','11163068','950302761',5,1,'2026-06-20 21:52:38'),(612,'Nancy Paredes','Apoderado','nparedes31@outlook.com','$2y$10$0TAtWMmb6cOqQS5R9KeSpupDiP5Eeq7sRS25Sy/dLbjDzhwLPqFey','46004665','932651136',5,1,'2026-06-20 21:52:38'),(625,'Javier Vargas','Apoderado','javiervargas55@outlook.com','$2y$10$WXASDOF0ckx4HOjZVogynOl0KmeCsZybHIMvCjz7jVl9k83Vp91RC','38000730','933222731',5,1,'2026-06-20 21:52:38'),(626,'Javier Diaz','Apoderado','javier.diaz51@outlook.com','$2y$10$T9i0zQMuzprDDP1Buv/Ap.HbVcVhxQNqQdezxe7v/O1/7DsJzJxa.','11981873','925329172',5,1,'2026-06-20 21:52:38'),(627,'Victor Condori','Apoderado','vcondori48@hotmail.com','$2y$10$MENmuvWsR0GhLXaewp8RiO9RMuyw3Y04/zWQ1WOyKg7zB1aU.F8wG','35269694','915442358',5,1,'2026-06-20 21:52:38'),(628,'Diana Flores','Apoderado','diana.flores52@outlook.com','$2y$10$fS30AIMah1U11uj5qzcvs.ivWVJAuKzc9JaALE9xlrYqFT/UQKIYy','72981637','993346558',5,1,'2026-06-20 21:52:38'),(641,'Carlos Rojas','Apoderado','crojas11@gmail.com','$2y$10$jSpM4YGuCEx9BDJk8Inlv.8wZ7.ohiFB0Rpne5RvGXVIudDUPks42','96045428','949540367',5,1,'2026-06-20 21:52:38'),(642,'Elizabeth Mendoza','Apoderado','emendoza03@gmail.com','$2y$10$6pGla02tSteu6yf/DxuRbOxakHZ2qZMEuVNDcn74LZyRkjFLmfTY2','33351330','940174147',5,1,'2026-06-20 21:52:38'),(643,'Ana Espinoza','Apoderado','aespinoza49@hotmail.com','$2y$10$pq0p0Pv/yOMiJj5sRUPFh.Yoeiv2Ee9KB1L3eyaPhdA68L.ExaQda','30854048','926073315',5,1,'2026-06-20 21:52:38'),(644,'Hector Huaman','Apoderado','hectorhuaman50@gmail.com','$2y$10$/ORHjNrU.BuOPugnmA0Lhe3GT4BYRaK8y7yIptRucbb7Nc75srWKC','53219940','917792515',5,1,'2026-06-20 21:52:38'),(657,'Francisca Huaman','Apoderado','francisca.huaman58@hotmail.com','$2y$10$RObec/klFk7Q.Ai9YULUCOi0EJJAula07eskzQdNTXmohFyAUfLy6','12258175','921891168',5,1,'2026-06-20 21:52:38'),(658,'Oscar Vargas','Apoderado','oscar.vargas93@hotmail.com','$2y$10$JTdm/vtOwX311HAfOGLjXOd8JRorVocRMO98yEg6swGQH01O26/pa','19793716','973060101',5,1,'2026-06-20 21:52:38'),(659,'Elsa Condori','Apoderado','econdori95@outlook.com','$2y$10$AsT1E7aRlnHNp9mq7pHqy.qglUQB9UI92b6Iue7bRUWHMrauQPhfi','10277793','946004116',5,1,'2026-06-20 21:52:38'),(660,'Patricia Alvarez','Apoderado','patriciaalvarez67@outlook.com','$2y$10$i0j7UyeyOW6/i1470qmxE.ftrMT3jHxIJJfdQg4VMElPH8hyp6Txy','45118005','977575589',5,1,'2026-06-20 21:52:38'),(673,'Javier Sanchez','Apoderado','javiersanchez59@gmail.com','$2y$10$yi499jTZI33mdiDqnNzTHOhataFCGiXPnBE2wjCqdYZVacc6CqiPS','47903611','931189805',5,1,'2026-06-20 21:52:38'),(674,'Fernando Chavez','Apoderado','fchavez22@outlook.com','$2y$10$jY2IP4pRRSOTxFJPGleaHO.Nitid7iqy0rFOwBc4hbH14PAw5FBhG','22890461','984686085',5,1,'2026-06-20 21:52:38'),(675,'Javier Paredes','Apoderado','j.paredes75@hotmail.com','$2y$10$Sn6sgrTXLtAYTinAFy647u1hCqVzUPoTehSpXgZfLy6kyEH.HNP9e','53624663','921851011',5,1,'2026-06-20 21:52:38'),(676,'Elizabeth Vargas','Apoderado','evargas59@outlook.com','$2y$10$sZqQVY9rvFG5FpFOI5y7aO5HOuqrmSBLnQNFuJA1Te97BqJxBXPAK','34835405','931547025',5,1,'2026-06-20 21:52:38'),(689,'Luisa Rojas','Apoderado','luisarojas53@gmail.com','$2y$10$rct7mPa3nB6j94t4sFdsBO/yVks9IEx4NoA8YK/lj5YFQQoSGSDmq','27922320','931741426',5,1,'2026-06-20 21:52:38'),(690,'Edith Mendoza','Apoderado','edith.mendoza07@gmail.com','$2y$10$2Fwv0MhqBBR5SpgoCvMs7.sgNlJQ8S4CCw3Xpsb5qK.BVkntZt2ba','28841843','967691342',5,1,'2026-06-20 21:52:38'),(691,'Hernan Espinoza','Apoderado','hernanespinoza72@outlook.com','$2y$10$CVjdfcg9R3asTo/fGTKTOOpYFZE3zWiSpqS0607GdHB/mg/rS41k2','37547558','933175139',5,1,'2026-06-20 21:52:38'),(692,'Ana Ramos','Apoderado','anaramos38@outlook.com','$2y$10$j6uAS1ezrD6y8hZ5ta/f/uTJJQfNtv4VwNrpBVUTN66Pvc3xHNmuq','62462876','962475448',5,1,'2026-06-20 21:52:38'),(705,'Blanca Chavez','Apoderado','blancachavez88@gmail.com','$2y$10$NckE2u/vp31.6VV4NwsTBOr9bA/4kGEPAl1WRMpDpQSgKUqCFk9Lq','53250525','929181176',5,1,'2026-06-20 21:52:38'),(706,'Julia Huaman','Apoderado','julia.huaman31@hotmail.com','$2y$10$mFH08W2KL9JlcGq30HzmGOOrC39WOCqTNDdA31l3Q5h.VEwT/gIA2','47707214','964738955',5,1,'2026-06-20 21:52:38'),(707,'Luisa Condori','Apoderado','lcondori31@hotmail.com','$2y$10$yRLn7Fh9PCOxwv1vstdBmeWBAY3lPaYpdnBHOlcV7cYSeu3ljlG6O','17894331','925175586',5,1,'2026-06-20 21:52:38'),(708,'Hector Mamani','Apoderado','h.mamani09@hotmail.com','$2y$10$WcUpMu34UVb4eWJr9Vkkw.RlMU.hr8dGvulen/iENQb3LoWPaHe0a','11640020','977990718',5,1,'2026-06-20 21:52:38'),(721,'Elizabeth Rojas','Apoderado','elizabethrojas69@outlook.com','$2y$10$WqV0ZIKRCTE..hZNCQuoZ.0cdN71lqi5MPEjELAxwpe3RoCZwvhvm','58820038','986491274',5,1,'2026-06-20 21:52:38'),(722,'Fernando Ruiz','Apoderado','fernandoruiz85@outlook.com','$2y$10$wXGZKbzgmgScK2/j5aNoHuJE.lBKRTaBfJowvjg4StdMrmlW/Gn/S','90053397','920278831',5,1,'2026-06-20 21:52:38'),(723,'Cesar Espinoza','Apoderado','cesar.espinoza79@gmail.com','$2y$10$E51rVMGcjbMrpWv4/6JFZeMh5TOcR2AajOdRINSLAhIllNxqbl9tG','99325206','963917801',5,1,'2026-06-20 21:52:38'),(724,'Julio Morales','Apoderado','j.morales93@gmail.com','$2y$10$2.Po..jgAg60BMKytDrIYeqTLIR0hjYjm5gEehoyrrL.sDep6i.Uq','44130971','925912675',5,1,'2026-06-20 21:52:38'),(737,'Javier Mamani','Apoderado','javier.mamani20@gmail.com','$2y$10$C6sjw/7eV0cP9dldbGZD7OYtl7jTwGl1urmi4BpbPeBmIBTtuQh.6','26267208','936088834',5,1,'2026-06-20 21:52:38'),(738,'Martha Rojas','Apoderado','m.rojas45@outlook.com','$2y$10$apJRTYEX953lSR0ner0D8u04M4E6GWVL8CMKRdIvkCooQPrDuz9Z2','16342859','927553415',5,1,'2026-06-20 21:52:38'),(739,'Jorge Espinoza','Apoderado','jorgeespinoza86@hotmail.com','$2y$10$/2cK2We4q0Fu9Xeh7tXLL.6M.FRrVq3QDpGt3KEx7P6UYmkZgk.pO','81883383','978739423',5,1,'2026-06-20 21:52:38'),(740,'Rosario Vargas','Apoderado','r.vargas01@hotmail.com','$2y$10$7UcnRvTtgk3mjek7I1dFbu1XjAxhTtTdhDIYc2.EgROKZJNBvk5.O','73367092','947228816',5,1,'2026-06-20 21:52:38'),(753,'Gabriela Flores','Apoderado','gabriela.flores31@hotmail.com','$2y$10$9N8WBYvatbACysDBn1hp5upKy5I2lVPes80aMMQxKRXJVVivLmqye','86663515','991430236',5,1,'2026-06-20 21:52:38'),(754,'Hugo Ruiz','Apoderado','hruiz28@gmail.com','$2y$10$7GQbMHwmhjifdR3xn0eXoOmDYyJet7EJj7CgzZflYU6..6k.8iCe.','92141146','949067690',5,1,'2026-06-20 21:52:38'),(755,'Luis Ramirez','Apoderado','luis.ramirez20@hotmail.com','$2y$10$HrBtDG9ieeQxfWraT8axMuOrx9CL.7y.VkIZF0we2H150t40JbE66','67431676','990693884',5,1,'2026-06-20 21:52:38'),(756,'Silvia Ruiz','Apoderado','sruiz49@gmail.com','$2y$10$GPjuPClTe8sPUKFeb3QHiOv9LAF9.ZZ8ztYY8PNDwI907nmJxJlSe','75408937','949415037',5,1,'2026-06-20 21:52:38'),(769,'Carlos Espinoza','Apoderado','carlos.espinoza55@hotmail.com','$2y$10$aEuLxQEd6D5s1IDS0bmUG.5Kwy2Z86ihASUZuawC9WcpJEiYtJNC2','13460128','936619834',5,1,'2026-06-20 21:52:38'),(770,'Angel Morales','Apoderado','amorales12@gmail.com','$2y$10$zeUpg5OdppRCxnNHy0JiYuYTsvKK6OQ7t7c85Ii2eLlwlVPPj66.G','52056462','967760191',5,1,'2026-06-20 21:52:38'),(771,'Jose Espinoza','Apoderado','j.espinoza73@gmail.com','$2y$10$D.JhVB9CWRQwVhgIypV4SOcAfxLXOf.DaosjtFlNYJm2QI4GKIS3G','59522656','978054949',5,1,'2026-06-20 21:52:38'),(772,'Luisa Morales','Apoderado','luisamorales99@hotmail.com','$2y$10$ucsaQqNUFjMXN1M42GJhG.g3aq4K928XLl8lTdEg9NBhal1DHKTcy','78848474','949087127',5,1,'2026-06-20 21:52:38'),(785,'Francisco Gutierrez','Apoderado','franciscogutierrez63@hotmail.com','$2y$10$sLpsrCvAr5//clBl7n916ePsRuE9llDY3af5.nRlzqk5sijBImMbK','53509127','926302072',5,1,'2026-06-20 21:52:38'),(786,'Daniel Condori','Apoderado','daniel.condori09@hotmail.com','$2y$10$rZ/Y37j6g9RdJzcUlC3DcuTvCs76..mL1VoGVnrvVioLvq1byDrGC','12248292','941018019',5,1,'2026-06-20 21:52:38'),(787,'Ana Condori','Apoderado','acondori80@gmail.com','$2y$10$447E4eRoRPYAKxnsT9U.HO9jQXDchhhF9O5BiATglZHn0e6Ngqd6q','34701999','961153437',5,1,'2026-06-20 21:52:38'),(788,'Pedro Alvarez','Apoderado','palvarez56@gmail.com','$2y$10$Tf9Ef7Zig4FGTlqqYZEUEOa2cRTZNa6gEE2PZxzYBHr3AXfRrpwXa','32302419','943572460',5,1,'2026-06-20 21:52:38');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-01 22:13:38
