<?php
declare(strict_types=1);

namespace PhiSYS\BaseConvert\Uuid;

use PhiSYS\BaseConvert\ArbitraryLengthBaseConvert;

class UuidBaseConvert
{
    private const RX_UUID = '/^[0-9a-f]{8}-?[0-9a-f]{4}-?[0-9a-f]{4}-?[0-9a-f]{4}-?[0-9a-f]{12}$/i';
    private const RX_BASE10_UUID = '/^0?\d{39}$/';
    private const RX_BASE64_UUID = '%^[a-z0-9+/]{22}={0,2}$%i';

    public static function isUuidString(string $string): bool
    {
        return 1 === \preg_match(self::RX_UUID, $string);
    }

    public static function isBase64Uuid(string $string): bool
    {
        return 1 === \preg_match(self::RX_BASE64_UUID, $string);
    }

    public static function encodeBase64Uuid(string $uuid): string
    {
        self::assertUuid($uuid);

        return \base64_encode(
            \hex2bin(
                \str_replace('-', '', $uuid),
            ),
        );
    }

    public static function decodeBase64Uuid(string $base64string): string
    {
        $hex = \bin2hex(\base64_decode($base64string, true));

        return self::hexToUuid($hex);
    }

    public static function isBase10Uuid(string $string): bool
    {
        return 1 === \preg_match(self::RX_BASE10_UUID, $string);
    }

    /**
     * Needs to be padded to 40 char with leading 0 to fit CODE128C
     */
    public static function encodeBase10Uuid(string $uuid): string
    {
        self::assertUuid($uuid);

        return \str_pad(
            ArbitraryLengthBaseConvert::baseConvert(
                \str_replace('-', '', $uuid),
                16,
                10,
            ),
            40,
            '0',
            \STR_PAD_LEFT,
        );
    }

    /**
     * Remove leading pad to go back to 39 char
     */
    public static function decodeBase10Uuid(string $numeric): string
    {
        $hex = \str_pad(
            ArbitraryLengthBaseConvert::baseConvert($numeric, 10, 16),
            32,
            '0',
            \STR_PAD_LEFT,
        );

        return self::hexToUuid($hex);
    }

    private static function hexToUuid(string $hex): string
    {
        if (32 !== \strlen($hex)) {
            throw new \InvalidArgumentException("Invalid UUID length after base conversion.");
        }

        return \strtolower(
            \substr($hex, 0, 8)
            . '-' . \substr($hex, 8, 4)
            . '-' . \substr($hex, 12, 4)
            . '-' . \substr($hex, 16, 4)
            . '-' . \substr($hex, 20, 12),
        );
    }

    public static function assertUuid(string $uuid): void
    {
        if (1 !== \preg_match(self::RX_UUID, $uuid)) {
            throw new \InvalidArgumentException("Invalid UUID for base convert.");
        }
    }

    public static function assertBase10Uuid($uuid): void
    {
        if (1 !== \preg_match(self::RX_BASE10_UUID, $uuid)) {
            throw new \InvalidArgumentException("Invalid Base10 encoded UUID.");
        }
    }
}
