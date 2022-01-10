<?php
declare(strict_types=1);

namespace Tests\Unit\Services\Wiki;

use App\Services\Wiki\ArticleVersionCalculationService;
use Tests\TestCase;

/**
 * Class ArticleVersionCalculationServiceTest
 * @package Tests\Unit\Services
 */
class ArticleVersionCalculationServiceTest extends TestCase
{
    public function testCalculateTextDiffPercentage()
    {
        $service = new ArticleVersionCalculationService();

        $this->assertSame(0.33, (float) number_format($service->calculateTextDiffPercentage('hi', 'hi '), 2));
        $this->assertSame(1.20, (float) number_format($service->calculateTextDiffPercentage('hello steve', 'hello'), 2));
    }

    public function testDetermineIfMajorReturnsTrueWhenHeaderCompletelyChanged()
    {
        $service = new ArticleVersionCalculationService();

        $newContent = "# header\n\nSomething for a test\n\n## let's change this header\n\nHopefully it will work";
        $oldContent = "# header\n\nSomething for a test\n\n## Now it's different\n\nHopefully it will work";

        $this->assertTrue($service->determineIfMajor($newContent, $oldContent));
    }

    public function testDetermineIfMajorReturnsFalseWhenHeaderPunctuationChanged()
    {
        $service = new ArticleVersionCalculationService();

        $newContent = "# header\n\nSomething for a test\n\n## lets change this header\n\nHopefully it will work";
        $oldContent = "# header\n\nSomething for a test\n\n## let's change this header\n\nHopefully it will work";

        $this->assertFalse($service->determineIfMajor($newContent, $oldContent));
    }

    public function testDetermineIfMajorReturnsTrueWhenHeaderRemoved()
    {
        $service = new ArticleVersionCalculationService();

        $oldContent = "# header\n\nSomething for a test\n\n## let's remove this header\n\nHopefully it will work";
        $newContent = "# header\n\nSomething for a test\n\nHopefully it will work";

        $this->assertTrue($service->determineIfMajor($newContent, $oldContent));
    }

    public function testDetermineIfMajorReturnsTrueWhenContentChangesALot()
    {
        $service = new ArticleVersionCalculationService();

        $newContent = "# header\n\nSomething for a test\n\nHopefully it will work";
        $oldContent = "# header\n\nSomething for a test. I am so happy that this is working.\n\nHopefully it will work";

        $this->assertTrue($service->determineIfMajor($newContent, $oldContent));
    }

    public function testDetermineIfMajorReturnsFalseWhenContentChangesLittle()
    {
        $service = new ArticleVersionCalculationService();

        $newContent = "# header\n\nSomething for a test\n\nHopefully it will work";
        $oldContent = "# header\n\nSomething for a test.\n\nHopefully it will work";

        $this->assertFalse($service->determineIfMajor($newContent, $oldContent));
    }

    public function testDetermineIfMinorReturnsTrueWhenAParagraphWasAdded()
    {
        $service = new ArticleVersionCalculationService();

        $newContent = "# header\n\nSomething for a test\n\nHopefully it will work\n\nHere's a new paragraph";
        $oldContent = "# header\n\nSomething for a test\n\nHopefully it will work";

        $this->assertTrue($service->determineIfMinor($newContent, $oldContent));
    }

    public function testDetermineIfMinorReturnsFalseWhenALineBreakWasAdded()
    {
        $service = new ArticleVersionCalculationService();

        $newContent = "# header\n\nSomething for a test\n\nHopefully it will work\n\n";
        $oldContent = "# header\n\nSomething for a test\n\nHopefully it will work";

        $this->assertFalse($service->determineIfMinor($newContent, $oldContent));
    }

    public function testDetermineIfMinorReturnsTrueWhenANewSentenceWasAdded()
    {
        $service = new ArticleVersionCalculationService();

        $newContent = "# header\n\nSomething for a test\n\nHopefully it will work. Here's another sentence";
        $oldContent = "# header\n\nSomething for a test\n\nHopefully it will work";

        $this->assertTrue($service->determineIfMinor($newContent, $oldContent));
    }

    public function testDetermineIfMinorReturnsFalseWhenPunctuationWasChanged()
    {
        $service = new ArticleVersionCalculationService();

        $newContent = "# header\n\nSomething for a test.\n\nHopefully it will work.";
        $oldContent = "# header\n\nSomething for a test\n\nHopefully it will work";

        $this->assertFalse($service->determineIfMinor($newContent, $oldContent));
    }
}
