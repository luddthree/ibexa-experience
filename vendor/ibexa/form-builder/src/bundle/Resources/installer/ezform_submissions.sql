DROP TABLE IF EXISTS `ezform_form_submissions`;
CREATE TABLE `ezform_form_submissions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `content_id` INT NOT NULL,
  `language_code` VARCHAR(6) NOT NULL,
  `user_id` INT NOT NULL,
  `created` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ezform_form_submission_data`;
CREATE TABLE `ezform_form_submission_data` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `form_submission_id` INT NOT NULL,
  `name` VARCHAR(128) NOT NULL,
  `identifier` VARCHAR(128) NOT NULL,
  `value` BLOB NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
