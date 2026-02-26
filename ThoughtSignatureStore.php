<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\AI\Platform\Bridge\Gemini;

use Symfony\AI\Platform\Result\ToolCall;

/**
 * Stores Gemini thoughtSignature values alongside ToolCall instances.
 *
 * Gemini 3+ models require thoughtSignature to be sent back with functionCall
 * parts. Since ToolCall is a generic platform class without metadata support,
 * this WeakMap-based store carries the signature from ResultConverter (read)
 * to AssistantMessageNormalizer (write) without modifying upstream classes.
 *
 * @see https://ai.google.dev/gemini-api/docs/thought-signatures
 */
final class ThoughtSignatureStore
{
    /** @var \WeakMap<ToolCall, string> */
    private static \WeakMap $signatures;

    public static function store(ToolCall $toolCall, string $signature): void
    {
        self::$signatures ??= new \WeakMap();
        self::$signatures[$toolCall] = $signature;
    }

    public static function get(ToolCall $toolCall): ?string
    {
        self::$signatures ??= new \WeakMap();

        return self::$signatures[$toolCall] ?? null;
    }
}
