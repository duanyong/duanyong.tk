-- MySQL dump 10.13  Distrib 5.1.49, for debian-linux-gnu (i686)
--
-- Host: localhost    Database: xoxo
-- ------------------------------------------------------
-- Server version	5.1.49-1ubuntu8.1

USE `xoxo`;

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

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `mobile` char(11) NOT NULL DEFAULT '' COMMENT '用户手机',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '用户密码',
  `name` varchar(32) NOT NULL DEFAULT '' COMMENT '用户姓名',
  `sex` boolean NOT NULL DEFAULT false COMMENT '用户性别',
  `email` varchar(128) NOT NULL DEFAULT '' COMMENT '登录邮箱',
  `regip` varchar(32) NOT NULL DEFAULT '' COMMENT '注册IP',
  `count_diary` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '用户发的日记总数',
  `ctime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户注册时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户当前状态',
  PRIMARY KEY (`uid`),
  KEY `email_index` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户信息表';
/*!40101 SET character_set_client = @saved_cs_client */;


DROP TABLE IF EXISTS `diary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `diary` (
  `did` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(10) unsigned NOT NULL default 0 COMMENT '外键，发表日记的用户',
  `date` char(10) NOT NULL DEFAULT '' COMMENT '用户填写的日期',
  `weather` varchar(20) NOT NULL DEFAULT '' COMMENT '用户填写的天气状况',
  `content` varchar(2560) NOT NULL DEFAULT '' COMMENT '日记内容',
  `ctime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发表日记的时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '日记的状态',
  PRIMARY KEY (`did`),
  KEY `uid_index` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户日记表';
/*!40101 SET character_set_client = @saved_cs_client */;


