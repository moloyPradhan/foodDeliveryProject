<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    /**
     * Upload an image to Cloudinary
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param array $options
     * @return string|null
     */
    public function uploadImage(
        UploadedFile $file,
        string $folder = 'uploads',
        array $options = []
    ): ?string {
        try {
            $defaultOptions = [
                'folder' => $folder,
            ];

            $uploadOptions = array_merge($defaultOptions, $options);

            $uploadedFile = Cloudinary::upload(
                $file->getRealPath(),
                [
                    'folder' => $folder,
                    'resource_type' => 'auto',
                ]
            );

            return $uploadedFile->getSecurePath(); // ALWAYS returns string

        } catch (\Throwable $e) {
            Log::error('Cloudinary upload failed', [
                'message' => $e->getMessage(),
                'file'    => $file->getClientOriginalName()
            ]);
            return null;
        }
    }


    /**
     * Upload multiple images
     *
     * @param array $files
     * @param string $folder
     * @return array
     */
    public function uploadMultipleImages(array $files, string $folder = 'uploads'): array
    {
        $uploadedUrls = [];

        foreach ($files as $key => $file) {
            if ($file instanceof UploadedFile) {
                $url = $this->uploadImage($file, $folder);
                if ($url) {
                    $uploadedUrls[$key] = $url;
                }
            }
        }

        return $uploadedUrls;
    }

    /**
     * Delete an image from Cloudinary
     *
     * @param string $imageUrl
     * @return bool
     */
    public function deleteImage(string $imageUrl): bool
    {
        try {
            $publicId = $this->extractPublicId($imageUrl);

            if ($publicId) {
                Cloudinary::destroy($publicId);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Cloudinary delete failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Upload with transformations (resize, crop, etc.)
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param int $width
     * @param int $height
     * @param string $crop
     * @return string|null
     */
    public function uploadWithTransformation(
        UploadedFile $file,
        string $folder = 'uploads',
        int $width = 800,
        int $height = 600,
        string $crop = 'fill'
    ): ?string {
        return $this->uploadImage($file, $folder, [
            'transformation' => [
                'width' => $width,
                'height' => $height,
                'crop' => $crop,
                'quality' => 'auto',
                'fetch_format' => 'auto'
            ]
        ]);
    }

    /**
     * Extract public_id from Cloudinary URL
     *
     * @param string $imageUrl
     * @return string|null
     */
    private function extractPublicId(string $imageUrl): ?string
    {
        try {
            // Example URL: https://res.cloudinary.com/demo/image/upload/v123456/folder/image.jpg
            $parts = explode('/', parse_url($imageUrl, PHP_URL_PATH));

            // Find the index after 'upload'
            $uploadIndex = array_search('upload', $parts);

            if ($uploadIndex === false) {
                return null;
            }

            // Get everything after 'upload' and version
            $pathParts = array_slice($parts, $uploadIndex + 2); // Skip 'upload' and version
            $publicId = implode('/', $pathParts);

            // Remove file extension
            $publicId = pathinfo($publicId, PATHINFO_DIRNAME) . '/' . pathinfo($publicId, PATHINFO_FILENAME);

            return $publicId;
        } catch (\Exception $e) {
            Log::error('Failed to extract public_id: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get transformed image URL (without uploading again)
     *
     * @param string $imageUrl
     * @param int $width
     * @param int $height
     * @param string $crop
     * @return string
     */
    /**
     * Get transformed image URL (without uploading again)
     * Cloudinary allows URL-based transformations
     *
     * @param string $imageUrl
     * @param int $width
     * @param int $height
     * @param string $crop
     * @return string
     */
    public function getTransformedUrl(
        string $imageUrl,
        int $width,
        int $height,
        string $crop = 'fill'
    ): string {
        try {
            // Cloudinary URLs can be transformed by modifying the URL directly
            // Example: https://res.cloudinary.com/demo/image/upload/w_300,h_200,c_fill/sample.jpg

            // Check if it's a valid Cloudinary URL
            if (!str_contains($imageUrl, 'res.cloudinary.com')) {
                return $imageUrl;
            }

            // Replace /upload/ with /upload/w_X,h_Y,c_Z/
            $transformation = "w_{$width},h_{$height},c_{$crop},q_auto,f_auto";

            $transformedUrl = str_replace(
                '/upload/',
                "/upload/{$transformation}/",
                $imageUrl
            );

            return $transformedUrl;
        } catch (\Exception $e) {
            Log::error('Failed to generate transformed URL: ' . $e->getMessage());
            return $imageUrl;
        }
    }
}
