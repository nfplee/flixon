<?php

namespace Flixon\Common;

class UploadHelpers {
	/**
	 * Get file name.
	 *
	 * @param    $name string
	 * @return   string, file name
	 */
	public static function getFileName(string $name, string $extension): string {
		return preg_replace('/[^a-z0-9_-]/i', '', str_replace(' ', '-', strtolower($name))) . '-' . Utilities::generateRandomString() . time() . $extension;
	}

	/**
	 * Upload the file.
	 *
	 * @param    $path string, upload path.
	 * @param    $fileName string, file name to upload.
	 * @param    $tempFile string, temporary uploaded file.
	 * @param    $extensions array, valid extensions array.
	 * @param    $maxFileSize integer, maximum file size.
	 * @param    $maxWidth integer, maximum width.
	 * @param    $maxHeight integer, maximum height.
	 * @return   boolean
	 */
	public static function uploadFile(string $path, string $fileName, string $tempFile, mixed $extensions = false, int $maxFileSize = 0, int $maxWidth = 0, int $maxHeight = 0): bool {
		if (($extensions !== false && !self::validateExtension($fileName, $extensions)) || !self::validateFileSize($tempFile, $maxFileSize) || !self::validateSize($tempFile, $maxWidth, $maxHeight) || !self::validateExistingFile($path, $fileName)) {
			return false;
		} else {
			move_uploaded_file($tempFile, $path . '/' . $fileName);

			return true;
		}
	}

	/**
	 * Upload resized image.
	 *
	 * @param    $path string, upload path.
	 * @param    $fileName string, file name.
	 * @param    $tempFile string, temporary uploaded file.
	 * @param    $newWidth integer, new width.
	 * @param    $newHeight integer, new height.
	 * @param    $quality integer, image quality.
	 * @return   boolean
	 */
	public static function uploadResizedImage(string $path, string $fileName, string $tempFile, int $newWidth, int $newHeight, int $quality = 80): bool {
		list($width, $height) = getimagesize($tempFile);

		// Check if we need to create a thumbnail.
		if ($width <= $newWidth && $height <= $newHeight) {
			copy($tempFile, $path . '/' . $fileName);
		} else {
			$extension = strtolower(strrchr($fileName, '.'));

			if ($extension == '.gif') {
				$image = imagecreatefromgif($tempFile);
			} elseif ($extension == '.jpg' || $extension == '.jpeg') {
				$image = imagecreatefromjpeg($tempFile);
			} elseif ($extension == '.png') {
				$image = imagecreatefrompng($tempFile);
			}

			$ratio = $width / $height;

			if ($newWidth / $newHeight > $ratio) {
				$newWidth = $newHeight * $ratio;
			} else {
				$newHeight = $newWidth / $ratio;
			}

			$thumbnail = imagecreatetruecolor($newWidth, $newHeight);
			$bg = imagecolorallocate($thumbnail, 255, 255, 255); // white
			imagefill($thumbnail, 0, 0, $bg); 
			imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

			if ($extension == '.gif') {
				imagegif($thumbnail, $path . '/' . $fileName, $quality);
			} elseif ($extension == '.jpg' || $extension == '.jpeg') {
				imagejpeg($thumbnail, $path . '/' . $fileName, $quality);
			} elseif ($extension == '.png') {
				imagepng($thumbnail, $path . '/' . $fileName, $quality);
			}
			
			imagedestroy($thumbnail);
		}

		return true;
	}

	/**
	 * Make sure an existing file doesn't exist.
	 *
	 * @param    $path string, upload path.
	 * @param    $fileName string, file name to check.
	 * @return   boolean
	 */
	public static function validateExistingFile(string $path, string $fileName): bool {
		return !file_exists($path . '/' . $fileName);
	}
	
	/**
	 * Validate extension.
	 *
	 * @param    $fileName string, file name to validate.
	 * @param    $extensions array, valid extensions array.
	 * @return   boolean
	 */
	public static function validateExtension(string $fileName, array $extensions): bool {
		return in_array(strtolower(strrchr($fileName, '.')), $extensions);
	}

	/**
	 * Validate file size.
	 *
	 * @param    $tempFile string, temporary file to validate.
	 * @param    $maxFileSize integer, maximum file size.
	 * @return   boolean
	 */
	public static function validateFileSize(string $tempFile, int $maxFileSize): bool {
		return filesize($tempFile) <= $maxFileSize || $maxFileSize == 0;
	}

	/**
	 * Validate size (width and height).
	 *
	 * @param    $tempFile string, temporary file to validate.
	 * @param    $maxWidth integer, maximum width.
	 * @param    $maxHeight integer, maximum height.
	 * @return   boolean
	 */
	public static function validateSize(string $tempFile, int $maxWidth, int $maxHeight): bool {
		list($width, $height) = getimagesize($tempFile);

		return ($width <= $maxWidth && $height <= $maxHeight) || ($maxWidth == 0 && $maxHeight == 0);
	}
}