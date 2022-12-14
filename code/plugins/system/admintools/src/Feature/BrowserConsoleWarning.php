<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Joomla\Plugin\System\AdminTools\Feature;

defined('_JEXEC') || die;

use Joomla\CMS\Language\Text;


class BrowserConsoleWarning extends Base
{
	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return ($this->wafParams->getValue('consolewarn', 0) == 1);
	}

	/**
	 * Inject some Javascript to display a warning inside browser console
	 *
	 * Please note: Since we're injecting javascript, we have to do that as late as possible, otherwise the document
	 * is not yet created and Joomla will create a new one for us, resulting in a vast collection of possible
	 * side-effects
	 */
	public function onBeforeRender()
	{
		// There's nothing to steal if you're a guest
		if ($this->app->getIdentity()->guest)
		{
			return;
		}

		$document = $this->app->getDocument();

		// Only work with HTML documents
		if ($document->getType() != 'html')
		{
			return;
		}

		$tmpl = $this->input->getCmd('tmpl', '');

		// We have some forced template? Better stop here
		if ($tmpl != '')
		{
			return;
		}

		$this->parentPlugin->loadLanguage('com_admintools');

		$warn_title = Text::_('COM_ADMINTOOLS_COMMON_LBL_CONSOLEWARN_TITLE', true);
		$body1      = Text::_('COM_ADMINTOOLS_COMMON_LBL_CONSOLEWARN_BODY1', true);
		$body2      = Text::_('COM_ADMINTOOLS_COMMON_LBL_CONSOLEWARN_BODY2', true);

		// Guess what? Coloured background works everywhere EXCEPT IE
		$js = <<<JS
// Internet Explorer 6-11
var isIE = /*@cc_on!@*/false || !!document.documentMode;
// Edge 20+
var isEdge = !isIE && !!window.StyleMedia;

if (!isIE && !isEdge)
{
    console.log('%c $warn_title ', 'font-size: 36px; color: red; font-weight: bold;');
    console.log('%c $body1 ', 'font-size: 14px;');
    console.log('%c $body2 ', 'font-size: 14px;');
}
JS;

		$document->addScriptDeclaration($js);
	}
}
