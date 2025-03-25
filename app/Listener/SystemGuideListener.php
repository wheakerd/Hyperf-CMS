<?php
declare(strict_types=1);

namespace App\Listener;

use App\Interface\SchemaInterface;
use App\Utils\Functions;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ContainerInterface;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;
use Hyperf\Support\Composer;
use Hyperf\Support\DotenvManager;
use InvalidArgumentException;
use PDO;
use PDOException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use function Hyperf\Support\make;

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

		$host      = $this->output->ask('请输入数据库连接地址', 'localhost');
		$port      = $this->output->ask('请输入数据库连接端口', '3306');
		$username  = $this->output->ask('请输入数据库帐号', 'root');
		$password  = $this->output->ask('请输入数据库密码', '123456');
		$dbname    = $this->output->ask('请输入数据库名称', '80b89cb6' ?: Functions::randomString(4));
		$charset   = $this->output->ask('请指定数据库编码集', 'utf8mb4');
		$collation = $this->output->ask('请指定数据库排序规则', 'utf8mb4_general_ci');

		$this->createDatabase($host, $port, $username, $password, $dbname, $charset, $collation);

		$this->initialization();

		$this->output->success('Database initialization completed.');
	}

	/**
	 * 创建数据库
	 *
	 * @param string $host      数据库地址
	 * @param string $port      数据库端口
	 * @param string $username  数据库用户名
	 * @param string $password  数据库密码
	 * @param string $dbname    数据库名称
	 * @param string $charset   数据库编码集
	 * @param string $collation 数据库排序规则
	 *
	 * @return void
	 */
	private function createDatabase(
		string $host,
		string $port,
		string $username,
		string $password,
		string $dbname,
		string $charset,
		string $collation,
	): void
	{
		//  当前仅支持 MySQL 数据库
		try {
			$pdo = new PDO(
				sprintf('mysql://host=%s::%s;', $host, $port),
				$username,
				$password,
				[
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				],
			);
		}
		catch (PDOException $PDOException) {
			exit($PDOException->getMessage() . "\n");
		}

		/** @noinspection SqlNoDataSourceInspection */
		$result = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname';");
		if ($result->fetchColumn()) {
			exit(sprintf('Database creation failed: [%s] database already exists', $dbname));
		}

		/** @noinspection SqlNoDataSourceInspection */
		$pdo->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET $charset COLLATE $collation");
		/** @noinspection SqlNoDataSourceInspection */
		$result = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname';");
		if (!$result->fetchColumn()) {
			exit(sprintf('Database [%s] creation failed', $dbname));
		}

		//  生成 .env 配置文件
		file_put_contents(ROOT_PATH . '/.env', "APP_NAME=Hyperf-CMS\nAPP_ENV=dev\n\n");
		file_put_contents(ROOT_PATH . '/.env', "DB_DRIVER=mysql\n", FILE_APPEND);
		file_put_contents(ROOT_PATH . '/.env', "DB_HOST=$host\n", FILE_APPEND);
		file_put_contents(ROOT_PATH . '/.env', "DB_PORT=$port\n", FILE_APPEND);
		file_put_contents(ROOT_PATH . '/.env', "DB_DATABASE=$dbname\n", FILE_APPEND);
		file_put_contents(ROOT_PATH . '/.env', "DB_USERNAME=$username\n", FILE_APPEND);
		file_put_contents(ROOT_PATH . '/.env', "DB_PASSWORD=$password\n", FILE_APPEND);
		file_put_contents(ROOT_PATH . '/.env', "DB_CHARSET=$charset\n", FILE_APPEND);
		file_put_contents(ROOT_PATH . '/.env', "DB_COLLATION=$collation\n", FILE_APPEND);
		file_put_contents(ROOT_PATH . '/.env', "DB_PREFIX=\n", FILE_APPEND);

		//  刷新 env 环境变量
		DotenvManager::load([ROOT_PATH]);

		//  重载 Hyperf\Config\Config
		$this->container->set(ConfigInterface::class, make(ConfigInterface::class));
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