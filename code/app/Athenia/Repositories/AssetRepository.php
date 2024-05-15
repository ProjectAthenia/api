<?php
declare(strict_types=1);

namespace App\Athenia\Repositories;

use App\Athenia\Contracts\Repositories\AssetRepositoryContract;
use App\Athenia\Models\BaseModelAbstract;
use App\Athenia\Traits\CanGetAndUnset;
use App\Models\Asset;
use App\Repositories\Traits\NotImplemented;
use DomainException;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Imagick;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class AssetRepository
 * @package App\Repositories
 */
class AssetRepository extends BaseRepositoryAbstract implements AssetRepositoryContract
{
    use \App\Athenia\Repositories\Traits\NotImplemented\FindOrFail, CanGetAndUnset;

    /**
     * @var Filesystem
     */
    protected $publicAssets;

    /**
     * @var string
     */
    private $assetBaseURL;

    /**
     * @var string
     */
    private $basePublicDirectory;

    /**
     * AssetRepository constructor.
     * @param Asset $model
     * @param LogContract $log
     * @param Factory $fileSystem
     * @param string $assetBaseURL
     * @param string $basePublicDirectory
     */
    public function __construct(Asset $model, LogContract $log, Factory $fileSystem,
                                string $assetBaseURL, string $basePublicDirectory)
    {
        parent::__construct($model, $log);
        $this->publicAssets = $fileSystem->disk('public');
        $this->assetBaseURL = $assetBaseURL;
        $this->basePublicDirectory = $basePublicDirectory;
    }

    /**
     * Overrides the parent create in order to process the file properly
     *
     * @param array $data
     * @param BaseModelAbstract|null $relatedModel
     * @param array $forcedValues
     * @return BaseModelAbstract
     * @throws \ImagickException
     */
    public function create(array $data = [], BaseModelAbstract $relatedModel = null, array $forcedValues = [])
    {
        if (isset($data['uploaded_file'])) {
            /** @var UploadedFile $file */
            $file = $data['uploaded_file'];
            $file->storePubliclyAs('public/' . $this->basePublicDirectory, $file->getClientOriginalName());
            $data['url'] = $this->assetBaseURL . '/' . $this->basePublicDirectory . '/' . $file->getClientOriginalName();

            unset ($data['uploaded_file']);
        } else {

            $fileContents = $this->getAndUnset($data, 'file_contents');
            $fileExtension = $this->getAndUnset($data, 'file_extension');

            if ($fileContents && $fileExtension) {
                $data['url'] = $this->assetBaseURL . '/' . $this->storeImage($fileContents, $fileExtension);
            }
        }

        return parent::create($data, $relatedModel, $forcedValues);
    }

    /**
     * Generates a public file name for an asset
     *
     * @param $fileExtension
     * @return string
     */
    protected function generatePublicFileName($fileExtension)
    {
        $attempts = 0;

        do {
            if ($attempts == 5) {
                throw new DomainException('Unable to generate a proper file name for the public file.');
            }
            $attempts++;

            $imageName = $this->basePublicDirectory . '/' .  Str::random(40) . '.' . $fileExtension;
        } while ($this->publicAssets->exists($imageName));

        return $imageName;
    }

    /**
     * Store an uploaded image and return the path
     *
     * @param $fileContents
     * @param $fileExtension
     * @return string
     * @throws \ImagickException
     */
    protected function storeImage($fileContents, $fileExtension)
    {
        $image = new Imagick();
        $image->readImageBlob($fileContents);
        $image->setImageFormat($fileExtension);

        $orientation = $image->getImageOrientation();

        switch($orientation) {
            case imagick::ORIENTATION_BOTTOMRIGHT:
                $image->rotateimage("#000", 180); // rotate 180 degrees
                break;

            case imagick::ORIENTATION_RIGHTTOP:
                $image->rotateimage("#000", 90); // rotate 90 degrees CW
                break;

            case imagick::ORIENTATION_LEFTBOTTOM:
                $image->rotateimage("#000", -90); // rotate 90 degrees CCW
                break;
        }

        $image->setImageOrientation(imagick::ORIENTATION_TOPLEFT);

        $image->commentImage('Uploaded to SGC');

        $attempts = 0;

        do {
            if ($attempts == 5) {
                throw new DomainException('Unable to generate a proper file name for the public file.');
            }
            $imageName = $this->generatePublicFileName($fileExtension);
            $attempts++;

        } while ($this->publicAssets->exists($imageName));

        $this->publicAssets->put($imageName, $image->__toString());

        return $imageName;
    }
}
