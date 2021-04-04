<?php

declare(strict_types=1);

namespace App\Controller\Pub\Secondary;

use App\Enum\EnvironmentEnum;
use App\Helper\DateHelper;
use App\Helper\FileHelper;
use App\Helper\FilePath;
use App\Secondary\FileUpload\FileUploader;
use App\Service\Listing\Save\ListingFileUploadService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileUploadController extends AbstractController
{
    /**
     * @Route("/private/file-upload", name="app_file_upload", methods={"POST"}, options={"expose": true})
     */
    public function uploadFile(Request $request, Filesystem $filesystem): Response
    {
        $filesArrayKey = ListingFileUploadService::UPLOADED_FILES_FIELD_NAME;
        if (empty($_FILES) && $request->files->count()) {
            /** @var UploadedFile $file */
            foreach ($request->files->get($filesArrayKey) as $file) {
                $_FILES[$filesArrayKey]['name'][] = $file->getClientOriginalName();
                $_FILES[$filesArrayKey]['type'][] = $file->getMimeType();
                $_FILES[$filesArrayKey]['tmp_name'][] = $file->getPathname();
                $_FILES[$filesArrayKey]['error'][] = $file->getError();
                $_FILES[$filesArrayKey]['size'][] = $file->getSize();
            }
        }
        foreach ($_FILES[$filesArrayKey]['name'] as $fileName) {
            FileHelper::throwExceptionIfUnsafeFilename($fileName);
        }

        $tempUploadDir = FilePath::getTempFileUpload().'/'.DateHelper::date('Y/m/d').'/';
        $filesystem->mkdir($tempUploadDir, 0770);
        $uploaderOptions = [
            'uploadDir' => $tempUploadDir,
            'extensions' => ['jpg', 'jpeg', 'png'],
            'fileMaxSize' => 2,
            'title' => ['zz_file_upload_'.DateHelper::date('Ymd_His').'_{random}.{extension}', 32],
        ];
        if (EnvironmentEnum::TEST === ($_ENV['APP_ENV'] ?? '')) {
            $uploaderOptions['move_uploaded_file'] = static function ($tmp, $dest) {
                return \copy($tmp, $dest);
            };
        }
        $fileUploader = new FileUploader($filesArrayKey, $uploaderOptions);
        $response = $fileUploader->upload();

        return $this->json($response);
    }
}
