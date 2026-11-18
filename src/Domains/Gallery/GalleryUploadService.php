<?php

declare(strict_types=1);

namespace EcApi\Domains\Gallery;

use EcApi\Models\Gallery;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class GalleryUploadService
{
    protected ImageManager $imageManager;

    public function __construct(?ImageManager $imageManager = null)
    {
        $this->imageManager = $imageManager ?? new ImageManager(new Driver());
    }

    /**
     * Upload a standard responsive image.
     *
     * Saves the original file preserving original format/extension and generates
     * responsive variants (sm, md, lg, xl) when the source image is wide enough.
     */
    public function uploadStandard(UploadedFile $file, ?int $createdById = null): Gallery
    {
        $disk = $this->disk();
        $directory = now()->format('Y/m/d');
        $filename = $this->generateUniqueFilename($file, $directory, $disk);
        $filepath = "{$directory}/{$filename}";

        // Store original file as-is without format conversion
        $disk->putFileAs($directory, $file, $filename);

        $imagePath = $file->getRealPath() ?: $file->getPathname();
        $image = $this->imageManager->decodePath($imagePath);

        $width = (int) $image->width();
        $height = (int) $image->height();
        $mimeType = (string) $file->getMimeType();
        $fileSize = (int) $file->getSize();

        // Generate responsive variants without upscaling
        $this->generateVariants($image, $directory, $filename, $disk);

        return Gallery::create([
            'filename'      => $filename,
            'filepath'      => $filepath,
            'mime_type'     => $mimeType,
            'file_size'     => $fileSize,
            'width'         => $width,
            'height'        => $height,
            'created_by_id' => $createdById,
        ]);
    }

    /**
     * Import a standard responsive image while preserving a known relative path.
     */
    public function importStandardAtPath(string $sourcePath, string $filepath, ?int $createdById = null): Gallery
    {
        $disk = $this->disk();
        $filepath = trim(str_replace('\\', '/', $filepath), '/');
        $directory = dirname($filepath);
        $filename = basename($filepath);

        if ($directory !== '.' && ! $disk->exists($directory)) {
            $disk->makeDirectory($directory);
        }

        if (! $disk->exists($filepath)) {
            File::copy($sourcePath, $disk->path($filepath));
        }

        $absolutePath = $disk->path($filepath);
        $image = $this->imageManager->decodePath($absolutePath);
        $width = (int) $image->width();
        $height = (int) $image->height();

        $this->generateMissingVariants($image, $directory, $filename, $disk);

        $gallery = Gallery::query()->firstOrNew(['filepath' => $filepath]);
        $gallery->fill([
            'filename' => $filename,
            'mime_type' => @mime_content_type($absolutePath) ?: null,
            'file_size' => File::size($absolutePath),
            'width' => $width,
            'height' => $height,
        ]);

        if (! $gallery->exists) {
            $gallery->created_by_id = $createdById;
        }

        $gallery->save();

        return $gallery;
    }

    /**
     * Upload a dedicated social image (fixed 1200x630, no responsive variants).
     */
    public function uploadSocial(UploadedFile $file, ?int $createdById = null): Gallery
    {
        $disk = $this->disk();
        $directory = now()->format('Y/m/d');
        $filename = $this->generateUniqueFilename($file, $directory, $disk);
        $filepath = "{$directory}/{$filename}";

        $imagePath = $file->getRealPath() ?: $file->getPathname();
        $image = $this->imageManager->decodePath($imagePath);

        if ($image->width() < 1200 || $image->height() < 630) {
            throw new \InvalidArgumentException('Social image must be at least 1200x630 pixels.');
        }

        if ($image->width() === 1200 && $image->height() === 630) {
            // Already exact target dimensions: store original directly with zero quality loss
            $disk->putFileAs($directory, $file, $filename);
            $fileSize = (int) $file->getSize();
        } else {
            // Crop/cover to fixed 1200x630
            $cropped = (clone $image)->cover(1200, 630);
            $extension = strtolower($file->getClientOriginalExtension());
            $encoded = (string) $cropped->encodeUsingFileExtension($extension);

            $disk->put($filepath, $encoded);
            $fileSize = strlen($encoded);
        }

        return Gallery::create([
            'filename'      => $filename,
            'filepath'      => $filepath,
            'mime_type'     => (string) $file->getMimeType(),
            'file_size'     => $fileSize,
            'width'         => 1200,
            'height'        => 630,
            'created_by_id' => $createdById,
        ]);
    }

    /**
     * Generate responsive size variants for standard images.
     */
    protected function generateVariants(
        $image,
        string $directory,
        string $filename,
        Filesystem $disk
    ): void {
        $sourceWidth = (int) $image->width();
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $basename = pathinfo($filename, PATHINFO_FILENAME);

        $variantMap = [
            Gallery::SIZE_SM => Gallery::WIDTH_SM,
            Gallery::SIZE_MD => Gallery::WIDTH_MD,
            Gallery::SIZE_LG => Gallery::WIDTH_LG,
            Gallery::SIZE_XL => Gallery::WIDTH_XL,
        ];

        foreach ($variantMap as $size => $targetWidth) {
            if ($sourceWidth < $targetWidth) {
                continue; // Never upscale
            }

            $variantImage = (clone $image)->scale(width: $targetWidth);
            $variantFilename = "{$basename}-{$size}.{$extension}";
            $variantFilepath = "{$directory}/{$variantFilename}";

            $encoded = (string) $variantImage->encodeUsingFileExtension($extension);
            $disk->put($variantFilepath, $encoded);
        }
    }

    /**
     * Generate missing responsive size variants without overwriting existing files.
     */
    protected function generateMissingVariants(
        $image,
        string $directory,
        string $filename,
        Filesystem $disk
    ): void {
        $sourceWidth = (int) $image->width();
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $basename = pathinfo($filename, PATHINFO_FILENAME);

        $variantMap = [
            Gallery::SIZE_SM => Gallery::WIDTH_SM,
            Gallery::SIZE_MD => Gallery::WIDTH_MD,
            Gallery::SIZE_LG => Gallery::WIDTH_LG,
            Gallery::SIZE_XL => Gallery::WIDTH_XL,
        ];

        foreach ($variantMap as $size => $targetWidth) {
            if ($sourceWidth < $targetWidth) {
                continue;
            }

            $variantFilename = "{$basename}-{$size}.{$extension}";
            $variantFilepath = $directory === '.'
                ? $variantFilename
                : "{$directory}/{$variantFilename}";

            if ($disk->exists($variantFilepath)) {
                continue;
            }

            $variantImage = (clone $image)->scale(width: $targetWidth);
            $encoded = (string) $variantImage->encodeUsingFileExtension($extension);
            $disk->put($variantFilepath, $encoded);
        }
    }

    /**
     * Generate a collision-safe filename within the destination directory.
     */
    protected function generateUniqueFilename(UploadedFile $file, string $directory, Filesystem $disk): string
    {
        $originalExt = strtolower($file->getClientOriginalExtension());
        $originalBase = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $slugBase = Str::slug($originalBase);

        if ($slugBase === '') {
            $slugBase = 'image';
        }

        $filename = "{$slugBase}.{$originalExt}";
        $counter = 2;

        while ($disk->exists("{$directory}/{$filename}")) {
            $filename = "{$slugBase}-{$counter}.{$originalExt}";
            $counter++;
        }

        return $filename;
    }

    protected function disk(): Filesystem
    {
        $diskName = (string) config('filesystems.default', 'local');

        return Storage::disk($diskName);
    }
}
