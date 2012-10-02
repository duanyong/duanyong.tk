-- MySQL dump 10.13  Distrib 5.1.49, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: xoxo
-- ------------------------------------------------------
-- Server version	5.1.49-1ubuntu8.1


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `user`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `201208aiyuji_user` (
    `id`            INT(10)         UNSIGNED    NOT NULL    AUTO_INCREMENT                  COMMENT '主键',
    `username`      CHAR(64)                    NOT NULL    DEFAULT ''                      COMMENT '用户手机',
    `password`      CHAR(32)                    NOT NULL    DEFAULT ''                      COMMENT '用户密码',
    `nickname`      VARCHAR(32)                 NOT NULL    DEFAULT ''                      COMMENT '用户昵称',
    `token`         CHAR(64)                    NOT NULL    DEFAULT ''                      COMMENT '用户标识',
    `firstname`     VARCHAR(32)                 NOT NULL    DEFAULT ''                      COMMENT '用户姓氏',
    `lastname`      VARCHAR(32)                 NOT NULL    DEFAULT ''                      COMMENT '用户名字',
    `sex`           BOOLEAN                     NOT NULL    DEFAULT FALSE                   COMMENT '用户性别',
    `regip`         VARCHAR(32)                 NOT NULL    DEFAULT ''                      COMMENT '注册IP',
    `sum`           INT(11)         UNSIGNED    NOT NULL    DEFAULT '0'                     COMMENT '留言总数',

    `time`          INT(11)         UNSIGNED    NOT NULL    DEFAULT '0'                     COMMENT '注册时间',
    `ftime`         DATETIME                    NOT NULL    DEFAULT '0000-00-00 00:00:00'   COMMENT '注册时间',
    `status`        TINYINT(4)                  NOT NULL    DEFAULT '0'                     COMMENT '当前状态',

    PRIMARY KEY (`id`),
    INDEX `idx_token` (`token`),
    INDEX `idx_username` (`username`),
    INDEX `idx_nickname` (`nickname`),
    INDEX `idx_status` (`status`)

) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户信息表';
/*!40101 SET character_set_client = @saved_cs_client */;


/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `201208aiyuji_words` (
    `id`            INT(10)         UNSIGNED    NOT NULL    AUTO_INCREMENT                  COMMENT '主键',
    `tear`          CHAR(32)                    NOT NULL    DEFAULT ''                      COMMENT '用户COOKIE',
    `token`         CHAR(64)                    NOT NULL    DEFAULT ''                      COMMENT '用户标识',
    `words`         TEXT                        NOT NULL    DEFAULT ''                      COMMENT '留言',
    `key`           VARCHAR(512)                NOT NULL    DEFAULT ''                      COMMENT '关键词',
    `public`        TINYINT(4)      UNSIGNED    NOT NULL    DEFAULT '1'                     COMMENT '是否公开',

    `ip`            VARCHAR(32)                 NOT NULL    DEFAULT ''                      COMMENT '注册IP',
    `time`          INT(11)         UNSIGNED    NOT NULL    DEFAULT '0'                     COMMENT '创建时间',
    `ftime`         DATETIME                    NOT NULL    DEFAULT '0000-00-00 00:00:00'   COMMENT '创建时间',
    `status`        TINYINT(4)                  NOT NULL    DEFAULT '0'                     COMMENT '用户当前状态',

    PRIMARY KEY (`id`),
    INDEX `idx_tear` (`tear`),
    INDEX `idx_token` (`token`),
    INDEX `idx_public` (`public`),
    INDEX `idx_status` (`status`)

) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户信息表';
/*!40101 SET character_set_client = @saved_cs_client */;
