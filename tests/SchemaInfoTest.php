<?php

namespace JsonSchemaInfo\Tests;

use JsonSchemaInfo\SchemaInfo;
use JsonSchemaInfo\RuleInfo;
use JsonSchema\Validator;
use JsonSchema\Constraints\Constraint;

class SchemaInfoTest extends \PHPUnit\Framework\TestCase
{
    public function dataValidateStandard()
    {
        return array(
            array('http://json-schema.org/draft-03/schema#'),
        );
    }

    public function dataSpecList()
    {
        return array(
            array(SchemaInfo::SPEC_DRAFT_03, 'http://json-schema.org/draft-03/schema#'),
            array(SchemaInfo::SPEC_DRAFT_04, 'http://json-schema.org/draft-04/schema#'),
            array(SchemaInfo::SPEC_DRAFT_06, 'http://json-schema.org/draft-06/schema#'),
        );
    }

    /** @dataProvider dataValidateStandard */
    // ensure the standard rulesets are valid
    public function testValidateStandard($uri)
    {
        $info = new SchemaInfo($uri);

        $schema = json_decode(file_get_contents(__DIR__ . '/../rules/schema.json'));

        $v = new Validator();

        // check spec rules
        $ruleset = $info->getSpecInfo();
        $v->validate($ruleset, $schema, Constraint::CHECK_MODE_EXCEPTIONS);

        // check base rules
        $ruleset = $info->getBaseInfo();
        $v->validate($ruleset, $schema, Constraint::CHECK_MODE_EXCEPTIONS);
    }

    /** @dataProvider dataSpecList */
    // ensure that getSchema() works
    public function testGetSchema($spec)
    {
        $info = new SchemaInfo($spec);
        $infoSpecSchema = $info->getSchema();
        $specSchema = json_decode(file_get_contents(__DIR__ . "/../dist/$spec/schema.json"));

        $this->assertEquals($specSchema, $infoSpecSchema);
    }

    /** @dataProvider dataSpecList */
    // ensure that getURI() works
    public function testGetURI($spec, $uri)
    {
        $info = new SchemaInfo($spec);

        $this->assertEquals($uri, $info->getURI());
    }

    /** @dataProvider dataSpecList */
    public function testGetSpecName($spec, $uri)
    {
        $this->assertEquals($spec, SchemaInfo::getSpecName($uri));

        $this->setExpectedException('\InvalidArgumentException');
        SchemaInfo::getSpecName(array());
    }

    // test type exception for spec arg
    public function testSpecArgTypeException()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $info = new SchemaInfo(array());
    }

    // test invalid spec exception for spec arg
    public function testSpecArgInvalidSpecException()
    {
        $this->setExpectedException('\InvalidArgumentException');
        $info = new SchemaInfo('invalid-spec');
    }

    // test rule info
    public function testRuleInfo()
    {
        $info = new SchemaInfo(SchemaInfo::SPEC_DRAFT_03);

        // invalid rule vocabulary & rule references should be null
        $this->assertNull($info->invalidVocabulary);
        $this->assertNull($info->core->invalidKeyword);
        $this->assertNull($info->core->{'$id'});
        $this->assertNull($info->core->id->invalidRule);
            
        // valid rule references should be a RuleInfo object
        $this->assertInstanceOf('\JsonSchemaInfo\RuleInfo', $info->core->id);

        // all rules should provide info rules, even if not defined
        $this->assertFalse($info->core->id->{'as-schema'});

        // check info rules are valid
        $this->assertFalse($info->validation->properties->{'as-schema'});
        $this->assertTrue($info->validation->properties->{'as-container'});
        $this->assertEquals(array('boolean'), $info->validation->exclusiveMinimum->{'allow-types'});
    }
}
