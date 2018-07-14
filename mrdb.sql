/*
 Navicat Premium Data Transfer

 Source Server         : local
 Source Server Type    : MySQL
 Source Server Version : 100121
 Source Host           : localhost:3306
 Source Schema         : mrdb

 Target Server Type    : MySQL
 Target Server Version : 100121
 File Encoding         : 65001

 Date: 14/07/2018 15:19:42
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for families
-- ----------------------------
DROP TABLE IF EXISTS `families`;
CREATE TABLE `families`  (
  `FAMILY_ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `FAMILY_NAME` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `PERS_ID` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`FAMILY_ID`) USING BTREE,
  INDEX `FAMILY_fk_PERSON`(`PERS_ID`) USING BTREE,
  CONSTRAINT `FAMILY_fk_PERSON` FOREIGN KEY (`PERS_ID`) REFERENCES `persons` (`PERS_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = 'ครอบครัว' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for family_members
-- ----------------------------
DROP TABLE IF EXISTS `family_members`;
CREATE TABLE `family_members`  (
  `FAMILY_MEMBER_ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `FAMILY_ID` int(10) UNSIGNED NOT NULL,
  `PERS_ID` int(10) UNSIGNED NULL DEFAULT NULL,
  `FAMILY_MEMBER_STATUS` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `IS_STAY` tinyint(1) NULL DEFAULT NULL,
  `START_DATE` datetime(0) NULL DEFAULT NULL,
  `END_DATE` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`FAMILY_MEMBER_ID`) USING BTREE,
  INDEX `FAMILY_MEMBER_fk_PERSON`(`PERS_ID`) USING BTREE,
  INDEX `FAMILY_MEMBER_fk_FAMILY`(`FAMILY_ID`) USING BTREE,
  CONSTRAINT `FAMILY_MEMBER_fk_FAMILY` FOREIGN KEY (`FAMILY_ID`) REFERENCES `families` (`FAMILY_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FAMILY_MEMBER_fk_PERSON` FOREIGN KEY (`PERS_ID`) REFERENCES `persons` (`PERS_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for family_room_mappings
-- ----------------------------
DROP TABLE IF EXISTS `family_room_mappings`;
CREATE TABLE `family_room_mappings`  (
  `FAMILY_ROOM_MAPPING_ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ROOM_ID` int(10) UNSIGNED NULL DEFAULT NULL,
  `FAMILY_ID` int(10) UNSIGNED NULL DEFAULT NULL,
  `START_DATE` datetime(0) NULL DEFAULT NULL,
  `END_DATE` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`FAMILY_ROOM_MAPPING_ID`) USING BTREE,
  INDEX `FAMILY_ROOM_MAPPING_fk_ROOM`(`ROOM_ID`) USING BTREE,
  INDEX `FAMILY_ROOM_MAPPING_fk_FAMILY`(`FAMILY_ID`) USING BTREE,
  CONSTRAINT `FAMILY_ROOM_MAPPING_fk_FAMILY` FOREIGN KEY (`FAMILY_ID`) REFERENCES `families` (`FAMILY_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FAMILY_ROOM_MAPPING_fk_ROOM` FOREIGN KEY (`ROOM_ID`) REFERENCES `home_rooms` (`ROOM_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for home_rooms
-- ----------------------------
DROP TABLE IF EXISTS `home_rooms`;
CREATE TABLE `home_rooms`  (
  `ROOM_ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `HOME_SECTION_ID` int(10) UNSIGNED NULL DEFAULT NULL,
  `ROOM_NAME` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `ROOM_ORDER` int(5) NULL DEFAULT NULL,
  `ROOM_ADDRESS` int(10) NULL DEFAULT NULL,
  `ROOM_SUB_ADDRESS` int(10) NULL DEFAULT NULL,
  `ROOM_SEQ` int(5) NULL DEFAULT NULL,
  `ROOM_STATUS_ID` int(5) NULL DEFAULT NULL,
  `OWNER_GROUP_ID` int(10) NULL DEFAULT NULL,
  PRIMARY KEY (`ROOM_ID`) USING BTREE,
  INDEX `HOME_ROOM_fk_HOME_SECTION`(`HOME_SECTION_ID`) USING BTREE,
  INDEX `HOME_ROOM_fk_ROOM_STATUS`(`ROOM_STATUS_ID`) USING BTREE,
  INDEX `HOME_ROOM_fk_OWNER_GROUP`(`OWNER_GROUP_ID`) USING BTREE,
  CONSTRAINT `HOME_ROOM_fk_HOME_SECTION` FOREIGN KEY (`HOME_SECTION_ID`) REFERENCES `home_sections` (`HOME_SECTION_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `HOME_ROOM_fk_OWNER_GROUP` FOREIGN KEY (`OWNER_GROUP_ID`) REFERENCES `owner_group_tbls` (`OWNER_GROUP_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `HOME_ROOM_fk_ROOM_STATUS` FOREIGN KEY (`ROOM_STATUS_ID`) REFERENCES `room_status_tbls` (`ROOM_STATUS_ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of home_rooms
-- ----------------------------
INSERT INTO `home_rooms` VALUES (3, 8, 'name of room', 1, 20, 10, 1, 1, 1);

-- ----------------------------
-- Table structure for home_sections
-- ----------------------------
DROP TABLE IF EXISTS `home_sections`;
CREATE TABLE `home_sections`  (
  `HOME_SECTION_ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `HOME_ID` int(10) UNSIGNED NULL DEFAULT NULL,
  `HOME_SECTION_NAME` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `HOME_SECTION_ORDER` int(5) NULL DEFAULT NULL,
  PRIMARY KEY (`HOME_SECTION_ID`) USING BTREE,
  INDEX `HOME_SECTION_fk_HOME`(`HOME_ID`) USING BTREE,
  CONSTRAINT `HOME_SECTION_fk_HOME` FOREIGN KEY (`HOME_ID`) REFERENCES `homes` (`HOME_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of home_sections
-- ----------------------------
INSERT INTO `home_sections` VALUES (8, 9, 'name of sec', 1);

-- ----------------------------
-- Table structure for home_type_tbls
-- ----------------------------
DROP TABLE IF EXISTS `home_type_tbls`;
CREATE TABLE `home_type_tbls`  (
  `HOME_TYPE_ID` int(10) NOT NULL AUTO_INCREMENT,
  `HOME_TYPE_NAME` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `HOME_TYPE_DESCR` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`HOME_TYPE_ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of home_type_tbls
-- ----------------------------
INSERT INTO `home_type_tbls` VALUES (1, 'test', 'test');

-- ----------------------------
-- Table structure for homes
-- ----------------------------
DROP TABLE IF EXISTS `homes`;
CREATE TABLE `homes`  (
  `HOME_ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `HOME_NAME` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `HOME_DESCR` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `HOME_TYPE_ID` int(10) NULL DEFAULT NULL,
  `OWNER_GROUP_ID` int(10) NULL DEFAULT NULL,
  `CREATE_DATE` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`HOME_ID`) USING BTREE,
  INDEX `HOME_fk_HOME_TYPE`(`HOME_TYPE_ID`) USING BTREE,
  CONSTRAINT `HOME_fk_HOME_TYPE` FOREIGN KEY (`HOME_TYPE_ID`) REFERENCES `home_type_tbls` (`HOME_TYPE_ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of homes
-- ----------------------------
INSERT INTO `homes` VALUES (9, 'A homes', 'this is a new homes', 1, 1, '2018-07-09 00:37:48');

-- ----------------------------
-- Table structure for owner_group_tbls
-- ----------------------------
DROP TABLE IF EXISTS `owner_group_tbls`;
CREATE TABLE `owner_group_tbls`  (
  `OWNER_GROUP_ID` int(10) NOT NULL AUTO_INCREMENT,
  `OWNER_GROUP_NAME` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `OWNER_GROUP_DESCR` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`OWNER_GROUP_ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of owner_group_tbls
-- ----------------------------
INSERT INTO `owner_group_tbls` VALUES (1, 'หน่วยที่ 1', 'DESCR');
INSERT INTO `owner_group_tbls` VALUES (2, 'หน่วยที่ 2', NULL);
INSERT INTO `owner_group_tbls` VALUES (3, 'หน่วยที่ 3', NULL);
INSERT INTO `owner_group_tbls` VALUES (4, 'หน่วยที่ 4', NULL);
INSERT INTO `owner_group_tbls` VALUES (5, 'หน่วยที่ 5', NULL);
INSERT INTO `owner_group_tbls` VALUES (6, 'หน่วยที่ 6', NULL);
INSERT INTO `owner_group_tbls` VALUES (7, 'NAME SSSS', 'DESCR');
INSERT INTO `owner_group_tbls` VALUES (8, 'NAME SSSS', 'DESCR');

-- ----------------------------
-- Table structure for person_currents
-- ----------------------------
DROP TABLE IF EXISTS `person_currents`;
CREATE TABLE `person_currents`  (
  `PERS_ID` int(10) UNSIGNED NOT NULL,
  `PREFIX` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
  `FIRST_NAME` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `LAST_NAME` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `PERS_NICKNAME` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `FIRST_NAME_EN` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `LAST_NAME_EN` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `PERS_NICKNAME_EN` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `PICTURE_PATH` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
  `PICTURE_PATH_PRIVATE` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
  `GENDER` varchar(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `MAR_STATUS_ID` int(10) UNSIGNED NULL DEFAULT NULL,
  `MAR_STATUS_DT` date NULL DEFAULT NULL,
  `ER_IMMUNIZATION` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `ER_IMMUNIZATION_DESCR` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
  `ADDRESS_1_TYPE0` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `DISTRICT_ID_TYPE0` int(10) UNSIGNED NULL DEFAULT NULL,
  `AMPHUR_ID_TYPE0` int(10) UNSIGNED NULL DEFAULT NULL,
  `PROVINCE_ID_TYPE0` int(10) UNSIGNED NULL DEFAULT NULL,
  `COUNTRY_ID_TYPE0` int(10) UNSIGNED NULL DEFAULT NULL,
  `ZIPCODE_TYPE0` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `ADDRESS_1_TYPE1` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `DISTRICT_ID_TYPE1` int(10) UNSIGNED NULL DEFAULT NULL,
  `AMPHUR_ID_TYPE1` int(10) UNSIGNED NULL DEFAULT NULL,
  `PROVINCE_ID_TYPE1` int(10) UNSIGNED NULL DEFAULT NULL,
  `COUNTRY_ID_TYPE1` int(10) UNSIGNED NULL DEFAULT NULL,
  `ZIPCODE_TYPE1` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `ADDRESS_1_TYPE2` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `DISTRICT_ID_TYPE2` int(10) UNSIGNED NULL DEFAULT NULL,
  `AMPHUR_ID_TYPE2` int(10) UNSIGNED NULL DEFAULT NULL,
  `PROVINCE_ID_TYPE2` int(10) UNSIGNED NULL DEFAULT NULL,
  `COUNTRY_ID_TYPE2` int(10) UNSIGNED NULL DEFAULT NULL,
  `ZIPCODE_TYPE2` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `EMAIL_ADDRESS_1` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `EMAIL_ADDRESS_2` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `PHONE_NBR` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `PHONE_EXT` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `MOBILE_NBR_1` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `MOBILE_NBR_2` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `MOBILE_NBR_3` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `MOBILE_NBR_4` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `MOBILE_NBR_5` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `LINE_ID` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `LAST_UPD_DTTM` datetime(0) NULL DEFAULT NULL,
  `LAST_UPD_OPRID` int(10) NULL DEFAULT NULL,
  PRIMARY KEY (`PERS_ID`) USING BTREE,
  INDEX `fk_PERSON_CURRENTS01_idx`(`MAR_STATUS_ID`) USING BTREE,
  INDEX `fk_PERSON_CURRENTS_ADDR_PROVINCE1_idx`(`PROVINCE_ID_TYPE0`) USING BTREE,
  INDEX `fk_PERSON_CURRENTS_ADDR_PROVINCE2_idx`(`PROVINCE_ID_TYPE1`) USING BTREE,
  INDEX `fk_PERSON_CURRENTS_ADDR_PROVINCE3_idx`(`PROVINCE_ID_TYPE2`) USING BTREE,
  INDEX `fk_PERSON_CURRENTS_ADDR_DISTRICT1_idx`(`DISTRICT_ID_TYPE0`) USING BTREE,
  INDEX `fk_PERSON_CURRENTS_ADDR_DISTRICT2_idx`(`DISTRICT_ID_TYPE1`) USING BTREE,
  INDEX `fk_PERSON_CURRENTS_ADDR_DISTRICT3_idx`(`DISTRICT_ID_TYPE2`) USING BTREE,
  INDEX `fk_PERSON_CURRENTS_ADDR_AMPHUR1_idx`(`AMPHUR_ID_TYPE0`) USING BTREE,
  INDEX `fk_PERSON_CURRENTS_ADDR_AMPHUR2_idx`(`AMPHUR_ID_TYPE1`) USING BTREE,
  INDEX `fk_PERSON_CURRENTS_ADDR_AMPHUR3_idx`(`AMPHUR_ID_TYPE2`) USING BTREE,
  INDEX `fk_PERSON_CURRENTS_ADDR_COUNTRY1_idx`(`COUNTRY_ID_TYPE0`) USING BTREE,
  INDEX `fk_PERSON_CURRENTS_ADDR_COUNTRY2_idx`(`COUNTRY_ID_TYPE1`) USING BTREE,
  INDEX `fk_PERSON_CURRENTS_ADDR_COUNTRY3_idx`(`COUNTRY_ID_TYPE2`) USING BTREE,
  CONSTRAINT `PERSON_CURRENTS_fk_PERSONS` FOREIGN KEY (`PERS_ID`) REFERENCES `persons` (`PERS_ID`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for person_owner_groups
-- ----------------------------
DROP TABLE IF EXISTS `person_owner_groups`;
CREATE TABLE `person_owner_groups`  (
  `PERS_ID` int(10) UNSIGNED NOT NULL,
  `OWNER_GROUP_ID` int(10) NOT NULL,
  PRIMARY KEY (`PERS_ID`, `OWNER_GROUP_ID`) USING BTREE,
  INDEX `PERSON_OWNER_GROUP_fk_OWNER_GROUP`(`OWNER_GROUP_ID`) USING BTREE,
  CONSTRAINT `PERSON_OWNER_GROUP_fk_OWNER_GROUP` FOREIGN KEY (`OWNER_GROUP_ID`) REFERENCES `owner_group_tbls` (`OWNER_GROUP_ID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `PERSON_OWNER_GROUP_fk_PERSON` FOREIGN KEY (`PERS_ID`) REFERENCES `persons` (`PERS_ID`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for person_type_tbls
-- ----------------------------
DROP TABLE IF EXISTS `person_type_tbls`;
CREATE TABLE `person_type_tbls`  (
  `TYPE_ID` int(10) NOT NULL AUTO_INCREMENT,
  `TYPE_NAME` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `TYPE_DESCR` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`TYPE_ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of person_type_tbls
-- ----------------------------
INSERT INTO `person_type_tbls` VALUES (1, 'TypeEdit', 'Descr');
INSERT INTO `person_type_tbls` VALUES (2, 'Type1', 'Descr');

-- ----------------------------
-- Table structure for persons
-- ----------------------------
DROP TABLE IF EXISTS `persons`;
CREATE TABLE `persons`  (
  `PERS_ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `TYPE_ID` int(10) NOT NULL,
  PRIMARY KEY (`PERS_ID`) USING BTREE,
  INDEX `PERSON_fk_PERSON_TYPE`(`TYPE_ID`) USING BTREE,
  CONSTRAINT `PERSON_fk_PERSON_TYPE` FOREIGN KEY (`TYPE_ID`) REFERENCES `person_type_tbls` (`TYPE_ID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for room_status_tbls
-- ----------------------------
DROP TABLE IF EXISTS `room_status_tbls`;
CREATE TABLE `room_status_tbls`  (
  `ROOM_STATUS_ID` int(5) NOT NULL AUTO_INCREMENT,
  `ROOM_STATUS_NAME` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `ROOM_STATUS_DESCR` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`ROOM_STATUS_ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of room_status_tbls
-- ----------------------------
INSERT INTO `room_status_tbls` VALUES (1, 'roomstatus', NULL);

SET FOREIGN_KEY_CHECKS = 1;
