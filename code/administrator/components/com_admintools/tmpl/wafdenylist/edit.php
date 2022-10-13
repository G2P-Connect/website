<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

/** @var \Akeeba\Component\AdminTools\Administrator\View\Wafdenylist\HtmlView $this */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

HTMLHelper::_('behavior.formvalidator');

?>
<form action="<?php echo Route::_('index.php?option=com_admintools&view=wafdenylist&layout=edit&id=' . $this->item->id); ?>"
      aria-label="<?= Text::_('COM_ADMINTOOLS_TITLE_WAFDENYLIST_EDIT', true) ?>"
      class="form-validate" id="adminForm" method="post" name="adminForm">

	<div class="card card-body mb-2">
		<?php foreach ($this->form->getFieldset('details') as $field): ?>
			<?= $field->renderField(); ?>
		<?php endforeach; ?>
	</div>

	<input type="hidden" name="task" value="">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
