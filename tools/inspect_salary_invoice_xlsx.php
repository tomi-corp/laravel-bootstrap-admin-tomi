<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$path = 'C:\Users\FIDAC\Downloads\TomiGaji_Invoice TOMI to KSP_for reza.xlsx';

$reader = IOFactory::createReaderForFile($path);
$reader->setReadDataOnly(false);
$spreadsheet = $reader->load($path);

foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
    echo 'SHEET|' . $worksheet->getTitle() . '|' . $worksheet->getHighestRow() . '|' . $worksheet->getHighestColumn() . PHP_EOL;

    $highestColumnIndex = Coordinate::columnIndexFromString($worksheet->getHighestColumn());
    $highestRow = $worksheet->getHighestRow();

    for ($row = 1; $row <= $highestRow; $row++) {
        $values = [];
        $hasValue = false;

        for ($column = 1; $column <= $highestColumnIndex; $column++) {
            $cell = $worksheet->getCell(Coordinate::stringFromColumnIndex($column) . $row);
            $value = $cell->getValue();
            $calculatedValue = null;

            if ($value !== null && $value !== '') {
                $hasValue = true;
                if (is_string($value) && str_starts_with($value, '=')) {
                    try {
                        $calculatedValue = $cell->getCalculatedValue();
                    } catch (Throwable $exception) {
                        $calculatedValue = '#ERR ' . $exception->getMessage();
                    }
                }
            }

            $values[] = [
                'cell' => Coordinate::stringFromColumnIndex($column) . $row,
                'value' => $value,
                'calculated' => $calculatedValue,
            ];
        }

        if ($hasValue) {
            echo json_encode($values, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        }
    }
}
