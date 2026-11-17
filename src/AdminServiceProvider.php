<?php

declare(strict_types=1);

namespace Admin;

use Admin\Models\Article;
use Admin\Models\ArticleVersion;
use Admin\Models\Category;
use Admin\Models\Short;
use Admin\Console\Commands\ECimportCommand;
use Admin\Console\Commands\FixSectionImageUrlsCommand;
use Admin\Console\Commands\ImportPostFeaturedImagesCommand;
use Admin\Console\Commands\ImportPostsToArticlesCommand;
use Admin\Console\Commands\ImportWpFeaturedImagesCommand;
use Admin\Console\Commands\ListPostImageUrlsCommand;
use Admin\Console\Commands\PostPrimaryBannerCommand;
use Admin\Console\Commands\SyncArticleVersionMetaImagesCommand;
use Admin\Observers\ArticleObserver;
use Admin\Observers\ArticleVersionObserver;
use Admin\Observers\CategoryObserver;
use Admin\Observers\ShortObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

final class AdminServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Relation::morphMap([
            'article_versions' => ArticleVersion::class,
            'shorts' => Short::class,
            'live_updates' => \Admin\Models\LiveUpdate::class,
            'content_sections' => \Admin\Models\ContentSection::class,
        ]);

        $this->commands([
            ECimportCommand::class,
            FixSectionImageUrlsCommand::class,
            ImportPostFeaturedImagesCommand::class,
            ImportPostsToArticlesCommand::class,
            ImportWpFeaturedImagesCommand::class,
            ListPostImageUrlsCommand::class,
            PostPrimaryBannerCommand::class,
            SyncArticleVersionMetaImagesCommand::class,
        ]);

        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/Routes/article.php');

        Category::observe(CategoryObserver::class);
        Short::observe(ShortObserver::class);
        Article::observe(ArticleObserver::class);
        ArticleVersion::observe(ArticleVersionObserver::class);
    }
}
