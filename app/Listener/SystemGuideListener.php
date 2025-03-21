<?php
declare(strict_types=1);

namespace App\Listener;

use App\Interface\SchemaInterface;
use Hyperf\Contract\ContainerInterface;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use Hyperf\Support\Composer;
use InvalidArgumentException;
use PDO;
use PDOException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * 系统引导
 *
 * @SystemGuideListener
 * @\App\Listener\SystemGuideListener
 */
#[Listener(priority: 999)]
final readonly class SystemGuideListener implements ListenerInterface
{
	private ArgvInput    $input;
	private SymfonyStyle $output;

	public function __construct(private ContainerInterface $container)
	{
		$this->input  = new ArgvInput;
		$this->output = new SymfonyStyle($this->input, new ConsoleOutput);
	}

	/**
	 * @return string[]
	 */
	public function listen(): array
	{
		return [
			BootApplication::class,
		];
	}

	/**
	 * @param object                  $event
	 *
	 * @phpstan-param BootApplication $event
	 *
	 * @return void
	 */
	public function process(object $event): void
	{
		if (file_exists(ROOT_PATH . '/.env')) return;

		$host     = $this->output->ask('请输入数据库连接地址', 'localhost');
		$port     = $this->output->ask('请输入数据库连接端口', '3306');
		$username = $this->output->ask('请输入数据库帐号', 'root');
		$password = $this->output->ask(
			question : '请输入数据库密码',
			validator: fn(null|string $value) => $value ?: throw new InvalidArgumentException('密码不能为空'),
		);
		$dbname   = $this->output->ask(
			question : '请输入数据库名称',
			validator: fn(null|string $value) => $value ?: throw new InvalidArgumentException('密码不能为空'),
		);

		$this->createDatabase($host, $port, $dbname, $username, $password);

		$this->initialization();

		$this->output->success('Database initialization completed.');
	}

	/**
	 * 创建数据库
	 *
	 * @param string $host     数据库地址
	 * @param string $dbname   数据库名称
	 * @param string $port     端口
	 * @param string $username 用户名
	 * @param string $password 密码
	 *
	 * @return void
	 */
	private function createDatabase(
		string $host,
		string $dbname,
		string $port,
		string $username,
		string $password,
	): void
	{
		//  当前仅支持 MySQL 数据库
		try {
			$pdo = new PDO(
				sprintf('mysql://host=%s::%s;', $host, $port),
				$username,
				$password,
			);
		}
		catch (PDOException $PDOException) {
			throw new InvalidArgumentException($PDOException->getMessage());
		}

		$result = $pdo->query("CREATE DATABASE IF NOT EXISTS `$dbname`");

		var_dump($result->fetchAll(PDO::FETCH_ASSOC));
	}

	/**
	 * 初始化所有数据表结构及其初始数据
	 *
	 * @return void
	 */
	private function initialization(): void
	{
		/**
		 * @var array<string, string> $classMap
		 */
		$classMap = Composer::getLoader()->getClassMap();

		foreach ($classMap as $classname => $path) {
			if (
				!str_starts_with($classname, 'App\\Schema\\') ||
				!in_array(SchemaInterface::class, class_implements($classname))
			) continue;

			try {
				/**
				 * @var SchemaInterface $class
				 */
				$class = $this->container->get($classname);
			}
			catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
				throw new InvalidArgumentException($e->getMessage());
			}

			$class->handle();

			$this->output->success(
				sprintf('Data table [%s] has been created', $classname),
			);
		}
	}
}