<?php

namespace App\Http\Controllers\Screening;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Screening;
use App\Models\ScreeningResult;
use Illuminate\View\View;

class StaticPageController extends Controller
{
    public function dashboard(): View
    {
        $riskCounts = ScreeningResult::query()
            ->selectRaw('risk_category, COUNT(*) as total')
            ->groupBy('risk_category')
            ->pluck('total', 'risk_category');

        $latestScreenings = Screening::query()
            ->with(['child', 'result'])
            ->latest('screening_date')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.index', [
            'totalChildren' => Child::count(),
            'totalScreenings' => Screening::count(),
            'riskCounts' => [
                'low' => $riskCounts->get('low', 0),
                'medium' => $riskCounts->get('medium', 0),
                'high' => $riskCounts->get('high', 0),
                'urgent' => $riskCounts->get('urgent', 0),
            ],
            'latestScreenings' => $latestScreenings,
        ]);
    }

    public function about(): View
    {
        return view('about.index');
    }
}
