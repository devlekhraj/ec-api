<?php

declare(strict_types=1);

namespace Admin\Domains\Shared\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Admin\Models\Gallery */
final class ImageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'filename' => $this->filename,
            'url' => $this->url,
        ];
    }
}
