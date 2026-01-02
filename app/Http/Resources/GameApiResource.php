<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Game  */
final class GameApiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[\Override]
    public function toArray(Request $request): array
    {
        $data =  [
            'id' => $this->getKey(),
            'tries_left' => $this->tries_left,
            'word' => $this->getWord(),
            'chars' => $this->characters_guessed,
            'status' => $this->status,
        ];

        if ('' !== $this->error) {
            $data['error'] = $this->error;
        }

        return $data;
    }
}
