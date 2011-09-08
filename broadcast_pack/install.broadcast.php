<?php
/*
public static function move (
        $src
        $dest
        $path= ''
        $use_streams=false
)

*/
jimport( 'joomla.filesystem.folder' );

if(JFolder::exists(JPATH_SITE.'/components/com_community/assets/favicon/'))
{
	JFolder::create(JPATH_SITE.'/components/com_community/assets/favicon/');
	JFile::move(JPATH_SITE.'/components/com_broadcast/images/twitter.png', JPATH_SITE.'/components/com_community/assets/favicon/twitter.png' );
	JFile::move(JPATH_SITE.'/components/com_broadcast/images/linkedin.png', JPATH_SITE.'/components/com_community/assets/favicon/linkedin.png' );
	JFile::move(JPATH_SITE.'/components/com_broadcast/images/facebook.png', JPATH_SITE.'/components/com_community/assets/favicon/facebook.png' );
}
