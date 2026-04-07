<?php

namespace App\Services\Parsing;

use App\Models\RawEmail;

class RawEmailClassifier
{
    public function __construct(private readonly ConfigurableRuleParser $configurableRuleParser)
    {
    }

    /**
     * @return array{result:array,traces:array<int,array<string,mixed>>}
     */
    public function classifyWithTrace(RawEmail $rawEmail): array
    {
        [$result, $traces] = $this->configurableRuleParser->parseWithTrace($rawEmail);

        if ($result !== null) {
            return ['result' => $result, 'traces' => $traces];
        }

        $fallback = [
            'status' => 'other_unmatched',
            'hostname' => null,
            'job_name' => null,
            'summary' => $rawEmail->subject ?: 'Unklassifizierte Nachricht',
            'vendor' => 'unknown',
            'classification' => 'other_unmatched',
        ];

        $traces[] = [
            'parser_rule_id' => null,
            'matched' => false,
            'reason' => 'no_rule_matched',
            'result_json' => $fallback,
        ];

        return ['result' => $fallback, 'traces' => $traces];
    }

    /**
     * @return array{status:string,hostname:?string,job_name:?string,summary:string,vendor:string,classification:string}
     */
    public function classify(RawEmail $rawEmail): array
    {
        return $this->classifyWithTrace($rawEmail)['result'];
    }
}
