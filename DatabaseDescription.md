# Database Description

## Schema

### socialinfo

```mysql
CREATE SCHEMA `socialinfo` DEFAULT CHARACTER SET utf8mb4;
```

## Table

> You should create tables according to the followig order !

### user

```mysql
CREATE TABLE `user` (
    `id`        int(11) NOT NULL AUTO_INCREMENT,
    `username`  varchar(45) NOT NULL,
    `password`  varchar(45) NOT NULL,
    `nickname`  varchar(45) NOT NULL,
    `phone`     varchar(45) NOT NULL,
    `authority` varchar(45) NOT NULL DEFAULT 'User',
    `enable`    tinyint(4) NOT NULL DEFAULT '1',
    PRIMARY KEY (`id`),
    UNIQUE KEY `username_UNIQUE` (`username`),
    UNIQUE KEY `phone_UNIQUE` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### questionnaire

```mysql
CREATE TABLE `questionnaire` (
    `id`            int(11) NOT NULL AUTO_INCREMENT,
    `user_id`       int(11) NOT NULL,
    `title`         varchar(45) NOT NULL,
    `begin_date`    bigint(20) NOT NULL,
    `end_date`      bigint(20) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id_idx` (`user_id`),
    CONSTRAINT `fk_questionnaire_user` 
        FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### question

```mysql
CREATE TABLE `question` (
    `id`                int(11) NOT NULL AUTO_INCREMENT,
    `questionnaire_id`  int(11) NOT NULL,
    `order`             int(11) NOT NULL,
    `type`              varchar(45) NOT NULL,
    `question`          varchar(45) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_questin_questionnaire_idx` (`questionnaire_id`),
    CONSTRAINT `fk_questin_questionnaire` 
        FOREIGN KEY (`questionnaire_id`) REFERENCES `questionnaire` (`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### option

```mysql
CREATE TABLE `option` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `question_id` int(11) NOT NULL,
    `order` int(11) NOT NULL,
    `content` varchar(45) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_option_question_idx` (`question_id`),
    CONSTRAINT `fk_option_question` 
        FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### visitor

```mysql
CREATE TABLE `visitor` (
    `id` int(11) NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### choice_answer

```mysql
CREATE TABLE `choice_answer`
(
    `id`         bigint(20) NOT NULL AUTO_INCREMENT,
    `option_id`  int(11)    NOT NULL,
    `visitor_id` int(11)    NOT NULL,
    PRIMARY KEY (`id`),
    KEY `fk_choice_answer_option_idx` (`option_id`),
    KEY `fk_choice_answer_visitor_idx` (`visitor_id`),
    CONSTRAINT `fk_choice_answer_option`
        FOREIGN KEY (`option_id`) REFERENCES `option` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_choice_answer_visitor`
        FOREIGN KEY (`visitor_id`) REFERENCES `visitor` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### text_answer

```mysql
CREATE TABLE `text_answer` (
    `id`            int(11) NOT NULL AUTO_INCREMENT,
    `question_id`   int(11) NOT NULL,
    `visitor_id`    int(11) NOT NULL,
    `text`          varchar(45) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    KEY `fk_text_answer_question_idx` (`question_id`),
    KEY `fk_text_answer_visitor_idx` (`visitor_id`),
    CONSTRAINT `fk_text_answer_question` 
        FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) 
        ON DELETE NO ACTION ON UPDATE NO ACTION,
    CONSTRAINT `fk_text_answer_visitor` 
        FOREIGN KEY (`visitor_id`) REFERENCES `visitor` (`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```