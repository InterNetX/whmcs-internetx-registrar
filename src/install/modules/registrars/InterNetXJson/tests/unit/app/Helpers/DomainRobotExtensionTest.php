<?php

declare(strict_types=1);

use App\Helpers\DomainRobotExtension;
use PHPUnit\Framework\TestCase;

final class DomainRobotExtensionTest extends TestCase
{
    public function testItExtensionCanBeSuccessfullyConverted(): void
    {
        $this->assertEquals(
            [ 'handle' => [
                'entityType'     => 't1',
                'number'         => 't2',
                'vatNumber'      => 't3',
                ]
            ],
            DomainRobotExtension::assign('it', [
                'IT Entity Type' => 't1',
                'ID Number'      => 't2',
                'vatnumber'      => 't3',
            ])
        );
    }

    public function testReturnsFalseForNonExistingExtensions(): void
    {
        $this->assertEquals(
            false,
            DomainRobotExtension::assign('thistlddoesnotexist', [])
        );
    }
}
