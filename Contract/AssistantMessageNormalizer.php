<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\AI\Platform\Bridge\Gemini\Contract;

use Symfony\AI\Platform\Bridge\Gemini\Gemini;
use Symfony\AI\Platform\Contract\Normalizer\ModelContractNormalizer;
use Symfony\AI\Platform\Message\AssistantMessage;
use Symfony\AI\Platform\Model;

/**
 * @author Christopher Hertel <mail@christopher-hertel.de>
 */
final class AssistantMessageNormalizer extends ModelContractNormalizer
{
    /**
     * @param AssistantMessage $data
     *
     * @return array{array{text: string}}
     */
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        $normalized = [];

        if (null !== $data->getContent()) {
            $normalized['text'] = $data->getContent();
        }

        if ($data->hasToolCalls()) {
            $toolCall = $data->getToolCalls()[0];
            $normalized['functionCall'] = [
                'id' => $toolCall->getId(),
                'name' => $toolCall->getName(),
            ];

            if ($toolCall->getArguments()) {
                $normalized['functionCall']['args'] = $toolCall->getArguments();
            }

            $metadata = $toolCall->getMetadata();
            if (isset($metadata['thoughtSignature'])) {
                $normalized['thoughtSignature'] = $metadata['thoughtSignature'];
            }
        }

        return [$normalized];
    }

    protected function supportedDataClass(): string
    {
        return AssistantMessage::class;
    }

    protected function supportsModel(Model $model): bool
    {
        return $model instanceof Gemini;
    }
}
