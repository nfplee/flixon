<?php

namespace Flixon\Filesystem;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class CsvHelpers {
    public static function read(UploadedFile $file, string $delimiter = ','): array {
        $data = $fields = []; $i = 0;
        $handle = fopen($file, 'r');

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (empty($fields)) {
                $fields = $row;
                continue;
            }

            foreach ($row as $key => $value) {
                $data[$i][$fields[$key]] = $value;
            }

            $i++;
        }

        fclose($handle);

        return $data;
    }

	public static function write(string $fileName, array $data, string $delimiter = ',') {
		$handle = fopen($fileName, 'w');

        if (count($data) > 0) {
	        // Writes file header.
	        //fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); - This breaks the export/import online.
            fputcsv($handle, array_keys((array)$data[0]));

	        foreach ($data as $line) {
	            fputcsv($handle, (array)$line, $delimiter);
	        }
        }

	    fclose($handle);
	}
}