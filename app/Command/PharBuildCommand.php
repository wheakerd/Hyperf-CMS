<?php
declare(strict_types=1);

namespace App\Command;

use App\Utils\Functions;
use Hyperf\Command\Annotation\Command;
use Hyperf\Phar\BuildCommand;
use InvalidArgumentException;
use Phar;
use Throwable;
use UnexpectedValueException;
use function Hyperf\Support\env;

/**
 * @RunPharBuildCommand
 * @\App\Command\RunPharBuildCommand
 */
#[Command(name: 'run:build')]
final class PharBuildCommand extends BuildCommand
{
	private string $bin             = 'bin/hyperf.php';
	private string $pharVersionPath = BASE_PATH . '/.phar.version';
	private string $version         = '0.0.0';

	/**
	 * @return void
	 */
	public function handle(): void
	{
		$this->input->setOption('name', $this->getPackageName());
		$this->input->setOption('bin', $this->getMain());
		$this->input->setOption('path', BASE_PATH);
		$this->input->setOption('phar-version', $this->getPharVersion());
		$this->input->setOption('mount', [
			'.env',
			'assets',
		]);

		$pharFile = BASE_PATH . DIRECTORY_SEPARATOR . $this->getPackageName();

		if (file_exists($pharFile) && is_file($pharFile)) {
			unlink($pharFile);
		}

		parent::handle();

		$phar              = new Phar($pharFile);
		$phar['index.php'] = <<<PHP
<?php
require __DIR__ . '/bin/hyperf.php';
__HALT_COMPILER(); ?>
PHP;
		$phar->stopBuffering();

		$this->line('PHAR package created successfully.', 'info');
	}

	public function getMain(): string
	{
		$bin = BASE_PATH . DIRECTORY_SEPARATOR . $this->bin;

		if (file_exists($bin) && is_file($bin)) {
			return $this->bin;
		}

		throw new InvalidArgumentException('No execution entry file exists.');
	}

	private function getPharVersion(): string
	{
		if (!file_exists($this->pharVersionPath)) {
			file_put_contents($this->pharVersionPath, '0.0.0');
		} else {
			//  Read the current version number
			$this->version = trim(file_get_contents($this->pharVersionPath));
		}

		//  Check if the version number format is valid
		if (preg_match('/^\d+\.\d+\.\d+$/', $this->version) !== 1) {
			throw new InvalidArgumentException('Invalid version format in .phar.version.');
		}

		$newVersion = Functions::incrementVersion($this->version);

		try {
			file_put_contents($this->pharVersionPath, $newVersion);
			$this->line("Version updated successfully to $newVersion.", 'info');
		}
		catch (Throwable $e) {
			$this->line('Failed to update version: ' . $e->getMessage(), 'info');
		}

		return $newVersion;
	}

	public function getPackageName(): string
	{
		$name = env('APP_NAME');

		if (is_null($name)) {
			throw new UnexpectedValueException(
				'APP_NAME does not exist in your .env file, please update your .env',
			);
		}

		return "$name.phar";
	}
}