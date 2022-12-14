<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2022 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Joomla\Plugin\System\AdminTools\Feature;

defined('_JEXEC') || die;

use Joomla\CMS\Date\Date;

class AwaySchedule extends Base
{
	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		if (!$this->app->isClient('administrator'))
		{
			return false;
		}

		if (!$this->wafParams->getValue('awayschedule_from') || !$this->wafParams->getValue('awayschedule_to'))
		{
			return false;
		}

		return true;
	}

	/**
	 * Checks if the secret word is set in the URL query, or redirects the user
	 * back to the home page.
	 */
	public function onAfterInitialise()
	{
		$timezone = $this->app->get('offset', 'UTC');

		$now  = new Date('now', $timezone);
		$from = new Date($this->wafParams->getValue('awayschedule_from'), $timezone);
		$to   = new Date($this->wafParams->getValue('awayschedule_to'), $timezone);

		// Wait, FROM is later than TO? This means that the user set an interval like this: 17:30 - 11:00
		// Let's move the FROM constrain one day back
		if ($from > $to)
		{
			$from = $from->modify('-1 day');
		}

		// Login attempt, while we set the away schedule, let's ban the user
		if ($now > $from && $now < $to)
		{
			$this->redirectAdminToHome();
		}
	}
}
