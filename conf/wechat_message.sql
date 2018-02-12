/*
Navicat MySQL Data Transfer

Source Server         : 192.168.0.177
Source Server Version : 50627
Source Host           : 127.0.0.1:3306
Source Database       : operation

Target Server Type    : MYSQL
Target Server Version : 50627
File Encoding         : 65001

Date: 2017-12-04 12:23:48
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `wechat_receive_message`
-- ----------------------------
DROP TABLE IF EXISTS `wechat_receive_message`;
CREATE TABLE `wechat_receive_message` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Content` text NOT NULL COMMENT '文本消息内容',
  `CreateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '消息创建时间',
  `Description` varchar(255) NOT NULL DEFAULT '' COMMENT '消息描述',
  `Event` varchar(255) NOT NULL DEFAULT '' COMMENT '事件类型，subscribe(订阅)、unsubscribe(取消订阅)，LOCATION，CLICK，事件类型，VIEW',
  `EventKey` varchar(255) NOT NULL DEFAULT '' COMMENT '事件KEY值，是一个32位无符号整数，即创建二维码时的二维码scene_id，事件KEY值，与自定义菜单接口中KEY值对应，事件KEY值，设置的跳转URL',
  `Format` varchar(255) NOT NULL DEFAULT '' COMMENT '语音格式，如amr，speex等',
  `FromUserName` varchar(255) NOT NULL DEFAULT '' COMMENT '发送方微信号',
  `Label` varchar(255) NOT NULL DEFAULT '' COMMENT '地理位置信息',
  `Latitude` decimal(13,10) NOT NULL DEFAULT '0.0000000000' COMMENT '地理位置纬度',
  `Location_X` decimal(13,10) NOT NULL DEFAULT '0.0000000000' COMMENT '地理位置维度',
  `Location_Y` decimal(13,10) NOT NULL DEFAULT '0.0000000000' COMMENT '地理位置经度',
  `Longitude` decimal(13,10) NOT NULL DEFAULT '0.0000000000' COMMENT '地理位置经度',
  `MediaId` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '图片消息媒体id，可以调用多媒体文件下载接口拉取数据。语音消息媒体id，可以调用多媒体文件下载接口拉取数据。',
  `MsgId` varchar(255) NOT NULL DEFAULT '' COMMENT '消息id，64位整型',
  `MsgType` varchar(16) NOT NULL DEFAULT '' COMMENT 'MsgType,消息类型,link,location,小视频为shortvideo,视频为video,语音为voice,image,text，event',
  `PicUrl` varchar(255) NOT NULL DEFAULT '' COMMENT '图片链接（由系统生成）',
  `Precision` decimal(9,6) NOT NULL DEFAULT '0.000000' COMMENT '地理位置精度',
  `Recognition` varchar(255) NOT NULL DEFAULT '' COMMENT '语音识别结果，UTF8编码',
  `Scale` decimal(10,0) NOT NULL DEFAULT '0' COMMENT '地图缩放大小',
  `ThumbMediaId` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据。',
  `Ticket` varchar(255) NOT NULL DEFAULT '' COMMENT '二维码的ticket，可用来换取二维码图片',
  `Title` varchar(255) NOT NULL DEFAULT '' COMMENT '消息标题',
  `ToUserName` varchar(255) NOT NULL DEFAULT '' COMMENT '接收方微信号',
  `Url` varchar(255) NOT NULL DEFAULT '' COMMENT '消息链接',
  `Poiname` varchar(255) NOT NULL DEFAULT '' COMMENT 'POI定位信息，一般包含名称、类别、经度纬度、附近酒店商铺等信息',
  `ScanType` varchar(255) NOT NULL DEFAULT '' COMMENT '扫描类型，一般是qrcode',
  `ScanResult` varchar(255) NOT NULL DEFAULT '' COMMENT '扫描结果，即二维码对应的字符串信息',
  `Count` int(11) NOT NULL DEFAULT '0' COMMENT '发送的图片数量',
  `PicList` text NOT NULL COMMENT '图片列表(PicMd5Sum:图片的MD5值，开发者若需要，可用于验证接收到图片)',
  `ExpiredTime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '有效期 (整形)，指的是时间戳，将于该时间戳认证过期',
  `FailTime` int(10) unsigned DEFAULT '0' COMMENT '失败发生时间 (整形)，时间戳',
  `FailReason` varchar(255) NOT NULL DEFAULT '' COMMENT '认证失败的原因',
  PRIMARY KEY (`ID`),
  KEY `ToUserName` (`ToUserName`) USING BTREE,
  KEY `FromUserName` (`FromUserName`) USING BTREE,
  KEY `MsgId` (`MsgId`) USING BTREE,
  KEY `MsgType` (`MsgType`) USING BTREE,
  KEY `CreateTime` (`CreateTime`) USING BTREE,
  KEY `Format` (`Format`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3381 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wechat_receive_message
-- ----------------------------

-- ----------------------------
-- Table structure for `wechat_send_message`
-- ----------------------------
DROP TABLE IF EXISTS `wechat_send_message`;
CREATE TABLE `wechat_send_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `articles` text NOT NULL,
  `article_count` int(11) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `description` varchar(255) NOT NULL DEFAULT '',
  `fromuser` varchar(255) NOT NULL DEFAULT '',
  `hqmusicurl` varchar(255) NOT NULL DEFAULT '',
  `media_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `msgtype` varchar(255) NOT NULL DEFAULT '',
  `musicurl` varchar(255) NOT NULL DEFAULT '',
  `picurl` varchar(255) NOT NULL DEFAULT '',
  `thumb_media_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `touser` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `touser` (`touser`) USING BTREE,
  KEY `msgtype` (`msgtype`) USING BTREE,
  KEY `createtime` (`createtime`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wechat_send_message
-- ----------------------------

-- ----------------------------
-- Table structure for `wechat_template_message`
-- ----------------------------
DROP TABLE IF EXISTS `wechat_template_message`;
CREATE TABLE `wechat_template_message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `client_id` int(11) NOT NULL DEFAULT '0' COMMENT '企业id',
  `template_id` varchar(255) NOT NULL DEFAULT '' COMMENT '微信消息模版id',
  `touser` varchar(255) NOT NULL DEFAULT '' COMMENT '接收人的openid',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '点击消息的跳转url',
  `data` varchar(255) NOT NULL DEFAULT '' COMMENT '模版数据',
  `msg_type` varchar(255) NOT NULL DEFAULT '' COMMENT '消息类型',
  `status` varchar(255) NOT NULL DEFAULT '' COMMENT '消息状态',
  `return` varchar(255) NOT NULL DEFAULT '' COMMENT '微信接口返回结果',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `ts` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of wechat_template_message
-- ----------------------------
INSERT INTO `wechat_template_message` VALUES ('1', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwIgjd9soE7SzlM_jo_Otuw0', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'SUCC', '', '1511871178', '2017-11-28 20:13:01');
INSERT INTO `wechat_template_message` VALUES ('2', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwDViuwxq-1VPnPgLmDOXZFI', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'FAILED', '', '1511872681', '2017-11-28 20:38:03');
INSERT INTO `wechat_template_message` VALUES ('3', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwDViuwxq-1VPnPgLmDOXZFI', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'FAILED', '', '1511872760', '2017-11-28 20:39:23');
INSERT INTO `wechat_template_message` VALUES ('4', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwDViuwxq-1VPnPgLmDOXZFI', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'FAILED', '', '1511872813', '2017-11-28 20:40:14');
INSERT INTO `wechat_template_message` VALUES ('5', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwDViuwxq-1VPnPgLmDOXZFI', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'INIT', '', '1511872817', '2017-11-28 20:40:17');
INSERT INTO `wechat_template_message` VALUES ('6', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwDViuwxq-1VPnPgLmDOXZFI', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'FAILED', '', '1511873414', '2017-11-28 20:50:17');
INSERT INTO `wechat_template_message` VALUES ('7', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwDViuwxq-1VPnPgLmDOXZFI', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'FAILED', '', '1511873425', '2017-11-28 20:50:27');
INSERT INTO `wechat_template_message` VALUES ('8', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwDViuwxq-1VPnPgLmDOXZFI', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'FAILED', '', '1511873498', '2017-11-28 20:51:40');
INSERT INTO `wechat_template_message` VALUES ('9', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwDViuwxq-1VPnPgLmDOXZFI', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'SUCC', '', '1511873586', '2017-11-28 20:53:08');
INSERT INTO `wechat_template_message` VALUES ('10', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwDViuwxq-1VPnPgLmDOXZFI', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'INIT', '', '1511873596', '2017-11-28 20:53:16');
INSERT INTO `wechat_template_message` VALUES ('11', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwDViuwxq-1VPnPgLmDOXZFI', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'SUCC', '', '1511873607', '2017-11-28 20:53:30');
INSERT INTO `wechat_template_message` VALUES ('12', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwDViuwxq-1VPnPgLmDOXZFI', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'FAILED', '', '1511926758', '2017-11-29 11:39:22');
INSERT INTO `wechat_template_message` VALUES ('13', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwDViuwxq-1VPnPgLmDOXZFI', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'FAILED', '', '1511926874', '2017-11-29 11:41:16');
INSERT INTO `wechat_template_message` VALUES ('14', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwDViuwxq-1VPnPgLmDOXZFI', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'SUCC', '', '1511927118', '2017-11-29 11:45:20');
INSERT INTO `wechat_template_message` VALUES ('15', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwIgjd9soE7SzlM_jo_Otuw0', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'SUCC', '', '1511927172', '2017-11-29 11:46:15');
INSERT INTO `wechat_template_message` VALUES ('16', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwIgjd9soE7SzlM_jo_Otuw0', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'SUCC', '{\"errcode\":0,\"errmsg\":\"ok\",\"msgid\":3.9173923388113e+16}', '1511927214', '2017-11-29 11:46:58');
INSERT INTO `wechat_template_message` VALUES ('17', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwIgjd9soE7SzlM_jo_Otuw0', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'SUCC', '{\"errcode\":0,\"errmsg\":\"ok\",\"msgid\":3.9190404603691e+16}', '1511928197', '2017-11-29 12:03:21');
INSERT INTO `wechat_template_message` VALUES ('18', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwIgjd9soE7SzlM_jo_Otuw0', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'FAILED', '{\"errcode\":41001,\"errmsg\":\"access_token missing hint: [F1NCaa0466hsz5!]\"}', '1511928462', '2017-11-29 12:07:46');
INSERT INTO `wechat_template_message` VALUES ('19', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwIgjd9soE7SzlM_jo_Otuw0', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'FAILED', '{\"errcode\":41001,\"errmsg\":\"access_token missing hint: [XDOLSA0503sz10!]\"}', '1511928500', '2017-11-29 12:08:23');
INSERT INTO `wechat_template_message` VALUES ('20', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwIgjd9soE7SzlM_jo_Otuw0', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'FAILED', '{\"errcode\":41001,\"errmsg\":\"access_token missing hint: [iC9VuA0204sz12!]\"}', '1511929200', '2017-11-29 12:20:04');
INSERT INTO `wechat_template_message` VALUES ('21', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwIgjd9soE7SzlM_jo_Otuw0', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'FAILED', '{\"errcode\":41001,\"errmsg\":\"access_token missing hint: [H0211sz12!]\"}', '1511929207', '2017-11-29 12:20:11');
INSERT INTO `wechat_template_message` VALUES ('22', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwIgjd9soE7SzlM_jo_Otuw0', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'FAILED', '{\"errcode\":41001,\"errmsg\":\"access_token missing hint: [8vbMJa0320hsz2!]\"}', '1511929316', '2017-11-29 12:22:00');
INSERT INTO `wechat_template_message` VALUES ('23', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwIgjd9soE7SzlM_jo_Otuw0', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'FAILED', '{\"errcode\":41001,\"errmsg\":\"access_token missing hint: [mSGy.a0324sz12!]\"}', '1511929321', '2017-11-29 12:22:04');
INSERT INTO `wechat_template_message` VALUES ('24', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwIgjd9soE7SzlM_jo_Otuw0', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'FAILED', '{\"errcode\":41001,\"errmsg\":\"access_token missing hint: [PhlvDa0540hsz5!]\"}', '1511929537', '2017-11-29 12:25:40');
INSERT INTO `wechat_template_message` VALUES ('25', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwIgjd9soE7SzlM_jo_Otuw0', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'SUCC', '{\"errcode\":0,\"errmsg\":\"ok\",\"msgid\":3.921534306286e+16}', '1511929683', '2017-11-29 12:28:07');
INSERT INTO `wechat_template_message` VALUES ('26', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwIgjd9soE7SzlM_jo_Otuw0', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'SUCC', '{\"errcode\":0,\"errmsg\":\"ok\",\"msgid\":\"3.9217209259704E+16\"}', '1511929794', '2017-11-29 12:29:58');
INSERT INTO `wechat_template_message` VALUES ('27', '0', 'fDDGjNbCi-M6f1cPXxah93IUSW1kUsYgkdJ2dZ30Tms', 'ovzFrwIgjd9soE7SzlM_jo_Otuw0', 'http://www.zhugedaodian.com', '{\"username\":{\"value\":\"\\u6d4b\\u8bd5\\u7528\\u6237\",\"color\":\"#ff0000\"},\"time\":{\"value\":\"2017-11-28 18:43:50\",\"color\":\"#00ff00\"},\"shop_name\":{\"value\":\"\\u5bcc\\u7532\\u96c6\\u56e2\",\"color\":\"#0000ff\"},\"money\":{\"value\":\"99.95\",\"color\":\"#f0f000\"}}', '', 'SUCC', '{\"errcode\":0,\"errmsg\":\"ok\",\"msgid\":4.2247499813077e+16}', '1512110413', '2017-12-01 14:40:18');
