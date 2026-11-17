<?php

declare(strict_types=1);

namespace Admin\Observers;

use Admin\Models\ArticleVersion;
use Admin\Support\NextRevalidator;

final class ArticleVersionObserver
{
    public function saved(ArticleVersion $version): void
    {
        if ($version->status === ArticleVersion::STATUS_PUBLISHED || $version->getOriginal('status') === ArticleVersion::STATUS_PUBLISHED) {
            $version->loadMissing(['authors', 'categories']);
            NextRevalidator::revalidate(NextRevalidator::articleTags($version));
        }
    }

    public function deleted(ArticleVersion $version): void
    {
        if ($version->status === ArticleVersion::STATUS_PUBLISHED || $version->getOriginal('status') === ArticleVersion::STATUS_PUBLISHED) {
            $version->loadMissing(['authors', 'categories']);
            NextRevalidator::revalidate(NextRevalidator::articleTags($version));
        }
    }
}
