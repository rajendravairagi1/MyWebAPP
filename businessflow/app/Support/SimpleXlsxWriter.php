<?php

namespace App\Support;

use ZipArchive;

/**
 * A minimal, dependency-free .xlsx (OOXML spreadsheet) writer — used
 * instead of a Composer package like PhpSpreadsheet because this app is
 * deployed by uploading a zip of changed source files to shared hosting
 * with no SSH/composer access (see CPANEL-DEPLOY.md); a new vendor/
 * package would have to be hand-merged into the server's vendor/
 * directory via the cPanel file manager, which is exactly the kind of
 * manual step that has caused broken deploys before on this app. This
 * writes the handful of XML parts a spreadsheet app needs directly,
 * using ZipArchive (already used elsewhere in this app, e.g. backups).
 *
 * Deliberately not a general-purpose library: one string type per cell
 * (numbers vs. everything-else-as-text), inline strings (no shared
 * strings table), one bold header row per sheet, and no column
 * width/number formatting - everything this app's exports actually need
 * and nothing more.
 */
class SimpleXlsxWriter
{
    /** @var array<string, list<list<string|int|float|null>>> */
    private array $sheets = [];

    /**
     * @param  list<string>  $headers
     * @param  list<list<string|int|float|null>>  $rows  Each row must have the same number of columns as $headers.
     */
    public function addSheet(string $name, array $headers, array $rows): static
    {
        $this->sheets[$this->safeSheetName($name)] = array_merge([$headers], $rows);

        return $this;
    }

    public function save(string $path): void
    {
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $sheetNames = array_keys($this->sheets);

        $zip->addFromString('[Content_Types].xml', $this->contentTypesXml(count($sheetNames)));
        $zip->addFromString('_rels/.rels', $this->rootRelsXml());
        $zip->addFromString('xl/workbook.xml', $this->workbookXml($sheetNames));
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelsXml(count($sheetNames)));
        $zip->addFromString('xl/styles.xml', $this->stylesXml());

        $i = 1;
        foreach ($this->sheets as $rows) {
            $zip->addFromString("xl/worksheets/sheet{$i}.xml", $this->sheetXml($rows));
            $i++;
        }

        $zip->close();
    }

    /**
     * Excel sheet names: max 31 chars, and can't contain : \ / ? * [ ].
     * Two different tables truncating to the same 31 chars is not a
     * real concern for this app's fixed, short set of tab names.
     */
    private function safeSheetName(string $name): string
    {
        $name = preg_replace('/[:\\\\\/\?\*\[\]]/', ' ', $name) ?? $name;

        return mb_substr(trim($name), 0, 31) ?: 'Sheet';
    }

    private function contentTypesXml(int $sheetCount): string
    {
        $overrides = '';
        for ($i = 1; $i <= $sheetCount; $i++) {
            $overrides .= "<Override PartName=\"/xl/worksheets/sheet{$i}.xml\" ContentType=\"application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml\"/>";
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .$overrides
            .'</Types>';
    }

    private function rootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>';
    }

    /**
     * @param  list<string>  $sheetNames
     */
    private function workbookXml(array $sheetNames): string
    {
        $sheetsXml = '';
        foreach ($sheetNames as $index => $name) {
            $sheetId = $index + 1;
            $sheetsXml .= '<sheet name="'.$this->escape($name)."\" sheetId=\"{$sheetId}\" r:id=\"rId{$sheetId}\"/>";
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            ."<sheets>{$sheetsXml}</sheets>"
            .'</workbook>';
    }

    private function workbookRelsXml(int $sheetCount): string
    {
        $rels = '';
        for ($i = 1; $i <= $sheetCount; $i++) {
            $rels .= "<Relationship Id=\"rId{$i}\" Type=\"http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet\" Target=\"worksheets/sheet{$i}.xml\"/>";
        }
        $stylesRid = $sheetCount + 1;
        $rels .= "<Relationship Id=\"rId{$stylesRid}\" Type=\"http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles\" Target=\"styles.xml\"/>";

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .$rels
            .'</Relationships>';
    }

    /**
     * Two cell styles: 0 = default, 1 = bold (used for each sheet's
     * header row).
     */
    private function stylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
            .'<fills count="1"><fill><patternFill patternType="none"/></fill></fills>'
            .'<borders count="1"><border/></borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0"/></cellStyleXfs>'
            .'<cellXfs count="2"><xf numFmtId="0" fontId="0" xfId="0"/><xf numFmtId="0" fontId="1" xfId="0" applyFont="1"/></cellXfs>'
            .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            .'</styleSheet>';
    }

    /**
     * @param  list<list<string|int|float|null>>  $rows  Row 0 is the header row (styled bold).
     */
    private function sheetXml(array $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<sheetData>';

        foreach ($rows as $rowIndex => $row) {
            $rowNumber = $rowIndex + 1;
            $isHeader = $rowIndex === 0;
            $xml .= "<row r=\"{$rowNumber}\">";

            foreach (array_values($row) as $colIndex => $value) {
                $ref = $this->columnLetter($colIndex).$rowNumber;
                $style = $isHeader ? ' s="1"' : '';

                if (is_int($value) || is_float($value)) {
                    $xml .= "<c r=\"{$ref}\"{$style}><v>{$value}</v></c>";
                } else {
                    $text = $this->escape((string) ($value ?? ''));
                    $xml .= "<c r=\"{$ref}\"{$style} t=\"inlineStr\"><is><t xml:space=\"preserve\">{$text}</t></is></c>";
                }
            }

            $xml .= '</row>';
        }

        $xml .= '</sheetData></worksheet>';

        return $xml;
    }

    private function columnLetter(int $index): string
    {
        $letter = '';
        $index++;

        while ($index > 0) {
            $rem = ($index - 1) % 26;
            $letter = chr(65 + $rem).$letter;
            $index = intdiv($index - 1, 26);
        }

        return $letter;
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}
