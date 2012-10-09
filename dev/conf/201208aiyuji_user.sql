-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 10, 2012 at 02:10 AM
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
-- Table structure for table `201208aiyuji_user`
--

CREATE TABLE IF NOT EXISTS `201208aiyuji_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `token` char(64) NOT NULL DEFAULT '' COMMENT '用户标识',
  `username` char(64) NOT NULL DEFAULT '' COMMENT '用户手机',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '用户密码',
  `nickname` varchar(32) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `firstname` varchar(32) NOT NULL DEFAULT '' COMMENT '用户姓氏',
  `lastname` varchar(32) NOT NULL DEFAULT '' COMMENT '用户名字',
  `sex` tinyint(1) NOT NULL DEFAULT '0' COMMENT '用户性别',
  `regip` varchar(32) NOT NULL DEFAULT '' COMMENT '注册IP',
  `sum` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '留言总数',
  `regtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '通过reg方式注册的时间',
  `tokentime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '通过token方式注册的时间',
  `nicktime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '通过nickname方式注册的时间',
  `time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '当前状态',
  PRIMARY KEY (`id`),
  KEY `idx_token` (`token`),
  KEY `idx_username` (`username`),
  KEY `idx_nickname` (`nickname`),
  KEY `idx_status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户信息表' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `201208aiyuji_user`
--

INSERT INTO `201208aiyuji_user` (`id`, `token`, `username`, `password`, `nickname`, `firstname`, `lastname`, `sex`, `regip`, `sum`, `regtime`, `tokentime`, `nicktime`, `time`, `status`) VALUES
(1, 'sbyldrnrpgmyplenvssyoragqcmleqqrlgklidruwfxudsocgvkktpftqxqmk', 'dd-up@hotmail.com', '54bf525e28a75bc57830248edb256339', '啊段', '', '', 0, '', 2, '2012-10-10 02:03:26', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1349805806, 4),
(2, 'sbyldrnrpgmyplenvssyoragqcmleqqrlgklidruwfxudsocgvkktpftqxqmk', 'oxzifkgrtuktembaizsfemnxqvweqyqegyunozjtjceaoognsddqoozbzztb', 'f386fda3743db0e6fe37f4bcbe1c5d89', '宝宝', '', '', 0, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2012-10-10 02:06:34', 1349805994, 2),
(3, 'sbyldrnrpgmyplenvssyoragqcmleqqrlgklidruwfxudsocgvkktpftqxqmk', 'iojhoxbfeuceqlhtsglxlefazkbmdtulhlzzfdcnxmgslxqqvyrfuviwvyfqip', 'e4fee685b5dd28142494a45a08651972', '爸爸妈妈', '', '', 0, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2012-10-10 02:07:14', 1349806034, 2);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
