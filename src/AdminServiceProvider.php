<?php

declare(strict_types=1);

namespace Admin;

use Admin\Models\Article;
use Admin\Models\ArticleVersion;
use Admin\Models\Category;
use Admin\Models\Short;
use Admin\Observers\ArticleObserver;
use Admin\Observers\ArticleVersionObserver;
use Admin\Observers\CategoryObserver;
use Admin\Observers\ShortObserver;
use Illuminate\Support\ServiceProvider;

final class AdminServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');
        $this->loadRoutesFrom(__DIR__ . '/Routes/article.php');

        Category::observe(CategoryObserver::class);
        Short::observe(ShortObserver::class);
        Article::observe(ArticleObserver::class);
        ArticleVersion::observe(ArticleVersionObserver::class);
    }
}
