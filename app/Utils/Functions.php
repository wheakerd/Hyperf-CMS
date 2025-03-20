<?php
declare(strict_types=1);

namespace App\Utils;

/**
 * @Functions
 * @\App\Functions
 */
final class Functions
{
    /**
     * 将一维数组转换树状结构
     * @param array $list
     * @param string $primaryKey
     * @param string $parentKey
     * @param int|null $key
     * @return array
     */
    static
    public function list(
        array  &$list,
        string $primaryKey,
        string $parentKey,
        ?int   $key = null,
    ): array
    {
        $data = [];

        foreach ($list as $item) {
            if ($item [$parentKey] !== $key) continue;

            $children = self::list(
                $list,
                $primaryKey,
                $parentKey,
                $item[$primaryKey],
            );

            $data [] = empty($children) ? $item : $item + compact('children');

            unset($item);
        }

        return $data;
    }

    /**
     * 生成安全的随机长度的 ASCII 组成的单字节字符串
     * @param int $length
     * @param bool $upper 是否将 ASCII 字符串转化为大写，
     * @return string
     */
    static
    public function randomString(int $length, bool $upper = false): string
    {
        $string = bin2hex(openssl_random_pseudo_bytes($length));
        return $upper ? strtoupper($string) : $string;
    }

    /**
     * 生成微信订单编号
     * @param int $id
     * @return string
     */
    static
    public function genWechatOrderCode(int $id = 0): string
    {
        $id = str_pad((string)$id, 3, '0', STR_PAD_LEFT);

        /**
         * @var int $nanoseconds 微妙
         * @var int $second 秒
         */
        [$nanoseconds, $second] = hrtime();

        $chars = uniqid();

        return $id . $second . '_' . $nanoseconds . $chars;
    }

    static
    public function incrementVersion($version): string
    {
        // 将版本号字符串拆分成数组
        $versionParts = explode('.', $version);

        // 从右到左递增每个部分
        for ($i = count($versionParts) - 1; $i >= 0; $i--) {
            // 将当前部分转为整数并增加1
            $versionParts[$i] = (int)$versionParts[$i] + 1;

            // 如果当前部分大于等于10，则进位
            if ($versionParts[$i] >= 10) {
                $versionParts[$i] = 0;
                if ($i > 0) {
                    $versionParts[$i - 1] = (int)$versionParts[$i - 1] + 1;
                }
            } else {
                break; // 如果没有超过10，则无需继续进位
            }
        }

        // 将版本号数组重新组合成字符串
        return implode('.', $versionParts);
    }
}