/*
Navicat MySQL Data Transfer

Date: 2019
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `data`
-- ----------------------------
DROP TABLE IF EXISTS `data`;
CREATE TABLE `data` (
  `id` int(30) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(48) NOT NULL DEFAULT '',
  `password` char(64) NOT NULL DEFAULT '',
  `salt` char(48) DEFAULT '',
  `email` char(48) DEFAULT '',
  `order` char(20) DEFAULT '未知' COMMENT '来源',
  PRIMARY KEY (`id`),
  KEY `email` (`email`(20))
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Records of data
-- ----------------------------
INSERT INTO `data` VALUES ('1', 'admin', 'admin888', '', 'admin@qq.com', 'test');
INSERT INTO `data` VALUES ('2', '123456', '123456', '', '123456@qq.com', 'test');
INSERT INTO `data` VALUES ('3', '111111', '111111', '', '111111@qq.com', 'test');
INSERT INTO `data` VALUES ('4', '222222', '222222', '', '222222@qq.com', 'test');
INSERT INTO `data` VALUES ('5', '333333', '333333', '', '333333@qq.com', 'test');
INSERT INTO `data` VALUES ('6', '444444', '444444', '', '444444@qq.com', 'test');
INSERT INTO `data` VALUES ('7', '555555', '555555', '', '555555@qq.com', 'test');
INSERT INTO `data` VALUES ('8', '666666', '666666', '', '666666@qq.com', 'test');
INSERT INTO `data` VALUES ('9', '777777', '777777', '', '777777@qq.com', 'test');
INSERT INTO `data` VALUES ('10', '888888', '888888', '', '888888@qq.com', 'test');
