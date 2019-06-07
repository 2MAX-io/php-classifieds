<?php

declare(strict_types=1);

namespace App\Controller\Pub\Secondary;

use App\Helper\FilePath;
use App\Service\System\FileUpload\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileUploadController extends AbstractController
{
    /**
     * @Route("/private/file-upload", name="app_file_upload", options={"expose": true})
     */
    public function uploadFile(): Response
    {
        $tmpUploadDir = FilePath::getTempFileUpload() . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
        if (!\is_dir($tmpUploadDir) && !\mkdir($tmpUploadDir, 0770, true) && !\is_dir($tmpUploadDir)) {
        } // todo: use symfony file

        $FileUploader = new FileUploader(
            'files', [
            'uploadDir' => $tmpUploadDir,
            'extensions' => ['jpg', 'jpeg', 'png'],
        ]
        );

        $upload = $FileUploader->upload();

        if ($upload['isSuccess']) {
            // get the uploaded files
            $files = $upload['files'];
        } else {
            // get the warnings
            $warnings = $upload['warnings'];
        }

        return $this->json($upload);
    }
}
