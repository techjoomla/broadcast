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
 * Dashboard widget for textbook
 *
 * @since	1.0
 * @access	public
 */
class Easysocial_broadcastWidgetsProfile extends SocialAppsWidgets
{

	/**
	 * sidebarBottom position
	 *
	 * This method will be displayed on the sidebar bottom of the dashboard.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id.
	 * @return	string	The output of your widget.
	 */
	public function sidebarBottom( $userId )
	{
	}

	/**
	 * sidebarBottom position
	 *
	 * This method will be displayed on the sidebar bottom of the dashboard.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		The user's id.
	 * @return	string	The output of your widget.
	 */
	public function sidebarTop( $userId )
	{
	}

	/**
	 * aboveHeader position
	 *
	 * Output of this method will be displayed on the person's profile above the header.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return	string	The output of your widget.
	 */
	public function aboveHeader( $userId )
	{
	}

}
