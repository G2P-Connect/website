<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\View\Htaccessmaker;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Helper\ServerTechnology;
use Akeeba\Component\AdminTools\Administrator\Model\HtaccessmakerModel;
use Akeeba\Component\AdminTools\Administrator\Model\ServerconfigmakerModel;
use Akeeba\Component\AdminTools\Administrator\View\Mixin\LoadAnyTemplate;
use Akeeba\Component\AdminTools\Administrator\View\Mixin\TaskBasedEvents;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	use TaskBasedEvents;
	use LoadAnyTemplate;

	/**
	 * Server configuration file contents for preview
	 *
	 * @var  string
	 */
	public $configFile;

	/**
	 * Is this supported? 0 No, 1 Yes, 2 Maybe
	 *
	 * @var  int
	 */
	public $isSupported;

	/**
	 * Should I enable www and non-www redirects, based on the value of $live_site?
	 *
	 * @var bool
	 */
	public $enableRedirects;

	/**
	 * The Joomla form used to generate the controls
	 *
	 * @var Form
	 */
	public $form;

	protected function onBeforePreview()
	{
		/** @var ServerconfigmakerModel $model */
		$model            = $this->getModel();
		$this->configFile = $model->makeConfigFile();
		$this->setLayout('plain');
	}

	protected function onBeforeMain()
	{
		$this->addToolbar();

		/** @var ServerconfigmakerModel $model */
		$model                 = $this->getModel();
		$this->form            = $model->getForm();
		$this->isSupported     = $model->isSupported();
		$this->enableRedirects = $model->enableRedirects();
	}

	protected function addToolbar()
	{
		$view = $this->getName();

		ToolbarHelper::title(Text::_('COM_ADMINTOOLS_TITLE_' . $view), 'admintools');

		$bar = Toolbar::getInstance('toolbar');

		$saveGroup = $bar->dropdownButton('save-group');
		$childBar  = $saveGroup->getChildToolbar();

		$childBar->apply('apply', 'COM_ADMINTOOLS_' . $view . '_LBL_APPLY');
		$childBar->save('save', 'COM_ADMINTOOLS_' . $view . '_LBL_SAVE');

		$bar->confirmButton('reset', 'COM_ADMINTOOLS_' . $view . '_LBL_RESET', 'reset')
			->icon('fa fa-bolt')
			->buttonClass('btn btn-danger')
			->message('COM_ADMINTOOLS_LBL_SERVERTECH_RESET_CONFIRM');

		$bar->popupButton('preview', 'Preview', 'preview')
			->icon('fa fa-file-code')
			->url(Route::_('index.php?option=com_admintools&view=' . $view . '&task=preview&tmpl=component'))
			->bodyHeight(380)
			->modalWidth(600);

		ToolbarHelper::back('JTOOLBAR_BACK', Route::_('index.php?option=com_admintools'));

		if (version_compare(JVERSION, '4.1.0', 'ge'))
		{
			// TODO Enable me if we ever add descriptions on the .htaccess Maker form
			// ToolbarHelper::inlinehelp();
		}

		ToolbarHelper::help(null, false, 'https://www.akeeba.com/documentation/admin-tools-joomla/htaccess-maker.html');
	}
}