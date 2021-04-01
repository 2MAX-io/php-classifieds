<?php

declare(strict_types=1);

namespace App\Controller\Pub\Secondary;

use App\Helper\DateHelper;
use App\Helper\FileHelper;
use App\Helper\FilePath;
use App\Secondary\FileUpload\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileUploadController extends AbstractController
{
    /**
     * @Route("/private/file-upload", name="app_file_upload", methods={"POST"}, options={"expose": true})
     */
    public function uploadFile(Filesystem $filesystem): Response
    {
        $filesArrayKey = 'files';
        foreach ($_FILES[$filesArrayKey]['name'] as $fileName) {
            FileHelper::throwExceptionIfUnsafeFilename($fileName);
        }

        $tempUploadDir = FilePath::getTempFileUpload().'/'.DateHelper::date('Y/m/d').'/';
        $filesystem->mkdir($tempUploadDir, 0770);
        $fileUploader = new FileUploader($filesArrayKey, [
            'uploadDir' => $tempUploadDir,
            'extensions' => ['jpg', 'jpeg', 'png'],
            'fileMaxSize' => 2,
            'title' => ['zz_file_upload_'.DateHelper::date('Ymd_His').'_{random}.{extension}', 32],
        ]);
        $response = $fileUploader->upload();

        return $this->json($response);
    }
}
