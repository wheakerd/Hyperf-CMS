<?php
declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Annotation\Command;
use Hyperf\Command\Command as HyperCommand;
use InvalidArgumentException;
use Throwable;
use UnexpectedValueException;
use function Hyperf\Support\env;

/**
 * @RunPharBuildCommand
 * @\App\Command\RunPharBuildCommand
 */
#[Command(name: 'run:build')]
final class RunPharBuildCommand extends HyperCommand
{
	protected string $exec = '%s %s/bin/hyperf.php phar:build --name=%s --phar-version=%s --bin=%s --path=%s -M .env';

	protected string $bin = PHP_BINARY;

	protected string $pharVersionPath = BASE_PATH . '/.phar.version';

	protected string $version;

	/**
	 * @return void
	 */
	public function handle(): void
	{
		if (!file_exists($this->pharVersionPath)) {
			throw new InvalidArgumentException('.phar.version file not found.');
		}
		//  Read the current version number
		$this->version = trim(file_get_contents($this->pharVersionPath));
		//  Check if the version number format is valid
		if (preg_match('/^\d+\.\d+\.\d+$/', $this->version) !== 1) {
			throw new InvalidArgumentException('Invalid version format in .phar.version.');
		}

		$this->line('Building project as Phar package...', 'info');

		if (file_exists($file = BASE_PATH . '/' . $this->getPharName() . '.phar')) {
			// Delete the historical PHAR files
			if (unlink($file)) {
				$this->line('\'' . $this->getPharName() . '.phar\' has been deleted successfully.', 'info');
			} else {
				throw new UnexpectedValueException(
					'Failed to delete \'' . $this->getPharName() . '.phar\'.',
				);
			}
		}

		var_dump(
			sprintf(
				$this->exec,
				$this->getBin(),
				BASE_PATH,
				$this->getPharName(),
				$this->version,
				'bin/hyperf.php',
				BASE_PATH,
			),
		);

		exec(
			sprintf(
				$this->exec,
				$this->getBin(),
				BASE_PATH,
				$this->getPharName(),
				$this->version,
				'bin/hyperf.php',
				BASE_PATH,
			),
		);

		$this->writeNewVersion();
	}

	public function getBin(): string
	{
		return $this->bin;
	}

	/**
	 * Write the new version number to the file
	 *
	 * @return void
	 */
	private function writeNewVersion(): void
	{
		[
			$major,
			$minor,
			$patch,
		] = explode('.', $this->version);
		$patch++;

		$newVersion = $major . '.' . $minor . '.' . $patch;

		try {
			file_put_contents($this->pharVersionPath, $newVersion);
			$this->line("Version updated successfully to $newVersion.", 'info');
		}
		catch (Throwable $e) {
			$this->line('Failed to update version: ' . $e->getMessage(), 'info');
		}
	}

	public function getPharName(): string
	{
		$name = env('APP_NAME');

		if (is_null($name)) {
			throw new UnexpectedValueException(
				'APP_NAME does not exist in your .env file, please update your .env',
			);
		}

		return $name . '.phar';
	}
}