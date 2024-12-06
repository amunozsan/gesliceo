<?php

namespace App\Providers\Filament;

use App\Filament\Pages\ApiTokens;
use App\Filament\Pages\CreateTeam;
use App\Filament\Pages\EditProfile;
use App\Filament\Pages\EditTeam;
use App\Listeners\SwitchTeam;
use App\Models\Team;
use Filament\Events\TenantSet;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;

class AthletePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('athlete')
            ->path('athlete')
            ->login()
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            ->registration()
            ->passwordReset()
            ->emailVerification()
            ->viteTheme('resources/css/app.css')
            ->colors([
                'primary' => Color::Gray,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Profile')
                    ->icon('heroicon-o-user-circle')
                    ->url(fn () => $this->shouldRegisterMenuItem()
                        ? url(EditProfile::getUrl())
                        : url($panel->getPath())),
            ])
            ->discoverResources(in: app_path('Filament/Athlete/Resources'), for: 'App\\Filament\\Athlete\\Resources')
            ->discoverPages(in: app_path('Filament/Athlete/Pages'), for: 'App\\Filament\\Athlete\\Pages')
            ->pages([
                Pages\Dashboard::class,
                EditProfile::class,
                ApiTokens::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Athlete/Widgets'), for: 'App\\Filament\\Athlete\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);

            if (Features::hasApiFeatures()) {
                $panel->userMenuItems([
                    MenuItem::make()
                        ->label('API Tokens')
                        ->icon('heroicon-o-key')
                        ->url(fn () => $this->shouldRegisterMenuItem()
                            ? url(ApiTokens::getUrl())
                            : url($panel->getPath())),
                ]);
            }
    
            if (Features::hasTeamFeatures()) {
                $panel
                    ->tenant(Team::class)
                    ->tenantRegistration(CreateTeam::class)
                    ->tenantProfile(EditTeam::class)
                    ->userMenuItems([
                        MenuItem::make()
                            ->label(fn () => __('Team Settings'))
                            ->icon('heroicon-o-cog-6-tooth')
                            ->url(fn () => $this->shouldRegisterMenuItem()
                                ? url(EditTeam::getUrl())
                                : url($panel->getPath())),
                    ]);
            }
    
            return $panel;
        }
    
        public function boot()
        {
            /**
             * Disable Fortify routes
             */
            Fortify::$registersRoutes = false;
    
            /**
             * Disable Jetstream routes
             */
            Jetstream::$registersRoutes = false;
    
            /**
             * Listen and switch team if tenant was changed
             */
            //Event::listen(
              //  TenantSet::class,
                //SwitchTeam::class,
            //);
        }
    
        public function shouldRegisterMenuItem(): bool
        {
            $hasVerifiedEmail = auth()->user()?->hasVerifiedEmail();
    
            return Filament::hasTenancy()
                ? $hasVerifiedEmail && Filament::getTenant()
                : $hasVerifiedEmail;
    }
}