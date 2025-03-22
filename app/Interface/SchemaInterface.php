<?php
declare(strict_types=1);

namespace App\Interface;

/**
 * @SchemaInterface
 * @\App\Interface\SchemaInterface
 */
interface SchemaInterface
{
	/**
	 * 创建数据库结构
	 * 载入初始数据
	 *
	 * @return void
	 */
	public function handle(): void;
}