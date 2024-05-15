<?php
declare(strict_types=1);

namespace Tests\Athenia\Unit\Http\Core\Requests;

use App\Athenia\Http\Core\Requests\BaseAssetUploadRequestAbstract;
use RuntimeException;
use Tests\TestCase;

/**
 * Class BaseAssetUploadRequestAbstractTest
 * @package Tests\Athenia\Unit\Http\V1\Requests
 */
final class BaseAssetUploadRequestAbstractTest extends TestCase
{
    public function testValidationDataSetsMimeType(): void
    {
        /** @var BaseAssetUploadRequestAbstract $request */
        $request = $this->getMockForAbstractClass(BaseAssetUploadRequestAbstract::class);

        $request->replace([
            'file_contents' => base64_encode('test'),
        ]);

        $data = callMethod($request, 'validationData');

        $this->assertEquals($data['mime_type'], 'text/plain');
    }

    public function testGetDecodedContentsThrowsException(): void
    {
        $this->expectException(RuntimeException::class);

        /** @var BaseAssetUploadRequestAbstract $request */
        $request = $this->getMockForAbstractClass(BaseAssetUploadRequestAbstract::class);

        $request->getDecodedContents();
    }

    public function testGetDecodedContentsReturnsCorrectContents(): void
    {
        /** @var BaseAssetUploadRequestAbstract $request */
        $request = $this->getMockForAbstractClass(BaseAssetUploadRequestAbstract::class);

        $request->replace([
            'file_contents' => base64_encode('<svg></svg>'),
        ]);

        callMethod($request, 'validationData');

        $this->assertEquals('<svg></svg>', $request->getDecodedContents());
    }

    public function testGetFileMimeTypeThrowsException(): void
    {
        $this->expectException(RuntimeException::class);

        /** @var BaseAssetUploadRequestAbstract $request */
        $request = $this->getMockForAbstractClass(BaseAssetUploadRequestAbstract::class);

        $request->getFileMimeType();
    }

    public function testGetFileMimeTypeReturnsCorrectContents(): void
    {
        /** @var BaseAssetUploadRequestAbstract $request */
        $request = $this->getMockForAbstractClass(BaseAssetUploadRequestAbstract::class);

        $request->replace([
            'file_contents' => base64_encode('<svg></svg>'),
        ]);

        callMethod($request, 'validationData');

        $this->assertEquals('image/svg+xml', $request->getFileMimeType());
    }
}
