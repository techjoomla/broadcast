<?php
/**
* @package		%PACKAGE%
* @subpackge	%SUBPACKAGE%
* @copyright	Copyright (C) 2010 - 2012 %COMPANY_NAME%. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*
* %PACKAGE% is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

/**
 * Profile Embed view for Textbook app
 *
 * @since	1.0
 * @access	public
 */
class Easysocial_broadcastViewDashboard extends SocialAppsView
{
	/**
	 * This method is invoked automatically and must exist on this view.
	 *
	 * The contents displayed here will be returned via an AJAX call from the system.
	 *
	 * @since	1.0.0
	 * @access	public
	 * @param	int		The user id that is currently being viewed.
	 * @return 	void
	 */
	public function display( $userId )
	{
		// Requires the viewer to be logged in to access this app
		Foundry::requireLogin();

		// We want the user object from EasySocial so we can do funky stuffs.
		$user 	= Foundry::user( $userId );

		// Since we are on the canvas page, we have the flexibility to change the page title.
		

		// Set the page title. You can use JFactory::getDocument()->setTitle( 'title' ) as well.
		Foundry::page()->title( $title );

		// Load up the model
		
		
		// If you use the built in theme manager, the namespace is relative to the following folder,
		// /media/com_easysocial/apps/user/textbook/themes/default

		$namespace 	= 'profile/default';

		// Output the contents
		echo parent::display( $canvas );
	}
}
