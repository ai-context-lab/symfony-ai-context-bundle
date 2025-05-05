<?php

namespace AiContextBundle\Tests;

use PHPUnit\Framework\TestCase;
use AiContextBundle\Generator\AbstractContextGenerator;

class AbstractContextGeneratorTest extends TestCase
{
    private function createDummyGenerator(): AbstractContextGenerator
    {
        return new class extends AbstractContextGenerator {
            public function generate(): array
            {
                return [];
            }

            public function findClasses(array $paths): array
            {
                return parent::findClasses($paths);
            }

            public function getClassFromFile(string $filePath): ?string
            {
                return parent::getClassFromFile($filePath);
            }
        };
    }

    public function testFindClassesReturnsValidClassNames(): void
    {
        $generator = $this->createDummyGenerator();

        /** @phpstan-ignore-next-line */
        $classes = $generator->findClasses([
            dirname(__DIR__) . '/src/Command'
        ]);

        $this->assertNotEmpty($classes);
        $this->assertContains('AiContextBundle\Command\GenerateAiContextCommand', $classes);
    }

    public function testGetClassFromInvalidFileReturnsNull(): void
    {
        $generator = $this->createDummyGenerator();

        $fakeFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($fakeFile, '<?php echo "no class here";');

        /** @phpstan-ignore-next-line */
        $class = $generator->getClassFromFile($fakeFile);

        $this->assertNull($class);

        unlink($fakeFile);
    }
}
