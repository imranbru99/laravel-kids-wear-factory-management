<?php

declare(strict_types=1);

namespace App\Modules\KidsWear\Support;

use ImranDevBd\AiHub\Facades\AIHub;
use Throwable;

class AiAssistant
{
    public function suggest(string $purpose, string $prompt, string $fallback): string
    {
        try {
            $response = AIHub::prompt($prompt)->system(
                'You advise a kids wear factory in Gazipur, Bangladesh. Be concrete, short, and operational. Reply in plain text.'
            )->send();

            $text = trim((string) ($response->content ?? ''));

            return $text !== '' ? $text : $fallback;
        } catch (Throwable $exception) {
            return $fallback."\n\nAI Hub was not called (".$exception->getMessage().').';
        }
    }
}
