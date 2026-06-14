<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRuleRequest;
use App\Http\Requests\UpdateRuleRequest;
use App\Models\Rule;
use App\Services\AuditService;
use Illuminate\Http\Request;

class RuleController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
    ) {}

    public function index()
    {
        $rules = Rule::with('creator')->latest()->paginate(15);

        return view('rules.index', compact('rules'));
    }

    public function create()
    {
        return view('rules.create');
    }

    public function store(StoreRuleRequest $request)
    {
        $rule = Rule::create([
            'title'       => $request->title,
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active', true),
            'created_by'  => auth()->id(),
        ]);

        $this->auditService->log(
            'rule_created',
            'Rule',
            $rule->id,
            ['title' => $rule->title],
        );

        return redirect()->route('rules.index')
            ->with('success', 'Rule created successfully.');
    }

    public function edit(Rule $rule)
    {
        return view('rules.edit', compact('rule'));
    }

    public function update(UpdateRuleRequest $request, Rule $rule)
    {
        $rule->update($request->validated());

        $this->auditService->log(
            'rule_updated',
            'Rule',
            $rule->id,
            ['title' => $rule->title],
        );

        return redirect()->route('rules.index')
            ->with('success', 'Rule updated successfully.');
    }
}
