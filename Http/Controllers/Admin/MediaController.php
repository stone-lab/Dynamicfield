<?php

namespace Modules\Dynamicfield\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Media\Image\Imagy;
use Modules\Media\Repositories\FileRepository;
use Modules\Media\Services\FileService;

class MediaController extends AdminBaseController
{
    /**
     * @var FileService
     */
    private $fileService;
    /**
     * @var FileRepository
     */
    private $file;
    /**
     * @var Imagy
     */
    private $imagy;

    public function __construct(FileService $fileService, FileRepository $file, Imagy $imagy)
    {
        $this->fileService = $fileService;
        $this->file = $file;
        $this->imagy = $imagy;
    }

    public function linkMedia(Request $request)
    {
        $mediaId = $request->get('mediaId');
        $file = $this->file->find($mediaId);
        $path = $file->path->getUrl();
        $thumbnailPath = $this->imagy->getThumbnail($file->path, 'mediumThumb');

        return Response::json([
            'error' => false,
            'message' => 'The link has been added.',
            'result' => [
                          'path' => $path,
                          'thumb' => $thumbnailPath,
            ],
        ]);
    }
}
