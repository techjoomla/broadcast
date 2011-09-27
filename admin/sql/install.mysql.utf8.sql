
CREATE TABLE IF NOT EXISTS `#__techjoomlaAPI_users` (
  `id` int(11) NOT NULL auto_increment,
  `api` varchar(200) NOT NULL,
  `token` TEXT NOT NULL,
  `user_id` int(11) NOT NULL,
  `client` varchar(200) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__broadcast_tmp_activities` (
  `uid` int(10) NOT NULL,
  `status` TEXT NOT NULL,
  `type` varchar(50) NOT NULL,
  `created_date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__broadcast_config` (
  `user_id` int(11) NOT NULL,
  `broadcast_activity_config` varchar(500) NOT NULL,
  `broadcast_rss_url` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		

CREATE TABLE IF NOT EXISTS `#__broadcast_queue` (
  `id` int(11) NOT NULL auto_increment,
  `status` varchar(500) NOT NULL,
  `userid` int(11) NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `flag` int(2) NOT NULL,
  `count` int(5) NOT NULL,
  `interval` int(11) NOT NULL,
  `api` varchar(500) NOT NULL,
  `supplier` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
