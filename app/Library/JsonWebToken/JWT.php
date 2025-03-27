<?php
declare(strict_types=1);

namespace App\Library\JsonWebToken;

use Carbon\Carbon;
use InvalidArgumentException;
use Jose\Component\Checker\{
	AlgorithmChecker,
	HeaderCheckerManager,
};
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWK;
use Jose\Component\Signature\{
	JWSBuilder,
	JWSLoader,
	JWSTokenSupport,
	JWSVerifier,
	Serializer\CompactSerializer,
	Serializer\JWSSerializerManager
};

/**
 * @JsonWebSignature
 * @\App\Library\JWT\JsonWebSignature
 *
 * @see https://web-token.spomky-labs.com/
 */
readonly class JWT
{
	public function __construct(private AlgorithmManager $signatureAlgorithmManager, private JWK $jwk)
	{
	}

	/**
	 * @/param string $iss 颁发者
	 * @/param array $aud 受众者
	 *
	 * @param mixed    $payload        载荷信息
	 * @param int      $expirationTime 有效时长，默认一个小时
	 * @param int|null $nowTime        令牌生效时间
	 *
	 * @return string
	 */
	public function create(mixed $payload = [], int $expirationTime = 60 * 60 * 24 * 30, ?int $nowTime = null): string
	{
		$nowTime ??= Carbon::now()->getTimestamp();

		$payload = json_encode(
			[
				"iat"  => $nowTime,
				"nbf"  => $nowTime,
				"exp"  => $nowTime + $expirationTime,
				//                "iss" => $iss,
				//                "aud" => $aud,
				"data" => $payload,
			],
		);

		$jwsBuilder = new JWSBuilder(
			$this->signatureAlgorithmManager,
		);

		$jws = $jwsBuilder->create()
			->withPayload($payload)
			->addSignature($this->jwk, ['alg' => 'HS256'])
			->build();

		$serializer = new CompactSerializer();

		return $serializer->serialize($jws, 0);
	}

	/**
	 * 校验
	 *
	 * @param string $token 令牌
	 *
	 * @return bool
	 */
	public function check(string $token): bool
	{
		$serializerManager = new JWSSerializerManager(
			[
				new CompactSerializer(),
			],
		);

		try {
			$jws = $serializerManager->unserialize($token);
		}
		catch (InvalidArgumentException) {
			return false;
		}

		$jwsVerifier = new JWSVerifier($this->signatureAlgorithmManager);

		return $jwsVerifier->verifyWithKey($jws, $this->jwk, 0);
	}

	/**
	 * 换取令牌
	 *
	 * @param string $token
	 * @param int    $expirationTime
	 *
	 * @return false|string
	 */
	public function barter(string $token, int $expirationTime = 3600): false|string
	{
		return $this->create(
			payload       : (array)$this->getPayload($token),
			expirationTime: $expirationTime,
		);
	}

	/**
	 * 获取载荷
	 *
	 * @param string $token
	 *
	 * @return object{
	 *
	 * }
	 */
	public function getPayload(string $token): object
	{
		$serializerManager = new JWSSerializerManager(
			[
				new CompactSerializer(),
			],
		);

		$jwsVerifier = new JWSVerifier(
			$this->signatureAlgorithmManager,
		);

		$headerCheckerManager = new HeaderCheckerManager(
			[
				new AlgorithmChecker(
					[
						"HS256",
					],
				),
			],
			[
				new JWSTokenSupport(),
			],
		);

		$jwsLoader = new JWSLoader(
			$serializerManager,
			$jwsVerifier,
			$headerCheckerManager,
		);

		$jws = $jwsLoader->loadAndVerifyWithKey($token, $this->jwk, $recipient);

		return (object)json_decode($jws->getPayload());
	}
}