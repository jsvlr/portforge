<div>
    <div class=" flex h-full flex-col items-center justify-center gap-y-8 px-8 py-12 text-center">
        <div class="w-100 space-y-2">
            <h1 class="text-2xl font-bold text-white">
                Welcome to {{ config('app.name') }}
            </h1>
            <p class="text-sm text-white/80">
                Your all-in-one solution for rapid application development
            </p>
        </div>

        <div class="w-full max-w-md space-y-3">
            @php
            $features = [
            [
            'icon' => 'heroicon-o-bolt',
            'title' => 'Lightning Fast',
            'description' => 'Build powerful admin panels in minutes',
            ],
            [
            'icon' => 'heroicon-o-squares-2x2',
            'title' => 'Flexible & Modular',
            'description' => 'Customize every aspect with ease',
            ],
            [
            'icon' => 'heroicon-o-chart-bar',
            'title' => 'Feature Rich',
            'description' => 'Advanced features built right in',
            ],
            [
            'icon' => 'heroicon-o-shield-check',
            'title' => 'Secure & Reliable',
            'description' => "Built on Laravel's solid foundation",
            ],
            ];
            @endphp

            @foreach ($features as $feature)
            <div class="flex items-start gap-x-3 rounded-lg bg-white/10 px-4 py-3 text-left">
                <x-filament::icon
                    :icon="$feature['icon']"
                    class="mt-0.5 h-5 w-5 shrink-0 text-white" />
                <div>
                    <p class="text-sm font-semibold text-white">
                        {{ $feature['title'] }}
                    </p>
                    <p class="text-xs text-white/70">
                        {{ $feature['description'] }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>

        <p class="text-xs text-white/60">
            Please log in to access your admin panel.
        </p>
    </div>
</div>