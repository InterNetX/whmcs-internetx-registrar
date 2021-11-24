<?php

declare(strict_types=1);

use App\Helpers\WhmcsBridge;
use PHPUnit\Framework\TestCase;

final class ZoneTemplateTest extends TestCase
{


    public function testReplaceInTemplateReplacesFullMatchesWithSimpleString(): void
    {
        $template = [
           "sample" => "{{domain}}"
        ];
        $result = WhmcsBridge::replaceInTemplate($template, [
            '{{domain}}' => 'example.com'
        ]);
        $this->assertEquals('example.com', $result['sample']);
    }

    public function testReplaceInTemplateReplacesFullMatchesWithObject(): void
    {
        $obj = new stdClass;
        $obj->test = 'testcontent';
        $template = [
           "sample" => '{{object}}'
        ];

        $result = WhmcsBridge::replaceInTemplate($template, [
            '{{object}}' => $obj
        ]);
        $this->assertIsObject($result['sample']);
    }

    public function testReplaceInTemplateReplacesRecursively(): void
    {
        $obj = new stdClass;
        $obj->test = 'testcontent';
        $template = [
           "sample" => '{{object}}',
           "subarray" => [
               'test' => '{{object}}'
           ]
        ];

        $result = WhmcsBridge::replaceInTemplate($template, [
            '{{object}}' => $obj
        ]);
        $this->assertIsObject($result['sample']);
        $this->assertIsObject($result['subarray']['test']);
    }

    public function testReplaceInTemplateReplacesPartialMatches(): void
    {
        $obj = new stdClass;
        $obj->test = 'testcontent';
        $template = [
           "sample" => '{{r1}}-{{r2}}',
        ];

        $result = WhmcsBridge::replaceInTemplate($template, [
            '{{r1}}' => 'A',
            '{{r2}}' => 'B',
        ]);
        $this->assertEquals('A-B', $result['sample']);

    }

}