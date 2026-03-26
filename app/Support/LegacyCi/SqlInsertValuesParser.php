<?php

namespace App\Support\LegacyCi;

use InvalidArgumentException;

/**
 * Extracts and parses MySQL INSERT ... VALUES (...) row tuples from dump fragments.
 */
class SqlInsertValuesParser
{
    public static function extractInsertStatement(string $filePath, string $table): string
    {
        $sections = self::extractAllValuesSections($filePath, $table);
        if ($sections === []) {
            throw new InvalidArgumentException("No INSERT found for table `{$table}` in {$filePath}");
        }

        return $sections[0];
    }

    /**
     * phpMyAdmin may split large tables into multiple INSERT statements.
     *
     * @return list<string>
     */
    public static function extractAllValuesSections(string $filePath, string $table): array
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new InvalidArgumentException("Cannot read file: {$filePath}");
        }

        $quoted = preg_quote($table, '/');
        if (! preg_match_all(
            '/INSERT INTO `'.$quoted.'`\s*\([^)]+\)\s*VALUES\s*(.+?);(?=\s*(?:--|INSERT INTO))/s',
            $content,
            $matches
        ) || $matches[1] === []) {
            throw new InvalidArgumentException("No INSERT found for table `{$table}` in {$filePath}");
        }

        return array_map(trim(...), $matches[1]);
    }

    /**
     * @return list<list<mixed>>
     */
    public static function parseAllRowsFromFile(string $filePath, string $table): array
    {
        $rows = [];
        foreach (self::extractAllValuesSections($filePath, $table) as $section) {
            $rows = array_merge($rows, self::parseRows($section));
        }

        return $rows;
    }

    /**
     * @return list<list<mixed>>
     */
    public static function parseRows(string $valuesSection): array
    {
        $valuesSection = trim($valuesSection);
        $rows = [];
        $i = 0;
        $len = strlen($valuesSection);

        while ($i < $len) {
            while ($i < $len && ctype_space($valuesSection[$i])) {
                $i++;
            }
            if ($i >= $len) {
                break;
            }
            if ($valuesSection[$i] !== '(') {
                throw new InvalidArgumentException("Expected '(' at byte {$i}");
            }
            $i++;

            $row = [];
            while (true) {
                $row[] = self::parseValue($valuesSection, $i, $len);
                while ($i < $len && ctype_space($valuesSection[$i])) {
                    $i++;
                }
                if ($i < $len && $valuesSection[$i] === ',') {
                    $i++;

                    continue;
                }
                if ($i < $len && $valuesSection[$i] === ')') {
                    $i++;
                    $rows[] = $row;
                    break;
                }
                throw new InvalidArgumentException("Expected ',' or ')' after value near byte {$i}");
            }

            while ($i < $len && ctype_space($valuesSection[$i])) {
                $i++;
            }
            if ($i < $len && $valuesSection[$i] === ',') {
                $i++;
            }
        }

        return $rows;
    }

    /**
     * @param  int  $len  string length
     */
    private static function parseValue(string $s, int &$i, int $len): mixed
    {
        while ($i < $len && ctype_space($s[$i])) {
            $i++;
        }
        if ($i >= $len) {
            throw new InvalidArgumentException('Unexpected end inside row');
        }

        if (strncasecmp(substr($s, $i, 4), 'NULL', 4) === 0) {
            $j = $i + 4;
            while ($j < $len && ctype_space($s[$j])) {
                $j++;
            }
            if ($j >= $len || $s[$j] === ',' || $s[$j] === ')') {
                $i = $j;

                return null;
            }
        }

        if ($s[$i] === "'") {
            $i++;
            $out = '';
            while ($i < $len) {
                if ($s[$i] === '\\' && $i + 1 < $len) {
                    $out .= $s[$i + 1];
                    $i += 2;

                    continue;
                }
                if ($s[$i] === "'" && $i + 1 < $len && $s[$i + 1] === "'") {
                    $out .= "'";
                    $i += 2;

                    continue;
                }
                if ($s[$i] === "'") {
                    $i++;

                    return $out;
                }
                $out .= $s[$i++];
            }
            throw new InvalidArgumentException('Unterminated string in SQL values');
        }

        if ($s[$i] === '-' || ctype_digit($s[$i]) || ($s[$i] === '.' && $i + 1 < $len && ctype_digit($s[$i + 1]))) {
            $start = $i;
            if ($s[$i] === '-') {
                $i++;
            }
            while ($i < $len && (ctype_digit($s[$i]) || $s[$i] === '.')) {
                $i++;
            }

            return 0 + substr($s, $start, $i - $start);
        }

        throw new InvalidArgumentException("Unexpected token at byte {$i}: ".$s[$i]);
    }
}
