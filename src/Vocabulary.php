<?php

namespace JsonSchemaInfo;

/**
 * Vocabulary wrapper to allow accessing as property
 *
 * @package baacode/json-schema-info
 * @license ISC
 * @author Steve Gilberd <steve@erayd.net>
 * @copyright (c) 2017 Erayd LTD
 */
class Vocabulary
{
    /** @var SchemaInfo Spec */
    private $spec;

    /** @var string Vocabulary */
    private $vocabulary;

    /**
     * Create a new instance
     *
     * @param SchemaInfo $spec
     * @param string $vocabulary
     */
    public function __construct($spec, $vocabulary)
    {
        $this->spec = $spec;
        $this->vocabulary = $vocabulary;
    }

    /**
     * Get a rule
     *
     * @param string $rule
     * @return RuleInfo
     */
    public function __get($rule)
    {
        if (in_array($rule, $this->spec->getSpecInfo()->vocabularies->{$this->vocabulary})) {
            return $this->$rule = new RuleInfo($this->spec, $this->vocabulary, $rule);
        } else {
            return $this->$rule = null;
        }
    }
}
