<?php

namespace App\Services;

class RTFTextExtractor
{
    /**
     * Extract plain text from RTF content
     */
    public static function extract($rtfContent)
    {
        // Se não for RTF, retornar como está
        if (substr($rtfContent, 0, 5) !== '{\\rtf') {
            return $rtfContent;
        }

        $text = $rtfContent;

        // Step 1: For large RTF files with lots of style definitions,
        // extract only the last part which contains the actual content
        if (strlen($text) > 100000) {
            // Take the last 10KB which should contain the actual document content
            $text = substr($text, -10240);
        }

        // Step 2: Convert Unicode characters FIRST (before removing other commands)
        // Handle \uXXX* format (asterisk as delimiter)
        $text = preg_replace_callback('/\\\\u(-?\\d+)\\*/', function ($matches) {
            $code = intval($matches[1]);
            if ($code < 0) {
                $code = 65536 + $code;
            }

            // Handle different ranges
            if ($code === 225) {
                return 'á';
            }
            if ($code === 226) {
                return 'â';
            }
            if ($code === 227) {
                return 'ã';
            }
            if ($code === 231) {
                return 'ç';
            }
            if ($code === 233) {
                return 'é';
            }
            if ($code === 234) {
                return 'ê';
            }
            if ($code === 237) {
                return 'í';
            }
            if ($code === 243) {
                return 'ó';
            }
            if ($code === 244) {
                return 'ô';
            }
            if ($code === 245) {
                return 'õ';
            }
            if ($code === 250) {
                return 'ú';
            }
            if ($code === 193) {
                return 'Á';
            }
            if ($code === 194) {
                return 'Â';
            }
            if ($code === 195) {
                return 'Ã';
            }
            if ($code === 199) {
                return 'Ç';
            }
            if ($code === 201) {
                return 'É';
            }
            if ($code === 202) {
                return 'Ê';
            }
            if ($code === 205) {
                return 'Í';
            }
            if ($code === 211) {
                return 'Ó';
            }
            if ($code === 212) {
                return 'Ô';
            }
            if ($code === 213) {
                return 'Õ';
            }
            if ($code === 218) {
                return 'Ú';
            }

            // For ASCII and extended ASCII
            if ($code < 256) {
                return chr($code);
            }

            // For other Unicode
            try {
                return mb_convert_encoding(pack('n', $code), 'UTF-8', 'UTF-16BE');
            } catch (\Exception $e) {
                return '';
            }
        }, $text);

        // Handle \uXXX? format (question mark as delimiter)
        $text = preg_replace_callback('/\\\\u(-?\\d+)\\?/', function ($matches) {
            $code = intval($matches[1]);
            if ($code < 0) {
                $code = 65536 + $code;
            }
            if ($code < 256) {
                return chr($code);
            }
            try {
                return mb_convert_encoding(pack('n', $code), 'UTF-8', 'UTF-16BE');
            } catch (\Exception $e) {
                return '';
            }
        }, $text);

        // Step 3: Convert hex encoded characters
        $text = preg_replace_callback("/\\\\'([0-9a-fA-F]{2})/", function ($matches) {
            return chr(hexdec($matches[1]));
        }, $text);

        // Step 4: Handle line breaks and tabs
        $text = str_replace('\\par', "\n", $text);
        $text = str_replace('\\line', "\n", $text);
        $text = str_replace('\\tab', "\t", $text);

        // Step 5: Remove all other RTF commands
        $text = preg_replace('/\\\\[a-z]+[-]?[0-9]*\\s?/i', '', $text);

        // Step 6: Remove special RTF characters
        $text = str_replace(['\\{', '\\}', '\\\\'], ['{', '}', '\\'], $text);

        // Step 7: Simple brace removal (the complex depth logic was causing issues)
        $text = str_replace(['{', '}'], '', $text);

        // Step 8: Clean up
        $text = str_replace(['\\~', '\\-', '\\*'], [' ', '', ''], $text);

        // Remove multiple spaces and normalize line breaks
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n\s*\n\s*\n+/', "\n\n", $text);

        // Remove common RTF artifacts
        $text = preg_replace('/\bd\s+d\b/', ' ', $text); // Remove "d d" patterns
        $text = preg_replace('/\bd\b(?=\s*[A-Z])/', '', $text); // Remove standalone 'd' before uppercase
        $text = preg_replace('/(?<=[a-z])\s+d\s+/', ' ', $text); // Remove ' d ' in middle of text

        // Step 9: Remove corrupted RTF patterns (NEW)
        $text = preg_replace('/\* \* \* \* \*/', '', $text); // Remove "* * * * *" patterns
        $text = preg_replace('/[0-9A-F]{16,}/', '', $text); // Remove long hex sequences like "020F0502020204030204"
        $text = preg_replace('/[;]{2,}/', ';', $text); // Remove multiple semicolons
        $text = preg_replace('/\s*;\s*;\s*/', '; ', $text); // Clean up " ; ; " patterns

        // Step 10: Validate content quality
        $text = self::validateAndCleanContent($text);

        // Clean up multiple spaces again after artifact removal
        $text = preg_replace('/\s+/', ' ', $text);

        // Remove leading/trailing whitespace
        $text = trim($text);

        return $text;
    }

    /**
     * Extract ementa and conteudo from text
     */
    public static function extractEmentaAndConteudo($text)
    {
        $ementa = '';
        $conteudo = '';

        // Clean the text
        $text = trim($text);

        // Extract only the content from the template variables, not the header
        $ementa = '';
        $conteudo = '';

        // Look for content between "Ementa:" and "Texto Principal:" - this is the actual ementa variable
        if (preg_match('/Ementa[:\s]*\s*(.*?)\s*Texto Principal/is', $text, $matches)) {
            $ementa = trim($matches[1]);
        }

        // Look for content after "Texto Principal:" - this is the actual texto variable
        if (preg_match('/Texto Principal[:\s]*\s*(.*?)$/is', $text, $matches)) {
            $conteudo = trim($matches[1]);
        }

        // Fallback: if no clear pattern, try line-by-line
        if (empty($ementa) || empty($conteudo)) {
            $lines = explode("\n", $text);
            $inEmenta = false;
            $inTexto = false;
            $ementaLines = [];
            $conteudoLines = [];

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }

                if (stripos($line, 'Ementa') !== false && stripos($line, ':') !== false) {
                    $inEmenta = true;
                    $inTexto = false;
                    // Extract content after "Ementa:"
                    $parts = explode(':', $line, 2);
                    if (count($parts) > 1 && ! empty(trim($parts[1]))) {
                        $ementaLines[] = trim($parts[1]);
                    }
                } elseif (stripos($line, 'Texto Principal') !== false && stripos($line, ':') !== false) {
                    $inEmenta = false;
                    $inTexto = true;
                    // Extract content after "Texto Principal:"
                    $parts = explode(':', $line, 2);
                    if (count($parts) > 1 && ! empty(trim($parts[1]))) {
                        $conteudoLines[] = trim($parts[1]);
                    }
                } elseif ($inEmenta) {
                    $ementaLines[] = $line;
                } elseif ($inTexto) {
                    $conteudoLines[] = $line;
                }
            }

            if (empty($ementa)) {
                $ementa = implode(' ', $ementaLines);
            }
            if (empty($conteudo)) {
                $conteudo = implode("\n", $conteudoLines);
            }
        }

        // Clean up ementa and conteudo - remove RTF artifacts aggressively
        $ementa = preg_replace('/\s+/', ' ', $ementa);
        $ementa = preg_replace('/^[d\s]*/', '', $ementa); // Remove 'd' at start
        $ementa = preg_replace('/[d\s]*$/', '', $ementa); // Remove 'd' at end
        $ementa = preg_replace('/\s*d\s*d\s*/', ' ', $ementa); // Remove "d d" patterns
        $ementa = preg_replace('/\s*d\s+/', ' ', $ementa); // Remove isolated 'd '
        $ementa = preg_replace('/\s+d\s*/', ' ', $ementa); // Remove ' d'
        $ementa = preg_replace('/\s+/', ' ', $ementa);
        $ementa = trim($ementa);

        $conteudo = preg_replace('/^[d\s]*/', '', $conteudo); // Remove 'd' at start aggressively
        $conteudo = preg_replace('/[d\s]*$/', '', $conteudo); // Remove 'd' at end
        $conteudo = preg_replace('/\s*d\s*d\s*/', ' ', $conteudo); // Remove "d d" patterns
        $conteudo = preg_replace('/\s*d\s+/', ' ', $conteudo); // Remove isolated 'd '
        $conteudo = preg_replace('/\s+d\s*/', ' ', $conteudo); // Remove ' d'
        $conteudo = preg_replace('/\s+/', ' ', $conteudo);
        $conteudo = trim($conteudo);

        if (empty($conteudo)) {
            $conteudo = 'Conteúdo a ser definido';
        }

        // Limit ementa size
        if (strlen($ementa) > 500) {
            $ementa = substr($ementa, 0, 497).'...';
        }

        return [
            'ementa' => $ementa,
            'conteudo' => $conteudo,
        ];
    }

    /**
     * Validate and clean content quality
     */
    private static function validateAndCleanContent(string $text): string
    {
        // If content is too corrupted, return a fallback
        $corruptionScore = 0;

        // Check for common corruption patterns
        if (preg_match_all('/\* \* \* \* \*/', $text)) {
            $corruptionScore += 10;
        }

        if (preg_match_all('/[0-9A-F]{16,}/', $text)) {
            $corruptionScore += 15;
        }

        if (preg_match_all('/[;]{3,}/', $text)) {
            $corruptionScore += 5;
        }

        // If corruption score is too high, return fallback content
        if ($corruptionScore > 20) {
            return 'Conteúdo corrompido detectado. Por favor, edite esta proposição novamente.';
        }

        // Remove any remaining corrupted patterns
        $text = preg_replace('/\* \* \* \* \*/', '', $text);
        $text = preg_replace('/[0-9A-F]{16,}/', '', $text);
        $text = preg_replace('/[;]{3,}/', ';', $text);

        // Ensure content has meaningful text
        $cleanText = preg_replace('/[^a-zA-ZÀ-ÿ0-9\s.,!?;:()\-]/', '', $text);
        $cleanText = preg_replace('/\s+/', ' ', $cleanText);
        $cleanText = trim($cleanText);

        // If cleaned content is too short, return fallback
        if (strlen($cleanText) < 20) {
            return 'Conteúdo insuficiente. Por favor, edite esta proposição novamente.';
        }

        return $cleanText;
    }
}
