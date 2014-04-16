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
class Easysocial_broadcastViewProfile extends SocialAppsView
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

		
		
	
		// Output the contents
		echo parent::display( $canvas );
	}
}
