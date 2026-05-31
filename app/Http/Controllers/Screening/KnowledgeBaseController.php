<?php

namespace App\Http\Controllers\Screening;

use App\Http\Controllers\Controller;
use App\Models\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule as ValidationRule;
use Illuminate\View\View;

class KnowledgeBaseController extends Controller
{
    public function index(): View
    {
        $rules = Rule::query()
            ->orderByDesc('is_active')
            ->orderBy('code')
            ->paginate(10);

        return view('knowledge-base.rules.index', compact('rules'));
    }

    public function create(): View
    {
        return view('knowledge-base.rules.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $rule = Rule::create($this->validatedData($request));

        return redirect()
            ->route('knowledge-base.rules.show', $rule)
            ->with('success', 'Rule knowledge base berhasil ditambahkan.');
    }

    public function show(Rule $rule): View
    {
        return view('knowledge-base.rules.show', compact('rule'));
    }

    public function edit(Rule $rule): View
    {
        return view('knowledge-base.rules.edit', compact('rule'));
    }

    public function update(Request $request, Rule $rule): RedirectResponse
    {
        $rule->update($this->validatedData($request, $rule));

        return redirect()
            ->route('knowledge-base.rules.show', $rule)
            ->with('success', 'Rule knowledge base berhasil diperbarui.');
    }

    public function deactivate(Rule $rule): RedirectResponse
    {
        $rule->update(['is_active' => false]);

        return redirect()
            ->route('knowledge-base.rules')
            ->with('success', "Rule {$rule->code} berhasil dinonaktifkan.");
    }

    private function validatedData(Request $request, ?Rule $rule = null): array
    {
        return $request->validate([
            'code' => [
                'required',
                'string',
                'max:255',
                ValidationRule::unique('rules', 'code')->ignore($rule),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'condition_summary' => ['nullable', 'string'],
            'recommendation' => ['required', 'string'],
            'explanation' => ['required', 'string'],
            'severity' => ['required', 'in:info,low,medium,high,urgent'],
            'source_reference' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]) + [
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
