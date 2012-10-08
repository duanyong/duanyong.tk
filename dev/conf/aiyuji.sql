/* 用户表 */;
/* 用户有四种状态
 *      status  0: 正常的注册用户
 *      status  1: 未注册用户（匿名用户）
 *      status  2: 未注册用户（写信里指定用户昵称）
*/;

CREATE TABLE `201208aiyuji_user` (
    `id`            INT(10)         UNSIGNED    NOT NULL    AUTO_INCREMENT                  COMMENT '主键',
    `token`         CHAR(64)                    NOT NULL    DEFAULT ''                      COMMENT '用户标识',

    `username`      CHAR(64)                    NOT NULL    DEFAULT ''                      COMMENT '用户手机',
    `password`      CHAR(32)                    NOT NULL    DEFAULT ''                      COMMENT '用户密码',

    `nickname`      VARCHAR(32)                 NOT NULL    DEFAULT ''                      COMMENT '用户昵称',

    `firstname`     VARCHAR(32)                 NOT NULL    DEFAULT ''                      COMMENT '用户姓氏',
    `lastname`      VARCHAR(32)                 NOT NULL    DEFAULT ''                      COMMENT '用户名字',
    `sex`           BOOLEAN                     NOT NULL    DEFAULT FALSE                   COMMENT '用户性别',
    `regip`         VARCHAR(32)                 NOT NULL    DEFAULT ''                      COMMENT '注册IP',
    `sum`           INT(11)         UNSIGNED    NOT NULL    DEFAULT '0'                     COMMENT '留言总数',

    `regtime`       DATETIME                    NOT NULL    DEFAULT '0000-00-00 00:00:000'  COMMENT '通过reg方式注册的时间',
    `tokentime`     DATETIME                    NOT NULL    DEFAULT '0000-00-00 00:00:000'  COMMENT '通过token方式注册的时间',
    `nicktime`      DATETIME                    NOT NULL    DEFAULT '0000-00-00 00:00:000'  COMMENT '通过nickname方式注册的时间',

    `time`          INT(11)         UNSIGNED    NOT NULL    DEFAULT '0'                     COMMENT '注册时间',
    `status`        TINYINT(4)                  NOT NULL    DEFAULT '0'                     COMMENT '当前状态',

    PRIMARY KEY (`id`),
    INDEX `idx_token` (`token`),
    INDEX `idx_username` (`username`),
    INDEX `idx_nickname` (`nickname`),
    INDEX `idx_status` (`status`)

) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户信息表';



/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `201208aiyuji_words` (
    `id`            INT(10)         UNSIGNED    NOT NULL    AUTO_INCREMENT                  COMMENT '主键',
    `sid`           INT(10)         UNSIGNED    NOT NULL    DEFAULT '0'                     COMMENT '发送者',
    `tid`           INT(10)         UNSIGNED    NOT NULL    DEFAULT '0'                     COMMENT '接收者',
    `words`         TEXT                        NOT NULL    DEFAULT ''                      COMMENT '留言',
    `key`           VARCHAR(512)                NOT NULL    DEFAULT ''                      COMMENT '关键词',
    `public`        TINYINT(4)      UNSIGNED    NOT NULL    DEFAULT '1'                     COMMENT '是否公开',

    `ip`            VARCHAR(32)                 NOT NULL    DEFAULT ''                      COMMENT '注册IP',
    `time`          INT(11)         UNSIGNED    NOT NULL    DEFAULT '0'                     COMMENT '创建时间',
    `status`        TINYINT(4)                  NOT NULL    DEFAULT '0'                     COMMENT '用户当前状态',

    PRIMARY KEY (`id`),
    INDEX `idx_sid` (`sid`),
    INDEX `idx_tid` (`tid`),
    INDEX `idx_public` (`public`),
    INDEX `idx_time` (`time`),
    INDEX `idx_status` (`status`)

) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户说说表';
/*!40101 SET character_set_client = @saved_cs_client */;
