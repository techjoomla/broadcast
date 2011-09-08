			
CREATE TABLE IF NOT EXISTS `#__broadcast_users` (
`user_id` int(11) NOT NULL,
`facbook_uid` varchar(100) NOT NULL,
`facebook_secret` varchar(400) NOT NULL,
`twitter_oauth` varchar(400) NOT NULL,
`twitter_secret` varchar(400) NOT NULL,
`linkedin_oauth` varchar(400) NOT NULL,
`linkedin_secret` varchar(400) NOT NULL,
PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__broadcast_tmp_activities` (
  `uid` int(10) NOT NULL,
  `status` varchar(500) NOT NULL,
  `type` varchar(50) NOT NULL,
  `created_date` date NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__broadcast_config` (
  `user_id` int(11) NOT NULL,
  `broadcast_activity_config` varchar(500) NOT NULL,
  `broadcast_rss_url` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		

CREATE TABLE IF NOT EXISTS `jos_broadcast_queue` (
  `id` int(11) NOT NULL auto_increment,
  `status` varchar(500) NOT NULL,
  `userid` int(11) NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `flag` int(2) NOT NULL,
  `count` int(5) NOT NULL,
  `interval` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
