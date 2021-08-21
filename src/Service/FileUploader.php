<?php

namespace App\Service;

use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    public function __construct(
        private SluggerInterface $slugger,
        private FilesystemOperator $articleFileSystem
    ) {
    }

    /**
     * @param File $file
     * @param string|null $oldFileName
     * @return string
     * @throws FilesystemException
     */
    public function uploadFile(File $file, ?string $oldFileName = null): string
    {
        $fileName = $this->slugger
            ->slug(
                pathinfo(
                    $file instanceof UploadedFile
                        ? $file->getClientOriginalName()
                        : $file->getFilename(),
                    PATHINFO_FILENAME
                )
            )
            ->append('-' . uniqid())
            ->append('.' . $file->guessExtension())
            ->toString();

        $stream = fopen($file->getPathname(), 'r');

        $this->articleFileSystem->writeStream($fileName, $stream);
        if (is_resource($stream)) {
            fclose($stream);
        }

        if ($oldFileName && $this->articleFileSystem->fileExists($oldFileName)) {
            $this->articleFileSystem->delete($oldFileName);
        }

        return $fileName;
    }
}
