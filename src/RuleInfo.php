<?php

namespace JsonSchemaInfo;

/**
 * Provide info on a specific rule
 *
 * @package baacode/json-schema-info
 * @license ISC
 * @author Steve Gilberd <steve@erayd.net>
 * @copyright (c) 2017 Erayd LTD
 */
class RuleInfo
{
    /** @var SchemaInfo Spec info object */
    protected $spec = null;

    /** @var string Vocabulary */
    protected $vocabulary = null;

    /** @var string Keyword */
    protected $keyword = null;

    /**
     * Create a new instance
     *
     * @param SchemaInfo $spec
     * @param string $vocabulary
     * @param string $keyword
     */
    public function __construct($spec, $vocabulary, $keyword)
    {
        $this->spec = $spec;
        $this->vocabulary = $vocabulary;
        $this->keyword = $keyword;
    }

    /**
     * Get a rule value
     *
     * @param string $rule Rule name
     * @return boolean
     */
    public function __get($rule)
    {
        $value = $this->getTarget($this->spec->getSpecInfo(), array('metadata', $this->vocabulary, $this->keyword, $rule));
        if (!is_null($value)) {
            return $this->$rule = $value;
        }

        $value = $this->getTarget($this->spec->getBaseInfo(), array('metadata', $this->vocabulary, $this->keyword, $rule));
        if (!is_null($value)) {
            return $this->$rule = $value;
        }

        $value = $this->getTarget($this->spec->getRuleSchema(), array('definitions', 'metadata', 'properties', $rule, 'default'));
        if (!is_null($value)) {
            return $this->$rule = $value;
        }

        return $this->$rule = null;
    }

    /**
     * Get an object target
     *
     * @param \StdClass $object
     * @param array $path
     * @return mixed
     */
    final protected function getTarget($object, $path)
    {
        if (property_exists($object, reset($path))) {
            return count($path) > 1 ? $this->getTarget($object->{array_shift($path)}, $path) : $object->{current($path)};
        }
        return null;
    }
}
