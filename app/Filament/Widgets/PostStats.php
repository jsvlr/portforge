<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Post;
use App\Enums\PostStatusEnum;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Icons\Heroicon;

class PostStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalPosts = Post::count();
        $published = Post::where('status', PostStatusEnum::Published)->count();
        $drafts = Post::where('status', PostStatusEnum::Draft)->count();
        $views = Post::sum('views');

        return [
            Stat::make('Total Posts', number_format($totalPosts))
                ->description('All blog posts')
                ->descriptionIcon(Heroicon::Bookmark)
                ->icon(Heroicon::DocumentText)
                ->color('primary')
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:scale-[1.02] transition-all duration-300',
                ])
                ->chart(
                    collect(range(0, 6))->map(function ($i) {
                        $date = now()->subDays(6 - $i)->toDateString();

                        return Post::whereDate('created_at', $date)->count();
                    })
                ),

            Stat::make('Published', number_format($published))
                ->description(
                    $published > 0
                        ? round($published / max($totalPosts, 1)) * 100 . ' % of all posts'
                        : 'No published post'
                )
                ->descriptionIcon(Heroicon::ArrowTrendingUp)
                ->icon(Heroicon::RocketLaunch)
                ->color($published > 0 ? 'success' : 'danger')
                ->chart(
                    collect(range(0, 6))->map(function ($i) {
                        $date = now()->subDays(6 - $i)->toDateString();

                        return Post::whereDate('created_at', $date)
                            ->where('status', PostStatusEnum::Published)
                            ->count();
                    })
                ),

            Stat::make('Drafts', number_format($drafts))
                ->description('Pending publication')
                ->descriptionIcon(Heroicon::Clock)
                ->icon(Heroicon::PencilSquare)
                ->color('warning')
                ->chart(
                    collect(range(0, 6))->map(function ($i) {
                        $date = now()->subDays(6 - $i)->toDateString();

                        return Post::whereDate('created_at', $date)
                            ->where('status', PostStatusEnum::Draft)
                            ->count();
                    })
                ),

            Stat::make('Total Views', number_format($views))
                ->description('Across all published posts')
                ->descriptionIcon(Heroicon::Eye)
                ->icon(Heroicon::Eye)
                ->color('info')
                ->chart(
                    collect(range(0, 6))->map(function ($i) {
                        $date = now()->subDays(6 - $i)->toDateString();

                        return Post::whereDate('created_at', $date)
                            ->sum('views');
                    })
                ),
        ];
    }
}
