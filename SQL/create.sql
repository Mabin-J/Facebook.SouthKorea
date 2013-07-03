CREATE TABLE IF NOT EXISTS `fb_korean_url` (
  `uid` bigint(20) NOT NULL,
  `link` varchar(100) NOT NULL,
  `url` varchar(100) NOT NULL,
  PRIMARY KEY (`uid`),
  UNIQUE KEY `url_uniq` (`url`),
  KEY `url_link` (`url`,`link`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

