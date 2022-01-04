<?php
declare(strict_types=1);

namespace Tests\Unit\Http\Core\Requests;

use App\Http\Core\Requests\BaseAssetUploadRequestAbstract;
use RuntimeException;
use Tests\TestCase;

/**
 * Class BaseAssetUploadRequestAbstractTest
 * @package Tests\Unit\Http\V1\Requests
 */
class BaseAssetUploadRequestAbstractTest extends TestCase
{
    public function testValidationDataSetsMimeType()
    {
        /** @var BaseAssetUploadRequestAbstract $request */
        $request = $this->getMockForAbstractClass(BaseAssetUploadRequestAbstract::class);

        $request->replace([
            'file_contents' => base64_encode('test'),
        ]);

        $data = callMethod($request, 'validationData');

        $this->assertEquals($data['mime_type'], 'text/plain');
    }

    public function testGetDecodedContentsThrowsException()
    {
        $this->expectException(RuntimeException::class);

        /** @var BaseAssetUploadRequestAbstract $request */
        $request = $this->getMockForAbstractClass(BaseAssetUploadRequestAbstract::class);

        $request->getDecodedContents();
    }

    public function testGetDecodedContentsReturnsCorrectContents()
    {
        /** @var BaseAssetUploadRequestAbstract $request */
        $request = $this->getMockForAbstractClass(BaseAssetUploadRequestAbstract::class);

        $request->replace([
            'file_contents' => base64_encode('<svg></svg>'),
        ]);

        callMethod($request, 'validationData');

        $this->assertEquals('<svg></svg>', $request->getDecodedContents());
    }

    public function testGetFileMimeTypeThrowsException()
    {
        $this->expectException(RuntimeException::class);

        /** @var BaseAssetUploadRequestAbstract $request */
        $request = $this->getMockForAbstractClass(BaseAssetUploadRequestAbstract::class);

        $request->getFileMimeType();
    }

    public function testGetFileMimeTypeReturnsCorrectContents()
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
