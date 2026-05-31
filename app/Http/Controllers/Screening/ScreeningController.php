<?php

namespace App\Http\Controllers\Screening;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Rule;
use App\Models\Screening;
use App\Services\NutritionExpertSystemService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ScreeningController extends Controller
{
    public function __construct(
        private readonly NutritionExpertSystemService $expertSystemService,
    ) {}

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'risk_category' => ['nullable', 'in:low,medium,high,urgent'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $screenings = Screening::query()
            ->with(['child', 'result'])
            ->when($validated['risk_category'] ?? null, function ($query, string $riskCategory) {
                $query->whereHas('result', fn ($resultQuery) => $resultQuery->where('risk_category', $riskCategory));
            })
            ->when($validated['search'] ?? null, function ($query, string $search) {
                $query->whereHas('child', fn ($childQuery) => $childQuery->where('name', 'like', "%{$search}%"));
            })
            ->latest('screening_date')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('screenings.index', [
            'screenings' => $screenings,
            'filters' => [
                'risk_category' => $validated['risk_category'] ?? '',
                'search' => $validated['search'] ?? '',
            ],
        ]);
    }

    public function create(Child $child): View
    {
        return view('screenings.create', compact('child'));
    }

    public function store(Request $request, Child $child): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'birth_date' => ['required', 'date'],
            'screening_date' => ['required', 'date'],
            'age_months' => ['required', 'integer', 'min:0'],
            'weight_kg' => ['required', 'numeric', 'min:0'],
            'height_cm' => ['required', 'numeric', 'min:0'],
            'muac_cm' => ['nullable', 'numeric', 'min:0'],
            'height_for_age_z_score' => ['nullable', 'numeric'],
            'weight_for_height_z_score' => ['nullable', 'numeric'],
            'birth_weight_gram' => ['nullable', 'integer', 'min:0'],
            'is_premature' => ['nullable', 'boolean'],
            'has_edema' => ['nullable', 'boolean'],
            'exclusive_breastfeeding' => ['nullable', 'boolean'],
            'complementary_feeding_started' => ['nullable', 'boolean'],
            'complementary_feeding_age_month' => ['nullable', 'integer', 'min:0'],
            'meal_frequency_per_day' => ['nullable', 'integer', 'min:0'],
            'dietary_diversity_score' => ['nullable', 'integer', 'min:0'],
            'animal_protein_frequency' => ['nullable', 'in:never,rare,sometimes,often'],
            'has_recurrent_diarrhea' => ['nullable', 'boolean'],
            'has_recurrent_infection' => ['nullable', 'boolean'],
            'immunization_complete' => ['nullable', 'boolean'],
            'food_insecurity' => ['nullable', 'boolean'],
            'safe_drinking_water' => ['nullable', 'boolean'],
            'proper_sanitation' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $screening = $child->screenings()->create([
            'screening_date' => $validated['screening_date'],
            'age_months' => $validated['age_months'],
            'weight_kg' => $validated['weight_kg'],
            'height_cm' => $validated['height_cm'],
            'muac_cm' => $validated['muac_cm'] ?? null,
            'height_for_age_z_score' => $validated['height_for_age_z_score'] ?? null,
            'weight_for_height_z_score' => $validated['weight_for_height_z_score'] ?? null,
            'birth_weight_gram' => $validated['birth_weight_gram'] ?? null,
            'is_premature' => $request->boolean('is_premature'),
            'has_edema' => $request->boolean('has_edema'),
            'exclusive_breastfeeding' => $validated['exclusive_breastfeeding'] ?? null,
            'complementary_feeding_started' => $validated['complementary_feeding_started'] ?? null,
            'complementary_feeding_age_month' => $validated['complementary_feeding_age_month'] ?? null,
            'meal_frequency_per_day' => $validated['meal_frequency_per_day'] ?? null,
            'dietary_diversity_score' => $validated['dietary_diversity_score'] ?? null,
            'animal_protein_frequency' => $validated['animal_protein_frequency'] ?? null,
            'has_recurrent_diarrhea' => $request->boolean('has_recurrent_diarrhea'),
            'has_recurrent_infection' => $request->boolean('has_recurrent_infection'),
            'immunization_complete' => $validated['immunization_complete'] ?? null,
            'food_insecurity' => $request->boolean('food_insecurity'),
            'safe_drinking_water' => $validated['safe_drinking_water'] ?? null,
            'proper_sanitation' => $validated['proper_sanitation'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $this->expertSystemService->saveResult($screening);

        return redirect()
            ->route('screenings.show', $screening)
            ->with('success', 'Data screening berhasil disimpan dan hasil rule-based reasoning sudah dibuat.');
    }

    public function show(Screening $screening): View
    {
        $screening->load(['child', 'result']);

        $triggeredRuleDetails = $this->triggeredRuleDetails($screening);

        return view('screenings.show', compact('screening', 'triggeredRuleDetails'));
    }

    public function exportPdf(Screening $screening): Response
    {
        $screening->load(['child', 'result']);

        $pdf = Pdf::loadView('screenings.pdf', [
            'screening' => $screening,
            'triggeredRuleDetails' => $this->triggeredRuleDetails($screening),
        ])->setPaper('a4');

        $fileName = 'hasil-screening-'.$screening->id.'-'.$screening->child->name;

        return $pdf->download(str($fileName)->slug('-').'.pdf');
    }

    private function triggeredRuleDetails(Screening $screening)
    {
        return collect($screening->result?->triggered_rules ?? [])
            ->map(function (string $code) {
                $rule = Rule::query()->where('code', $code)->first();

                if ($rule) {
                    return [
                        'code' => $rule->code,
                        'name' => $rule->name,
                        'severity' => $rule->severity,
                        'explanation' => $rule->explanation,
                        'recommendation' => $rule->recommendation,
                    ];
                }

                return $this->fallbackRuleDetail($code);
            })
            ->filter()
            ->values();
    }

    private function fallbackRuleDetail(string $code): ?array
    {
        return match ($code) {
            'MPASI_DELAYED' => [
                'code' => $code,
                'name' => 'MPASI terlambat',
                'severity' => 'medium',
                'explanation' => 'Anak berusia 7 bulan atau lebih tetapi belum mulai MPASI, sehingga kebutuhan gizi dari makanan pendamping berisiko belum terpenuhi.',
                'recommendation' => 'Diskusikan pemberian MPASI sesuai usia dengan kader posyandu, ahli gizi, atau tenaga kesehatan.',
            ],
            'DIETARY_DIVERSITY_LOW' => [
                'code' => $code,
                'name' => 'Keragaman pangan rendah',
                'severity' => 'medium',
                'explanation' => 'Skor keragaman pangan kurang dari 5 menunjukkan variasi kelompok makanan anak masih terbatas.',
                'recommendation' => 'Variasikan makanan anak dengan sumber karbohidrat, protein, sayur, buah, dan pangan kaya mikronutrien sesuai usia.',
            ],
            default => null,
        };
    }
}
