<?php

declare(strict_types=1);

namespace App\Controller\Pub\Secondary;

use App\Helper\DateHelper;
use App\Helper\ExceptionHelper;
use App\Helper\FileHelper;
use App\Helper\FilePath;
use App\Helper\IniHelper;
use App\Helper\RandomHelper;
use App\Service\Listing\Save\ListingFileUploadService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\PathUtil\Path;

/**
 * php.ini values have minimum value of:
 *
 * memory_limit=64M
 * upload_max_filesize=2M
 * post_max_size=25M
 * file_uploads=1
 * max_file_uploads=20
 */
class FileUploadController extends AbstractController
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var TranslatorInterface
     */
    private $trans;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ValidatorInterface $validator, TranslatorInterface $trans, LoggerInterface $logger)
    {
        $this->validator = $validator;
        $this->trans = $trans;
        $this->logger = $logger;
    }

    /**
     * @Route("/private/file-upload", name="app_file_upload", methods={"POST"}, options={"expose": true})
     */
    public function uploadFile(Request $request, Filesystem $filesystem): Response
    {
        IniHelper::setMemoryLimitIfLessThanMb(64);
        IniHelper::setSizeIfLessThanMb('upload_max_filesize', 2);
        IniHelper::setSizeIfLessThanMb('post_max_size', 25);
        $filesArrayKey = ListingFileUploadService::UPLOADED_FILES_FIELD_NAME;
        $tempUploadDir = FilePath::getTempFileUpload().'/'.DateHelper::date('Y/m/d').'/';
        $filesystem->mkdir($tempUploadDir, 0770);

        $response = [];
        $response['hasWarnings'] = false;
        $response['isSuccess'] = true;
        $response['warnings'] = [];
        $response['files'] = [];

        /** @var UploadedFile $uploadedFile */
        foreach ($request->files->get($filesArrayKey) as $uploadedFile) {
            try {
                $violations = $this->validator->validate($uploadedFile, [
                    new File([
                        'mimeTypes' => FileHelper::getValidMimeTypesList(),
                    ]),
                ]);
                if ($violations->count() > 0) {
                    $this->logger->error('uploaded file failed validation, violations: {violations}', [
                        'violations' => $violations,
                    ]);

                    $response['warnings'][] = $violations->get(0)->getMessage();
                    continue;
                }
                FileHelper::throwExceptionIfUnsafeFilename($uploadedFile->getClientOriginalName());
                $temporaryFile = $uploadedFile->move(
                    $tempUploadDir,
                    'zz_file_upload_'
                    .DateHelper::date('Ymd_His')
                    .'_'
                    .RandomHelper::string(32)
                    .'.'
                    .$uploadedFile->getClientOriginalExtension()
                );

                $response['files'][] = [
                    'date' => DateHelper::date('r'),
                    'extension' => $uploadedFile->getClientOriginalExtension(),
                    'file' => Path::makeRelative($temporaryFile->getRealPath() ?: '', FilePath::getPublicDir()),
                    'format' => \strtok($temporaryFile->getMimeType(), '/'),
                    'name' => $temporaryFile->getFilename(),
                    'old_name' => $uploadedFile->getClientOriginalName(),
                    'old_title' => \pathinfo($uploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                    'replaced' => null,
                    'size' => $temporaryFile->getSize(),
                    'size2' => $this->formatSize($temporaryFile->getSize()),
                    'title' => \pathinfo($uploadedFile->getClientOriginalName(), \PATHINFO_FILENAME),
                    'type' => $temporaryFile->getMimeType(),
                    'uploaded' => 1,
                    'image' => null,
                ];
            } catch (\Throwable $e) {
                $response['warnings'][] = $this->trans->trans('trans.Error, try to upload again');
                $this->logger->error('error while uploading files', ExceptionHelper::flatten($e));
            }
        }

        $response['hasWarnings'] = \count($response['warnings']) > 0;
        $response['isSuccess'] = 0 === \count($response['warnings']);

        return $this->json($response);
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return \number_format($bytes / 1073741824, 2).' GB';
        }
        if ($bytes >= 1048576) {
            return \number_format($bytes / 1048576, 2).' MB';
        }
        if ($bytes > 0) {
            return \number_format($bytes / 1024, 2).' KB';
        }

        return '0 bytes';
    }
}
