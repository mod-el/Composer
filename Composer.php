<?php namespace Model\Composer;

use Model\Core\Module;

class Composer extends Module
{
	public function check(): string
	{
		return $this->exec();
	}

	public function initCommand(): string
	{
		return $this->exec('init', '-n');
	}

	public function installCommand(): string
	{
		return $this->exec('install', '-no');
	}

	public function updateCommand(): string
	{
		return $this->exec('update', '-no');
	}

	private function exec(string $command = '', string $options = ''): string
	{
		exec('composer ' . $command . ' ' . $options . ' 2>&1', $outputArr, $return);

		$output = implode("\n", $outputArr);
		if ($return !== 0)
			throw new \Exception($output);

		return $output;
	}

	public function getJson(): ?array
	{
		$composerFilePath = INCLUDE_PATH . 'composer.json';
		if (file_exists($composerFilePath))
			return json_decode(file_get_contents($composerFilePath), true);
		return null;
	}

	public function addToJson(string $package, string $version = '*')
	{
		$json = $this->getJson();

		$packages = $json['require'];
		if (!isset($packages[trim($package)]))
			$packages[trim($package)] = trim($version);

		$this->saveJsonPackages($packages, $json);
	}

	public function saveJsonPackages(array $packages, ?array $json = null): ?string
	{
		if ($json === null)
			$json = $this->getJson();

		if (json_encode($packages, JSON_FORCE_OBJECT) !== json_encode($json['require'], JSON_FORCE_OBJECT)) {
			$json['require'] = $packages;
			file_put_contents(INCLUDE_PATH . 'composer.json', json_encode($json, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT));

			return $this->installCommand();
		}
		return null;
	}
}
