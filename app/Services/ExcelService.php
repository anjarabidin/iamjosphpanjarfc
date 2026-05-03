<?php

namespace App\Services;

use OpenSpout\Reader\XLSX\Reader as XLSXReader;
use OpenSpout\Writer\XLSX\Writer as XLSXWriter;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Cell;
use Illuminate\Http\UploadedFile;

class ExcelService
{
    /**
     * Read Excel file and return data as array
     */
    public function readExcel(UploadedFile $file): array
    {
        $data = [];

        $reader = new XLSXReader();
        $reader->open($file->getPathname());

        foreach ($reader->getSheetIterator() as $sheet) {
            $isFirstRow = true;
            $headers = [];

            foreach ($sheet->getRowIterator() as $row) {
                $cells = $row->getCells();
                $rowData = [];

                foreach ($cells as $cell) {
                    $rowData[] = $cell->getValue();
                }

                if ($isFirstRow) {
                    $headers = $rowData;
                    $isFirstRow = false;
                    continue;
                }

                if (!empty(array_filter($rowData))) {
                    $data[] = array_combine($headers, $rowData);
                }
            }
        }

        $reader->close();

        return $data;
    }

    /**
     * Write data to Excel file
     */
    public function writeExcel(array $data, array $headers, string $filename): string
    {
        $filePath = storage_path('app/temp/' . $filename);

        // Ensure temp directory exists
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $writer = new XLSXWriter();
        $writer->openToFile($filePath);

        // Write headers
        $headerCells = [];
        foreach ($headers as $header) {
            $headerCells[] = Cell::fromValue($header);
        }
        $headerRow = new Row($headerCells);
        $writer->addRow($headerRow);

        // Write data
        foreach ($data as $item) {
            $cells = [];
            foreach ($headers as $key => $header) {
                $value = is_string($key) ? ($item[$key] ?? '') : ($item[$header] ?? '');
                $cells[] = Cell::fromValue($value);
            }
            $row = new Row($cells);
            $writer->addRow($row);
        }

        $writer->close();

        return $filePath;
    }

    /**
     * Generate template Excel file
     */
    public function generateTemplate(array $headers, string $filename): string
    {
        return $this->writeExcel([], $headers, $filename);
    }
}
