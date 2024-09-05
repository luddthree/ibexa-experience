DROP TABLE IF EXISTS `ezform_forms`;
CREATE TABLE `ezform_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) DEFAULT NULL,
  `version_no` int(11) DEFAULT NULL,
  `content_field_id` int(11) DEFAULT NULL,
  `language_code` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ezform_fields`;
CREATE TABLE `ezform_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) DEFAULT NULL,
  `name` VARCHAR(128) NOT NULL,
  `identifier` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ezform_field_attributes`;
CREATE TABLE `ezform_field_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) DEFAULT NULL,
  `identifier` varchar(128) DEFAULT NULL,
  `value` blob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ezform_field_validators`;
CREATE TABLE `ezform_field_validators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field_id` int(11) DEFAULT NULL,
  `identifier` varchar(128) DEFAULT NULL,
  `value` blob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
