<?php

namespace App\Providers\Filament;

use Exception;
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
        try {
            $brandLogo = Storage::url(Setting::get('general.brand_logo'));
            $favicon = Storage::url(Setting::get('general.favicon'));
            $brandName = Setting::get('general.brand_name');

            $panel
                ->brandLogo($brandLogo)
                ->favicon($favicon)
                ->brandName($brandName);
        } catch (Exception $e) {
            // Settings table may not exist yet (e.g. before first migration/seed)
            \Illuminate\Support\Facades\Log::warning('Could not apply general panel settings: ' . $e->getMessage());
        }

        return $panel;
    }
}
