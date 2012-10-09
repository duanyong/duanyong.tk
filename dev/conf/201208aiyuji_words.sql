-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 10, 2012 at 02:09 AM
-- Server version: 5.5.24
-- PHP Version: 5.3.10-1ubuntu3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `201208aiyuji_words`
--

CREATE TABLE IF NOT EXISTS `201208aiyuji_words` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `sid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发送者',
  `tid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '接收者',
  `words` text NOT NULL COMMENT '留言',
  `key` varchar(512) NOT NULL DEFAULT '' COMMENT '关键词',
  `public` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '是否公开',
  `ip` varchar(32) NOT NULL DEFAULT '' COMMENT '注册IP',
  `time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户当前状态',
  PRIMARY KEY (`id`),
  KEY `idx_sid` (`sid`),
  KEY `idx_tid` (`tid`),
  KEY `idx_public` (`public`),
  KEY `idx_time` (`time`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户说说表' AUTO_INCREMENT=11 ;

--
-- Dumping data for table `201208aiyuji_words`
--

INSERT INTO `201208aiyuji_words` (`id`, `sid`, `tid`, `words`, `key`, `public`, `ip`, `time`, `status`) VALUES
(1, 1, 2, '亲爱的，这是爱语记第一条信息。两年之后咱们见。', '', 1, '10.212.17.99', 1345575998, 0),
(2, 1, 2, '亲爱的，不要忘记我们之后的约定。', '', 1, '10.212.17.99', 1345576141, 0),
(3, 0, 3, '对不起！想一直留在你们身边', '', 1, '114.112.26.107', 1345576472, 0),
(4, 0, 3, '对不起！想一直留在你们身边', '', 1, '114.112.26.107', 1345576488, 0),
(5, 1, 2, '今天手机没带，在路上一直想着怎么办？到公司之后却一直忙着连个电话都没有给你打。实在对不起。\r\n\r\n最后还是借同事的手机在中午吃饭时给你打了个电话。宝，sorry。', '', 1, '10.212.18.202', 1345614273, 0),
(6, 1, 2, '亲爱的，今天把公司邮箱配置到秋秋邮箱了，以后我们可以一块儿看了。抱抱。', '', 1, '10.212.16.78', 1345693994, 0),
(7, 1, 2, '那么多的爱，什么时候全能写得完？也许像朋友说的，一辈子就只够爱一人！', '', 1, '10.212.18.122', 1346211487, 0),
(8, 1, 2, '第一次和我妈通话，什么感觉啊？抱抱，这辈子咱们就要这么过了啊。', '', 1, '172.16.51.30', 1346603569, 0),
(9, 1, 2, '亲爱的，现在还没睡啊。', '', 1, '127.0.0.1', 1349805994, 0),
(10, 1, 3, '这是小玉给你们的，好好保重。', '', 1, '127.0.0.1', 1349806034, 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
