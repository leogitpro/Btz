# Doctrine Migration File Generated on 2016-11-14 17:39:29

# Version 20161114165551
CREATE TABLE adminer (admin_id INT UNSIGNED AUTO_INCREMENT NOT NULL, admin_email VARCHAR(45) NOT NULL COMMENT '用户邮件地址, 登入账号.', admin_passwd CHAR(32) NOT NULL COMMENT '用户密码.', admin_name VARCHAR(45) NOT NULL COMMENT '用户名称.', admin_status SMALLINT UNSIGNED DEFAULT 0 NOT NULL, PRIMARY KEY(admin_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
INSERT INTO `adminer` (`admin_email`, `admin_passwd`, `admin_name`, `admin_status`) VALUES (?, ?, ?, ?);
INSERT INTO migrations (version) VALUES ('20161114165551');
