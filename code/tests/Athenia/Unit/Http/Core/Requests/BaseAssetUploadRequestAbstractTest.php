<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Http\Core\Requests;

use App\Athenia\Http\Core\Requests\BaseAssetUploadRequestAbstract;
use RuntimeException;
use Tests\Mocks\AssetUploadRequest;
use Tests\TestCase;

/**
 * Class BaseAssetUploadRequestAbstractTest
 * @package Tests\Athenia\Unit\Http\V1\Requests
 */
final class BaseAssetUploadRequestAbstractTest extends TestCase
{
    public function testValidationDataSetsMimeType(): void
    {
        $request = new AssetUploadRequest();

        $request->replace([
            'file_contents' => base64_encode('test'),
        ]);

        $data = callMethod($request, 'validationData');

        $this->assertEquals($data['mime_type'], 'text/plain');
    }

    public function testGetDecodedContentsThrowsException(): void
    {
        $this->expectException(RuntimeException::class);

        $request = new AssetUploadRequest();

        $request->getDecodedContents();
    }

    public function testGetDecodedContentsReturnsCorrectContents(): void
    {
        $request = new AssetUploadRequest();

        $request->replace([
            'file_contents' => base64_encode('<svg></svg>'),
        ]);

        callMethod($request, 'validationData');

        $this->assertEquals('<svg></svg>', $request->getDecodedContents());
    }

    public function testGetFileMimeTypeThrowsException(): void
    {
        $this->expectException(RuntimeException::class);

        $request = new AssetUploadRequest();

        $request->getFileMimeType();
    }

    public function testGetFileMimeTypeReturnsCorrectContents(): void
    {
        $request = new AssetUploadRequest();

        $request->replace([
            'file_contents' => base64_encode('<svg></svg>'),
        ]);

        callMethod($request, 'validationData');

        $this->assertEquals('image/svg+xml', $request->getFileMimeType());
    }
}
