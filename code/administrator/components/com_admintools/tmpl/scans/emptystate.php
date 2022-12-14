<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Layout\LayoutHelper;

$displayData = [
	'textPrefix' => 'COM_ADMINTOOLS_SCANS',
	'formURL'    => 'index.php?option=com_admintools&view=scans',
	//'helpURL'    => 'https://docs.joomla.org/Special:MyLanguage/Adding_a_new_article',
	'createURL'  => '',
	'icon'       => 'fa fa-search',
];

$user = Factory::getApplication()->getIdentity();

if ($user->authorise('core.manage', 'com_admintools'))
{
	$displayData['createURL'] = '#newScan';
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);

echo $this->loadAnyTemplate('scans/scanmodal');
