<?php
/**
* @package		Broadcast
* @copyright	Copyright © 2012 - All rights reserved.
* @license		GNU/GPL
* @author		TechJoomla
* @author mail	extensions@techjoomla.com
* @website		http://techjoomla.com
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// We want to import our app library
Foundry::import( 'admin:/includes/apps/apps' );

/**
 * Some application for EasySocial. Take note that all classes must be derived from the `SocialAppItem` class
 *
 * @since	1.0
 * @author	Author Name <author@email.com>
 */
class SocialUserAppEasysocial_broadcast extends SocialAppItem
{
	/**
	 * Class constructor.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct()
	{
		parent::__construct();
		$this->db= JFactory::getDBO();
	}

	/**
	 * Triggers the preparation of stream.
	 *
	 * If you need to manipulate the stream object, you may do so in this trigger.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialStreamItem	The stream object.
	 * @param	bool				Determines if we should respect the privacy
	 */
	public function onAfterStorySave()
	{
		$userid=JFactory::getUser()->id;
		$content=$_POST['content'];

		require_once JPATH_SITE.'/components/com_broadcast/helper.php';

		$combroadcastHelper=new combroadcastHelper();
		//if User not connected to any social api return
		$config->supplier='broadcast';
		$config->userid=$userid;
		$connectionstatus=$combroadcastHelper->getuserconnectionstatus($config);
		if(!$connectionstatus)
		return;
		$user_settings=$combroadcastHelper->getusersetting($userid);
		if($user_settings)
		$subscribedapp	= explode('|',$user_settings);
		$config=new stdClass;
		if(isset($_POST['links_url']))
		{
			if(in_array('links',$subscribedapp))
			$linkurl=$_POST['links_url'];
			else
			return;
		}

		else if(isset($_POST['photos']['0']) )
		{
			if(!in_array('photos',$subscribedapp))
			return;

			$photo = Foundry::table( 'Photo' );
			$photo->load($_POST['photos']['0'] );
			// $size can be small / large, you can see the source code :)
			$linkurl=$photo->getSource( 'large' );

		}
		if($linkurl)
			$content.=" ".$linkurl;
			if(file_exists(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php'))
			require_once(JPATH_SITE .DS. 'components'.DS.'com_broadcast'.DS.'helper.php');

			$combroadcastHelper=new combroadcastHelper();
			$combroadcastHelper->addtoQueue($userid, $content, date('Y-m-d H:i:s',time()),1,0,'','com_easysocial',1);


	}



	/*
	 * Triggers the preparation of activity logs which appears in the user's activity log.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialStreamItem	The stream object.
	 * @param	bool				Determines if we should respect the privacy
	 */
	public function onPrepareActivityLog( SocialStreamItem &$item, $includePrivacy = true )
	{

	}

	/**
	 * Triggers after a like is saved.
	 *
	 * This trigger is useful when you want to manipulate the likes process.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableLikes	The likes object.
	 *
	 * @return	none
	 */
	public function onAfterLikeSave( &$likes )
	{
	}

	/**
	 * Triggered when a comment save occurs.
	 *
	 * This trigger is useful when you want to manipulate comments.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialTableComments	The comment object
	 * @return
	 */
	public function onAfterCommentSave( &$comment )
	{
	}

	/**
	 * Renders the notification item that is loaded for the user.
	 *
	 * This trigger is useful when you want to manipulate the notification item that appears
	 * in the notification drop down.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onNotificationLoad( $item )
	{
	}

}
