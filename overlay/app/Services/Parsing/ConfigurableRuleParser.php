<?php

namespace App\Services\Parsing;

use App\Models\ParserRule;
use App\Models\RawEmail;

class ConfigurableRuleParser
{
    /**
     * @return array{0:?array,1:array<int,array<string,mixed>>}
     */
    public function parseWithTrace(RawEmail $rawEmail): array
    {
        $rules = ParserRule::query()->where('is_active', true)->orderBy('priority')->orderBy('id')->get();
        $traces = [];

        foreach ($rules as $rule) {
            $result = $this->applyRule($rule, $rawEmail->from_address, $rawEmail->subject, $rawEmail->body_text);

            if ($result !== null) {
                $traces[] = [
                    'parser_rule_id' => $rule->id,
                    'matched' => true,
                    'reason' => 'matched',
                    'result_json' => $result,
                ];

                return [$result, $traces];
            }

            $traces[] = [
                'parser_rule_id' => $rule->id,
                'matched' => false,
                'reason' => 'pattern_not_matched',
                'result_json' => null,
            ];
        }

        return [null, $traces];
    }

    /**
     * @return array{status:string,hostname:?string,job_name:?string,summary:string,vendor:string,classification:string}|null
     */
    public function parse(RawEmail $rawEmail): ?array
    {
        return $this->parseWithTrace($rawEmail)[0];
    }

    /**
     * @return array{matched:bool,result:?array}
     */
    public function testRule(ParserRule $rule, ?string $from, ?string $subject, ?string $body): array
    {
        $result = $this->applyRule($rule, $from, $subject, $body);

        return [
            'matched' => $result !== null,
            'result' => $result,
        ];
    }

    /**
     * @return array<int,array{rule_id:int,rule_name:string,matched:bool,result:?array}>
     */
    public function testAllRules(?string $from, ?string $subject, ?string $body): array
    {
        $results = [];

        $rules = ParserRule::query()->where('is_active', true)->orderBy('priority')->orderBy('id')->get();
        foreach ($rules as $rule) {
            $result = $this->applyRule($rule, $from, $subject, $body);
            $results[] = [
                'rule_id' => $rule->id,
                'rule_name' => $rule->name,
                'matched' => $result !== null,
                'result' => $result,
            ];
        }

        return $results;
    }

    /**
     * @return array{status:string,hostname:?string,job_name:?string,summary:string,vendor:string,classification:string}|null
     */
    private function applyRule(ParserRule $rule, ?string $from, ?string $subject, ?string $body): ?array
    {
        $fieldValue = $this->valueForField($from, $subject, $body, $rule->match_field);

        if ($fieldValue === null || stripos($fieldValue, $rule->match_pattern) === false) {
            return null;
        }

        $sourceText = $body ?: $subject;

        return [
            'status' => $rule->status,
            'hostname' => $this->extractByRegex($rule->hostname_regex, $sourceText),
            'job_name' => $this->extractByRegex($rule->job_name_regex, $sourceText),
            'summary' => $subject ?: 'Regelbasierte Zuordnung',
            'vendor' => $rule->vendor,
            'classification' => $rule->status === 'other_unmatched' ? 'other_unmatched' : 'backup_event',
        ];
    }

    private function valueForField(?string $from, ?string $subject, ?string $body, string $field): ?string
    {
        return match ($field) {
            'from' => $from,
            'subject' => $subject,
            default => $body,
        };
    }

    private function extractByRegex(?string $regex, ?string $haystack): ?string
    {
        if (! $regex || ! $haystack) {
            return null;
        }

        if (@preg_match('/'.$regex.'/i', $haystack, $matches) === 1) {
            return trim($matches[1] ?? $matches[0]);
        }

        return null;
    }
}
