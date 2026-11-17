<?php

declare(strict_types=1);

namespace Admin\Domains\Post\Actions;

use Admin\Models\Post;
use Admin\Models\GalleryUsage;

final class PostDeleteAction
{
    public function execute(Post $post): void
    {
        try {
            GalleryUsage::query()
                ->where('usage_type', 'posts')
                ->where('usage_id', $post->id)
                ->delete();
            $post->files()->detach();
        } catch (\Throwable $e) {
            // proceed even if cleanup partially fails
        }

        $post->delete();
    }
}
