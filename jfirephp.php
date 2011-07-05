<?php
/**
 * @version		$Id$
 * @package		Kunena
 * @subpackage	com_kunena
 * @copyright	Copyright (C) 2010 Kunena Team. All rights reserved.
 * @license		GNU General Public License <http://www.gnu.org/copyleft/gpl.html>
 * @link		http://www.kunena.com
 */

// no direct access
defined('_JEXEC') or die();

jimport( 'joomla.plugin.plugin' );

class plgSystemJFirePHP extends JPlugin
{

	/**
	 * onAfterInitialise handler
	 *
	 * Register FirePHP libraries and set options according to paramters
	 *
	 * @access	public
	 * @return null
	 */

	public function onAfterInitialise()
	{
		require_once 'jfirephp'.DS.'firephpcore'.DS.'fb.php';

		// JFirePHP is installed and loaed
		define('JFIREPHP', 1);

		// Before doing any checks lets disable logging
		FB::setEnabled(false);

		// Check if the integration is set to enabled
		$enable = (bool) $this->params->get('enable', 0);

		// Only turn on if enabled
		if ($enable) {

			// if limited to debug mode, check JDEBUG
			$limittodebug = (bool) $this->params->get('limittodebug', 1);
			if ( $limittodebug == false || JDEBUG) {
				// We are enabled and either in Debug mode, or it does not matter
				FB::setEnabled(true);

				$verbose = (bool) $this->params->get('verbose', 0);

				if ($verbose) {
					FB::group('JFirePHP Startup',array('Collapsed' => true,'Color' => '#FF4000'));
					FB::log('JFirePHP enabled! - Verbose Output Mode: ON');
				}

				$options = array('maxObjectDepth' => intval($this->params->get('maxObjectDepth', 10)),
				                 'maxArrayDepth' => intval($this->params->get('maxArrayDepth', 20)),
				                 'useNativeJsonEncode' => intval($this->params->get('useNativeJsonEncode', 1)),
				                 'includeLineNumbers' => intval($this->params->get('includeLineNumbers', 1)));

				FB::setOptions($options);

				if ($verbose) {
					FB::log('JFirePHP: Options Set - maxObjectDepth:'.$options['maxObjectDepth'].
							 ' maxArrayDepth:'.$options['maxArrayDepth'].
							 ' useNativeJsonEncode:'.$options['useNativeJsonEncode'].
							 ' includeLineNumbers:'.$options['includeLineNumbers']);
				}

				$redirectphp = (bool) $this->params->get('redirectphp', 0);

				if ($redirectphp) {
					// Convert E_WARNING, E_NOTICE, E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE and
					// E_RECOVERABLE_ERROR errors to ErrorExceptions and send all Exceptions to Firebug automatically
					FB::registerErrorHandler(true);
					FB::registerExceptionHandler();
					FB::registerAssertionHandler(true, false);

					if($verbose) FB::log('JFirePHP: E_WARNING, E_NOTICE, E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE and E_RECOVERABLE_ERROR redirected.');
				}

				if ($verbose) FB::groupEnd();
			}
		}
	}

}
