<?php

declare(strict_types=1);

namespace App\Controller\Pub\Secondary;

use App\Helper\FileHelper;
use App\Helper\FilePath;
use App\System\FileUpload\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileUploadController extends AbstractController
{
    /**
     * @Route("/private/file-upload", name="app_file_upload", options={"expose": true})
     */
    public function uploadFile(Filesystem $filesystem): Response
    {
        $fileKey = 'files';
        foreach ($_FILES[$fileKey]['name'] as $fileName) {
            FileHelper::throwExceptionIfUnsafeFilename($fileName);
        }

        $tmpUploadDir = FilePath::getTempFileUpload() . '/' . \date('Y') . '/' . \date('m') . '/' . \date('d') . '/';
        $filesystem->mkdir($tmpUploadDir, 0770);
        $fileUploader = new FileUploader($fileKey, [
            'uploadDir' => $tmpUploadDir,
            'extensions' => ['jpg', 'jpeg', 'png'],
            'fileMaxSize' => 2,
            'title' => ['zz_file_upload_' . \date('Ymd_His') . '_{random}.{extension}', 32]
        ]);
        $response = $fileUploader->upload();

        return $this->json($response);
    }
}
