<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Joomla\Plugin\System\AdminTools\Feature;

defined('_JEXEC') || die;

use Exception;
use Joomla\CMS\Language\Text;
use RuntimeException;

/**
 * Self protection.
 *
 * Monitors whenever someone tries to unpublish the Admin Tools pluign, overriding the action.
 */
class ProtectAgainstDeactivation extends Base
{
	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		$enabled = $this->wafParams->getValue('selfprotect', 1) == 1;

		return $enabled & $this->app->isClient('administrator');
	}

	/**
	 * Disables creating new admins or updating new ones
	 */
	public function onAfterInitialise()
	{
		$input  = $this->input;
		$option = $input->getCmd('option', '');
		$task   = $input->getCmd('task', '');

		if ($option != 'com_plugins')
		{
			return;
		}

		$this->onDirectUnpublish($task);

		$this->onApplyOrSave($task);
	}

	/**
	 * Gets the extennsion ID for the System - Admin Tools plugin
	 *
	 * @return  int|null  The ID or null on failure
	 */
	protected function getPluginId()
	{
		$db    = $this->db;
		$query = $db->getQuery(true)
			->select($db->qn('extension_id'))
			->from($db->qn('#__extensions'))
			->where($db->qn('type') . ' = ' . $db->q('plugin'))
			->where($db->qn('element') . ' = ' . $db->q('admintools'))
			->where($db->qn('folder') . ' = ' . $db->q('system'));

		try
		{
			return $db->setQuery($query)->loadResult();
		}
		catch (Exception $e)
		{
			return null;
		}
	}

	/**
	 * Handles the case of someone directly unpublishing the plugin from the Plugin Manager interface
	 *
	 * @param   string  $task
	 */
	private function onDirectUnpublish($task)
	{
		$allowedTasks = ['unpublish', 'plugins.unpublish'];

		if (!in_array($task, $allowedTasks))
		{
			return;
		}

		// Get a list of all IDs in the request
		$ids   = $this->input->get('cid', [], 'array');
		$ids[] = $this->input->getInt('id', null);

		// Get the plugin ID for System - Admin Tools
		$ourId = $this->getPluginId();

		if (is_null($ourId) || empty($ourId))
		{
			return;
		}

		// Does the ID exist in the array? We need to be thorough, we can't do a simple in_array.
		foreach ($ids as $id)
		{
			$id = (int) trim($id);

			if ($id == $ourId)
			{
				throw new RuntimeException(Text::_('JGLOBAL_AUTH_ACCESS_DENIED'), 403);
			}
		}
	}

	/**
	 * Handles the case of someone directly unpublishing the plugin from the Plugin Manager interface
	 *
	 * @param   string  $task
	 */
	private function onApplyOrSave($task)
	{
		$allowedTasks = ['apply', 'save', 'plugins.apply', 'plugins.save', 'plugin.apply', 'plugin.save'];

		if (!in_array($task, $allowedTasks))
		{
			return;
		}

		// Get a list of all IDs in the request
		$ids   = $this->input->get('cid', [], 'array');
		$ids[] = $this->input->getInt('id', null);
		$ids[] = $this->input->getInt('extension_id', null);

		// Get the plugin ID for System - Admin Tools
		$ourId = $this->getPluginId();

		if (is_null($ourId) || empty($ourId))
		{
			return;
		}

		// Does the ID exist in the array? We need to be thorough, we can't do a simple in_array.
		$found = false;

		foreach ($ids as $id)
		{
			$id = (int) trim($id ?? '');

			if ($id == $ourId)
			{
				$found = true;

				break;
			}
		}

		if (!$found)
		{
			return;
		}

		// Get the form data and look for the enabled field
		$jform = $this->input->get('jform', [], 'array');

		if (!isset($jform['enabled']))
		{
			// Not saving the "enabled" value
			return;
		}

		if ($jform['enabled'] == 1)
		{
			// The plugin is being activated
			return;
		}

		// Apparently someone tries to activate the plugin. NOPE.
		throw new RuntimeException(Text::_('JGLOBAL_AUTH_ACCESS_DENIED'), 403);
	}
}
