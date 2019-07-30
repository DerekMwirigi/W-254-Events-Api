/*
Navicat MySQL Data Transfer

Source Server         : Localhost
Source Server Version : 50524
Source Host           : localhost:3309
Source Database       : events

Target Server Type    : MYSQL
Target Server Version : 50524
File Encoding         : 65001

Date: 2019-07-30 08:31:48
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `categories`
-- ----------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `image` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of categories
-- ----------------------------
INSERT INTO `categories` VALUES ('1', '4356', 'Music', 'https://mrc-assets.s3.amazonaws.com/assets/Image/103884-fitandcrop-717x437.jpg');
INSERT INTO `categories` VALUES ('2', '3563', 'Art', 'https://www.noetwo.com/98-home_default/reflexion.jpg');
INSERT INTO `categories` VALUES ('3', '6743', 'Tech', 'https://www-tc.pbs.org/wgbh/nova/media/images/nova-wonders-can-we-build-a-brain-hero_xn7Rr8X.width-800.jpg');

-- ----------------------------
-- Table structure for `checkouts`
-- ----------------------------
DROP TABLE IF EXISTS `checkouts`;
CREATE TABLE `checkouts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `eventId` int(1) DEFAULT NULL,
  `noTickets` int(11) DEFAULT NULL,
  `billingTypeId` int(11) DEFAULT NULL,
  `billingAddress` text,
  `createdOn` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of checkouts
-- ----------------------------
INSERT INTO `checkouts` VALUES ('1', '1', '2', '2', '1', '{\"billingTypeId\":1,\"mpesaPhone\":\"254706828557\",\"amount\":100}', '2019-07-29 16:41:27');
INSERT INTO `checkouts` VALUES ('2', '1', '2', '2', '1', '{\"billingTypeId\":1,\"mpesaPhone\":\"254706828557\",\"amount\":100}', '2019-07-29 20:54:08');
INSERT INTO `checkouts` VALUES ('3', '1', '2', '2', '1', '{\"billingTypeId\":1,\"mpesaPhone\":\"254706828557\",\"amount\":100}', '2019-07-29 22:05:13');
INSERT INTO `checkouts` VALUES ('4', '1', '2', '2', '1', '{\"billingTypeId\":1,\"mpesaPhone\":\"254706828557\",\"amount\":3000}', '2019-07-30 00:31:49');
INSERT INTO `checkouts` VALUES ('5', '1', '2', '2', '1', '{\"billingTypeId\":1,\"mpesaPhone\":\"254706828557\",\"amount\":3000}', '2019-07-30 00:53:48');
INSERT INTO `checkouts` VALUES ('6', '1', '2', '2', '1', '{\"billingTypeId\":1,\"mpesaPhone\":\"254706828557\",\"amount\":3000}', '2019-07-30 00:57:45');
INSERT INTO `checkouts` VALUES ('7', '1', '1', '1', '1', '{\"billingTypeId\":1,\"mpesaPhone\":\"254706828557\",\"amount\":1000}', '2019-07-30 01:04:31');

-- ----------------------------
-- Table structure for `events`
-- ----------------------------
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(100) DEFAULT NULL,
  `label` varchar(100) DEFAULT NULL,
  `description` text,
  `eventDate` date DEFAULT NULL,
  `ticketPrice` decimal(11,2) DEFAULT NULL,
  `locationId` int(11) DEFAULT NULL,
  `image` text,
  `plannerId` int(11) DEFAULT NULL,
  `noTickets` int(11) DEFAULT NULL,
  `soldTickets` int(11) DEFAULT '0',
  `createdById` int(11) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `statusCode` int(11) DEFAULT NULL,
  `statusName` varchar(100) DEFAULT 'Upcoming',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of events
-- ----------------------------
INSERT INTO `events` VALUES ('1', '3456', 'Jazz Festival', 'Jazz Jazz', '2019-07-31', '1000.00', '2', 'https://files.guidedanmark.org/files/476/221328_nykoebing-roervig-jazz-festival-2019-groen.jpg', '2', '2000', '0', '1', '2019-07-28 22:39:18', '1', 'Upcoming');
INSERT INTO `events` VALUES ('2', '2345', 'Radical Kesha', 'Kesha', '2019-08-10', '1500.00', '1', 'https://cdn-az.allevents.in/banners/ace6eeb63c8ba10412adf3f182b1053d', '3', '300', '0', '1', '2019-07-28 23:15:58', '1', 'Upcoming');

-- ----------------------------
-- Table structure for `locations`
-- ----------------------------
DROP TABLE IF EXISTS `locations`;
CREATE TABLE `locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `lat` varchar(255) DEFAULT NULL,
  `lng` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of locations
-- ----------------------------
INSERT INTO `locations` VALUES ('1', 'Gong Racecorse', '-1.06', '35.26');
INSERT INTO `locations` VALUES ('2', 'KICC', '-1.02', '34.02');

-- ----------------------------
-- Table structure for `planners`
-- ----------------------------
DROP TABLE IF EXISTS `planners`;
CREATE TABLE `planners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `mobile` varchar(12) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of planners
-- ----------------------------
INSERT INTO `planners` VALUES ('1', 'NRG Radio', '254706828557', 'info@nrg.radio', 'https://nrg.radio');
INSERT INTO `planners` VALUES ('2', 'Safaricom Twaweza', '254706828557', 'plan@safcom.com', 'https://safaricom.com');
INSERT INTO `planners` VALUES ('3', 'ChrisCo Upper Room', '25470682857', 'planners@chrisco.church', 'https://chrisco.church');

-- ----------------------------
-- Table structure for `tickets`
-- ----------------------------
DROP TABLE IF EXISTS `tickets`;
CREATE TABLE `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(100) DEFAULT NULL,
  `buyerId` int(11) DEFAULT NULL,
  `eventId` int(11) DEFAULT NULL,
  `noTickets` int(11) DEFAULT NULL,
  `paymentStatus` varchar(100) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tickets
-- ----------------------------
INSERT INTO `tickets` VALUES ('1', '4546', '2', '1', '2', 'Pending', '2019-07-29 01:38:28');
INSERT INTO `tickets` VALUES ('2', '7654', '2', '2', '1', 'Paid', '2019-07-29 02:48:13');
INSERT INTO `tickets` VALUES ('3', '7655', '2', '1', '2', 'Paid', '2019-07-29 14:01:43');

-- ----------------------------
-- Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(100) DEFAULT NULL,
  `token` text,
  `firstName` varchar(100) DEFAULT NULL,
  `lastName` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mobile` varchar(12) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `image` text,
  `roleId` int(11) DEFAULT NULL,
  `createdOn` datetime DEFAULT NULL,
  `createdById` int(11) DEFAULT NULL,
  `statusCode` int(11) DEFAULT '1',
  `statusName` varchar(200) DEFAULT 'Active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', '34643', 'a8c281c80e0fa491fd615a906cc8ccf6d6d0c33020190716102804', 'Derek', 'Mugambi', 'derekmwirigi@gmail.com', '254706828557', '5f4dcc3b5aa765d61d8327deb882cf99', 'https://www.noetwo.com/98-home_default/reflexion.jpg', '1', '2019-07-29 12:37:38', '1', '1', 'Active');
