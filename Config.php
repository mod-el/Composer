<?php namespace Model\Composer;

use Model\Core\Module_Config;

class Config extends Module_Config
{
	public $configurable = true;

	/**
	 * Returns the config template
	 *
	 * @param string $type
	 * @return string
	 */
	public function getTemplate(string $type): ?string
	{
		return $type === 'config' ? 'config' : null;
	}

	/**
	 * @param string $type
	 * @param array $data
	 * @return bool
	 */
	public function saveConfig(string $type, array $data): bool
	{
		if ($type !== 'config')
			return true;

		$json = $this->model->_Composer->getJson();

		if (!$json)
			throw new \Exception('composer.json was not found or it is invalid');

		$newRequire = [];
		foreach ($json['require'] as $package => $version) {
			if (isset($_POST['delete-' . $package]))
				continue;

			if (isset($_POST[$package . '-package'], $_POST[$package . '-version'])) {
				$newRequire[trim($_POST[$package . '-package'])] = trim($_POST[$package . '-version']);
			} else {
				$newRequire[$package] = $version;
			}
		}

		if (!empty($_POST['new-package']) and !empty($_POST['new-version']))
			$newRequire[trim($_POST['new-package'])] = trim($_POST['new-version']);

		$output = $this->model->_Composer->saveJsonPackages($newRequire, $json);
		if ($output)
			$this->model->viewOptions['messages'][] = nl2br($output);

		return true;
	}

	public function getConfigData(): ?array
	{
		return [];
	}
}
