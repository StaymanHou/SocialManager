DROP TABLE IF EXISTS main_conf;
DROP TABLE IF EXISTS module;
DROP TABLE IF EXISTS auto_mode;
DROP TABLE IF EXISTS account;
DROP TABLE IF EXISTS acc_setting;
DROP TABLE IF EXISTS status;
DROP TABLE IF EXISTS queue;
DROP TABLE IF EXISTS rss_post;

CREATE TABLE main_conf (
	PK INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	TITLE VARCHAR(20) NOT NULL,
	CACHING_TIME INT NOT NULL DEFAULT 7,
	IMAGE_FILE_DIR VARCHAR(256) NOT NULL,
	LOAD_ITERATION INT NOT NULL DEFAULT 1,
	PULLER_ITERATION INT NOT NULL DEFAULT 300,
	POSTER_ITERATION INT NOT NULL DEFAULT 60
) ENGINE = INNODB, COLLATE = utf8_general_ci;

INSERT INTO `main_conf` (`PK`, `TITLE`, `CACHING_TIME`, `IMAGE_FILE_DIR`, `LOAD_ITERATION`, `PULLER_ITERATION`, `POSTER_ITERATION`) VALUES
(1, 'Default', 7, '/', 1, 300, 60);

CREATE TABLE module (
	PK INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	NAME VARCHAR(20) NOT NULL
) ENGINE = INNODB, COLLATE = utf8_general_ci;

INSERT INTO `module` (`PK`, `NAME`) VALUES
(1, 'twitter'),
(2, 'facebook'),
(3, 'googleplus'),
(4, 'tumblr'),
(5, 'pinterest');

CREATE TABLE auto_mode (
	PK INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	MODULE INT NOT NULL,
	CODE INT NOT NULL,
	TITLE VARCHAR(20) NOT NULL,
	OTHER_SETTING TEXT
) ENGINE = INNODB, COLLATE = utf8_general_ci;

INSERT INTO `auto_mode` (`PK`, `MODULE`, `CODE`, `TITLE`, `OTHER_SETTING`) VALUES
(1, 1, 1, 'Off', '{"direction": "Not automatically forward the rss pool to the queue."}'),
(2, 1, 2, 'Normal', '{"direction": "This auto mode will try to keep the queue having at least one item pending. It will try to share image first. If there is no image, it will share text only. If there is no rss at all which has not been used, it will give up until the next checking period."}'),
(3, 2, 1, 'Off', '{"direction": "Not automatically forward the rss pool to the queue."}'),
(4, 2, 2, 'Normal', '{"direction": "This auto mode will try to keep the queue having at least one item pending. It will try to share image first. If there is no image, it will share text only. If there is no rss at all which has not been used, it will give up until the next checking period."}'),
(5, 3, 1, 'Off', '{"direction": "Not automatically forward the rss pool to the queue."}'),
(6, 3, 2, 'Normal', '{"direction": "This auto mode will try to keep the queue having at least one item pending. It will try to share image first. If there is no image, it will share text only. If there is no rss at all which has not been used, it will give up until the next checking period."}'),
(7, 4, 1, 'Off', '{"direction": "Not automatically forward the rss pool to the queue."}'),
(8, 4, 2, 'Normal', '{"direction": "This auto mode will try to keep the queue having at least one item pending. It will try to share image first. If there is no image, it will share text only. If there is no rss at all which has not been used, it will give up until the next checking period."}'),
(9, 5, 1, 'Off', '{"direction": "Not automatically forward the rss pool to the queue."}'),
(10, 5, 2, 'Normal', '{"direction": "This auto mode will try to keep the queue having at least one item pending. It will try to share image first. If there is no image, it will share text only. If there is no rss at all which has not been used, it will give up until the next checking period."}');

CREATE TABLE account (
	PK INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	NAME VARCHAR(20) NOT NULL,
	RSS_URL VARCHAR(256) NOT NULL,
	ACTIVE BOOLEAN DEFAULT 0,
	LAST_UPDATE DATETIME,
    DELETED BOOLEAN DEFAULT 0
) ENGINE = INNODB, COLLATE = utf8_general_ci;

INSERT INTO `account` (`PK`, `NAME`, `RSS_URL`, `ACTIVE`, `LAST_UPDATE`, `DELETED`) VALUES
(1, 'kpopstarz', 'http://www.kpopstarz.com/rss/articles/topnews/all.rss', 0, NULL, 0);

CREATE TABLE acc_setting (
	PK INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	ACCOUNT INT NOT NULL,
	MODULE INT NOT NULL,
	USERNAME VARCHAR(64) NOT NULL,
	PSWD VARCHAR(64) NOT NULL COLLATE latin1_swedish_ci,
	OTHER_SETTING TEXT,
	EXTRA_CONTENT VARCHAR(256) DEFAULT '',
	ACTIVE BOOLEAN DEFAULT 0,
	AUTO_MODE INT NOT NULL,
	TAG_LIMIT INT NOT NULL DEFAULT 1,
	TIME_START TIME NOT NULL DEFAULT '00:00:00',
	TIME_END TIME NOT NULL DEFAULT '00:00:00',
	NUM_PER_DAY INT NOT NULL DEFAULT 24,
	MIN_POST_INTERVAL INT NOT NULL DEFAULT 0,
	QUEUE_SIZE INT NOT NULL DEFAULT 48
) ENGINE = INNODB, COLLATE = utf8_general_ci;

INSERT INTO `acc_setting` (`PK`, `ACCOUNT`, `MODULE`, `USERNAME`, `PSWD`, `OTHER_SETTING`, `AUTO_MODE`) VALUES
(1, 1, 1, 'exampletwitter', 'exampletwitter', '', 1),
(2, 1, 2, 'examplefacebook', 'examplefacebook', '{"page_name":"kpopstarz facebook page"}', 3),
(3, 1, 3, 'examplegoogleplus', 'examplegoogleplus', '{"page_name":"kpopstarz google+ page","circle_name_list":["examplecircle1","examplecircle2"]}', 5),
(4, 1, 4, 'exampletumblr', 'exampletumblr', '{"blog_name":"kpopstarz tumblr blog"}', 8),
(5, 1, 5, 'examplepinterest', 'examplepinterest', '{"board_name": "kpopstarz pin board"}', 9);

CREATE TABLE status (
	PK INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	TITLE VARCHAR(20) NOT NULL
) ENGINE = INNODB, COLLATE = utf8_general_ci;

INSERT INTO `status` (`PK`, `TITLE`) VALUES
(1, 'Pending'),
(2, 'Posted'),
(3, 'PostFail');

CREATE TABLE queue (
	PK INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	STATUS INT NOT NULL,
	ACCOUNT INT NOT NULL,
	MODULE INT NOT NULL,
	TYPE INT NOT NULL,
	TITLE VARCHAR(200),
	CONTENT TEXT,
	EXTRA_CONTENT VARCHAR(256),
	TAG VARCHAR(200),
	IMAGE_FILE VARCHAR(256),
	LINK VARCHAR(512),
	OTHER_FIELD TEXT,
	SCHEDULE_TIME DATETIME NOT NULL,
	RSS_SOURCE_PK INT NOT NULL
) ENGINE = INNODB, COLLATE = utf8_general_ci;

CREATE TABLE rss_post (
	PK INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	ACCOUNT INT NOT NULL,
	TITLE VARCHAR(200),
    DESCRIPTION VARCHAR(512),
	CONTENT TEXT,
	TAG VARCHAR(200),
	IMAGE_FILE VARCHAR(256),
    IMAGE_LINK VARCHAR(512),
	LINK VARCHAR(512),
	OTHER_FIELD TEXT,
	SOCIAL_SCORE VARCHAR(256),
	CREATE_TIME DATETIME NOT NULL
) ENGINE = INNODB, COLLATE = utf8_general_ci;

CREATE TABLE tags (
    PK INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    TITLE VARCHAR(64),
    MAP_TAG VARCHAR(64)
) ENGINE = INNODB, COLLATE = utf8_general_ci;
