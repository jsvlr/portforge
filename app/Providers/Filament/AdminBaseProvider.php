<?php

namespace App\Providers\Filament;

use Filament\PanelProvider;
use Filament\Panel;
use Illuminate\Support\Facades\Storage;
use Outerweb\Settings\Facades\Setting;

abstract class AdminBaseProvider extends PanelProvider
{

    protected function applySettings(Panel $panel): Panel
    {
        $this->applyGeneralSettings($panel);
        return $panel;
    }

    protected function applyGeneralSettings(Panel $panel): Panel
    {
        $brandLogo = Storage::url(Setting::get('general.brand_logo'));
        $favicon = Storage::url(Setting::get('general.favicon'));
        $brandName = Setting::get('general.brand_name');

        return $panel
            ->brandLogo($brandLogo)
            ->favicon($favicon)
            ->brandName($brandName);
    }
}
