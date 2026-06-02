<?php

namespace App\Providers;

use App\Models\Periode;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        view()->composer('*', function ($view) {
            $periodes = Periode::orderBy('tahun')
                ->orderByRaw("FIELD(nama_periode, 'Q1', 'Q2', 'Q3', 'Q4')")
                ->get();

            $currentPeriode = null;
            $periodeId = session('current_periode_id');

            if ($periodeId) {
                $currentPeriode = Periode::find($periodeId);
            }

            if (!$currentPeriode && $periodes->isNotEmpty()) {
                // Prefer periode with status 'Aktif' if present, otherwise pick the first
                $currentPeriode = $periodes->firstWhere('status', 'Aktif') ?? $periodes->first();
                if ($currentPeriode) {
                    session(['current_periode_id' => $currentPeriode->id]);
                }
            }

            $view->with('periodes', $periodes)->with('currentPeriode', $currentPeriode);
        });
    }
}
