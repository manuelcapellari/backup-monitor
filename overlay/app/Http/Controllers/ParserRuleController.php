<?php

namespace App\Http\Controllers;

use App\Models\ParserRule;
use App\Services\Parsing\ConfigurableRuleParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ParserRuleController extends Controller
{
    public function index(): View
    {
        return view('parser-rules.index', [
            'rules' => ParserRule::query()->orderBy('priority')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('parser-rules.form', [
            'rule' => new ParserRule([
                'vendor' => 'custom',
                'match_field' => 'subject',
                'status' => 'info',
                'priority' => 100,
                'is_active' => true,
            ]),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        ParserRule::query()->create($data);

        return redirect()->route('parser-rules.index')->with('status', 'Parser-Regel angelegt.');
    }

    public function edit(ParserRule $parserRule): View
    {
        return view('parser-rules.form', [
            'rule' => $parserRule,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, ParserRule $parserRule): RedirectResponse
    {
        $data = $this->validateData($request);
        $parserRule->update($data);

        return redirect()->route('parser-rules.index')->with('status', 'Parser-Regel aktualisiert.');
    }

    public function destroy(ParserRule $parserRule): RedirectResponse
    {
        $parserRule->delete();

        return redirect()->route('parser-rules.index')->with('status', 'Parser-Regel gelöscht.');
    }

    public function duplicate(ParserRule $parserRule): RedirectResponse
    {
        $copy = $parserRule->replicate();
        $copy->name = $parserRule->name.' (Kopie)';
        $copy->priority = $parserRule->priority + 10;
        $copy->is_active = false;
        $copy->save();

        return redirect()->route('parser-rules.edit', $copy)->with('status', 'Regel dupliziert.');
    }

    public function toggle(ParserRule $parserRule): RedirectResponse
    {
        $parserRule->update([
            'is_active' => ! $parserRule->is_active,
        ]);

        return redirect()->route('parser-rules.index')->with('status', 'Regelstatus geändert.');
    }

    public function moveUp(ParserRule $parserRule): RedirectResponse
    {
        $parserRule->update([
            'priority' => max(1, $parserRule->priority - 10),
        ]);

        return redirect()->route('parser-rules.index')->with('status', 'Regelpriorität erhöht (weiter nach oben).');
    }

    public function moveDown(ParserRule $parserRule): RedirectResponse
    {
        $parserRule->update([
            'priority' => $parserRule->priority + 10,
        ]);

        return redirect()->route('parser-rules.index')->with('status', 'Regelpriorität gesenkt (weiter nach unten).');
    }

    public function testForm(?ParserRule $parserRule = null): View
    {
        return view('parser-rules.test', [
            'rules' => ParserRule::query()->orderBy('priority')->orderBy('name')->get(),
            'selectedRule' => $parserRule,
            'testResult' => null,
            'allRuleResults' => null,
            'input' => [
                'mode' => 'single',
                'from' => '',
                'subject' => '',
                'body' => '',
            ],
        ]);
    }

    public function testRun(Request $request, ConfigurableRuleParser $parser): View
    {
        $data = $request->validate([
            'mode' => ['required', Rule::in(['single', 'all'])],
            'parser_rule_id' => ['nullable', Rule::exists('parser_rules', 'id')],
            'from' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
        ]);

        $rule = null;
        $testResult = null;
        $allRuleResults = null;

        if ($data['mode'] === 'single') {
            if (empty($data['parser_rule_id'])) {
                return redirect()->back()->withErrors(['parser_rule_id' => 'Bitte eine Regel auswählen.'])->withInput();
            }

            $rule = ParserRule::query()->findOrFail((int) $data['parser_rule_id']);
            $testResult = $parser->testRule($rule, $data['from'] ?? null, $data['subject'] ?? null, $data['body'] ?? null);
        } else {
            $allRuleResults = $parser->testAllRules($data['from'] ?? null, $data['subject'] ?? null, $data['body'] ?? null);
        }

        return view('parser-rules.test', [
            'rules' => ParserRule::query()->orderBy('priority')->orderBy('name')->get(),
            'selectedRule' => $rule,
            'testResult' => $testResult,
            'allRuleResults' => $allRuleResults,
            'input' => [
                'mode' => $data['mode'],
                'from' => $data['from'] ?? '',
                'subject' => $data['subject'] ?? '',
                'body' => $data['body'] ?? '',
            ],
        ]);
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'vendor' => ['required', 'string', 'max:80'],
            'match_field' => ['required', Rule::in(['from', 'subject', 'body'])],
            'match_pattern' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['success', 'warning', 'error', 'info', 'other_unmatched'])],
            'priority' => ['required', 'integer', 'min:1', 'max:10000'],
            'hostname_regex' => ['nullable', 'string', 'max:255'],
            'job_name_regex' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        return $data;
    }
}
