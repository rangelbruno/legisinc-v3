<?php

namespace App\Services\Admin;

use App\Models\TipoParametro;
use Illuminate\Support\Facades\Validator;

class ValidacaoParametroService
{
    /**
     * Validar valor de acordo com o tipo de parâmetro
     */
    public function validarValor(TipoParametro $tipo, mixed $valor, array $regrasAdicionais = []): array
    {
        $regras = $this->obterRegrasValidacao($tipo, $regrasAdicionais);
        $validator = Validator::make(['valor' => $valor], ['valor' => $regras]);

        if ($validator->fails()) {
            return [
                'valido' => false,
                'erros' => $validator->errors()->get('valor')
            ];
        }

        // Validações específicas por tipo
        $validacaoEspecifica = $this->validarPorTipo($tipo, $valor);

        if (!$validacaoEspecifica['valido']) {
            return $validacaoEspecifica;
        }

        return [
            'valido' => true,
            'erros' => []
        ];
    }

    /**
     * Obter regras de validação para um tipo
     */
    private function obterRegrasValidacao(TipoParametro $tipo, array $regrasAdicionais = []): array
    {
        $regras = $tipo->getValidationRules();
        
        // Adicionar regras específicas da configuração
        $config = $tipo->configuracao_padrao_formatada;
        
        switch ($tipo->codigo) {
            case 'string':
                if (isset($config['max_length'])) {
                    $regras[] = 'max:' . $config['max_length'];
                }
                if (isset($config['min_length']) && $config['min_length'] > 0) {
                    $regras[] = 'min:' . $config['min_length'];
                }
                if (isset($config['regex']) && $config['regex']) {
                    $regras[] = 'regex:' . $config['regex'];
                }
                break;
                
            case 'text':
                if (isset($config['max_length'])) {
                    $regras[] = 'max:' . $config['max_length'];
                }
                if (isset($config['min_length']) && $config['min_length'] > 0) {
                    $regras[] = 'min:' . $config['min_length'];
                }
                break;
                
            case 'integer':
                if (isset($config['min'])) {
                    $regras[] = 'min:' . $config['min'];
                }
                if (isset($config['max'])) {
                    $regras[] = 'max:' . $config['max'];
                }
                break;
                
            case 'decimal':
                if (isset($config['min'])) {
                    $regras[] = 'min:' . $config['min'];
                }
                if (isset($config['max'])) {
                    $regras[] = 'max:' . $config['max'];
                }
                break;
                
            case 'date':
                if (isset($config['min_date']) && $config['min_date']) {
                    $regras[] = 'after_or_equal:' . $config['min_date'];
                }
                if (isset($config['max_date']) && $config['max_date']) {
                    $regras[] = 'before_or_equal:' . $config['max_date'];
                }
                break;
                
            case 'datetime':
                if (isset($config['min_date']) && $config['min_date']) {
                    $regras[] = 'after_or_equal:' . $config['min_date'];
                }
                if (isset($config['max_date']) && $config['max_date']) {
                    $regras[] = 'before_or_equal:' . $config['max_date'];
                }
                break;
                
            case 'file':
                if (isset($config['max_size'])) {
                    $regras[] = 'max:' . $config['max_size'];
                }
                if (isset($config['allowed_extensions'])) {
                    $regras[] = 'mimes:' . implode(',', $config['allowed_extensions']);
                }
                break;
                
            case 'image':
                if (isset($config['max_size'])) {
                    $regras[] = 'max:' . $config['max_size'];
                }
                if (isset($config['allowed_extensions'])) {
                    $regras[] = 'mimes:' . implode(',', $config['allowed_extensions']);
                }
                if (isset($config['max_width'])) {
                    $regras[] = 'dimensions:max_width=' . $config['max_width'];
                }
                if (isset($config['max_height'])) {
                    $regras[] = 'dimensions:max_height=' . $config['max_height'];
                }
                break;
        }

        // Adicionar regras adicionais
        if (!empty($regrasAdicionais)) {
            $regras = array_merge($regras, $regrasAdicionais);
        }

        return $regras;
    }

    /**
     * Validação específica por tipo
     */
    private function validarPorTipo(TipoParametro $tipo, mixed $valor): array
    {
        $config = $tipo->configuracao_padrao_formatada;

        switch ($tipo->codigo) {
            case 'email':
                return $this->validarEmail($valor, $config);
                
            case 'url':
                return $this->validarUrl($valor, $config);
                
            case 'json':
                return $this->validarJson($valor, $config);
                
            case 'array':
                return $this->validarArray($valor, $config);
                
            case 'enum':
                return $this->validarEnum($valor, $config);
                
            case 'color':
                return $this->validarCor($valor, $config);
                
            case 'password':
                return $this->validarPassword($valor, $config);
                
            default:
                return ['valido' => true, 'erros' => []];
        }
    }

    /**
     * Validar email
     */
    private function validarEmail(string $valor, array $config): array
    {
        $erros = [];

        if ($config['multiple'] ?? false) {
            $emails = explode(',', $valor);
            foreach ($emails as $email) {
                $email = trim($email);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $erros[] = "Email inválido: {$email}";
                }
            }
        } else {
            if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
                $erros[] = 'Email inválido';
            }
        }

        // Validar domínios permitidos
        if (!empty($config['domains'])) {
            $emails = $config['multiple'] ? explode(',', $valor) : [$valor];
            foreach ($emails as $email) {
                $email = trim($email);
                $domain = substr(strrchr($email, '@'), 1);
                if (!in_array($domain, $config['domains'])) {
                    $erros[] = "Domínio não permitido: {$domain}";
                }
            }
        }

        return [
            'valido' => empty($erros),
            'erros' => $erros
        ];
    }

    /**
     * Validar URL
     */
    private function validarUrl(string $valor, array $config): array
    {
        $erros = [];

        if (!filter_var($valor, FILTER_VALIDATE_URL)) {
            $erros[] = 'URL inválida';
        } else {
            $parsedUrl = parse_url($valor);
            $scheme = $parsedUrl['scheme'] ?? '';

            // Validar protocolos permitidos
            $protocolosPermitidos = $config['allowed_protocols'] ?? ['http', 'https'];
            if (!in_array($scheme, $protocolosPermitidos)) {
                $erros[] = "Protocolo não permitido: {$scheme}";
            }

            // Validar FTP se não permitido
            if (!($config['allow_ftp'] ?? false) && $scheme === 'ftp') {
                $erros[] = 'Protocolo FTP não permitido';
            }
        }

        return [
            'valido' => empty($erros),
            'erros' => $erros
        ];
    }

    /**
     * Validar JSON
     */
    private function validarJson(string $valor, array $config): array
    {
        $erros = [];

        $decoded = json_decode($valor, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $erros[] = 'JSON inválido: ' . json_last_error_msg();
        } else {
            // Validar estrutura se necessário
            if ($config['validate_structure'] ?? false) {
                $chaves = $config['required_keys'] ?? [];
                foreach ($chaves as $chave) {
                    if (!array_key_exists($chave, $decoded)) {
                        $erros[] = "Chave obrigatória ausente: {$chave}";
                    }
                }
            }
        }

        return [
            'valido' => empty($erros),
            'erros' => $erros
        ];
    }

    /**
     * Validar array
     */
    private function validarArray(string $valor, array $config): array
    {
        $erros = [];

        $separator = $config['separator'] ?? ',';
        $valores = explode($separator, $valor);

        if ($config['trim_values'] ?? true) {
            $valores = array_map('trim', $valores);
        }

        if ($config['remove_empty'] ?? true) {
            $valores = array_filter($valores);
        }

        if (empty($valores)) {
            $erros[] = 'Array não pode estar vazio';
        }

        return [
            'valido' => empty($erros),
            'erros' => $erros
        ];
    }

    /**
     * Validar enum
     */
    private function validarEnum(string $valor, array $config): array
    {
        $erros = [];

        $opcoes = $config['options'] ?? [];
        if (empty($opcoes)) {
            $erros[] = 'Nenhuma opção definida para este enum';
        } else {
            if ($config['multiple'] ?? false) {
                $valores = explode(',', $valor);
                foreach ($valores as $v) {
                    $v = trim($v);
                    if (!in_array($v, $opcoes)) {
                        $erros[] = "Opção inválida: {$v}";
                    }
                }
            } else {
                if (!in_array($valor, $opcoes)) {
                    $erros[] = "Opção inválida: {$valor}";
                }
            }
        }

        return [
            'valido' => empty($erros),
            'erros' => $erros
        ];
    }

    /**
     * Validar cor
     */
    private function validarCor(string $valor, array $config): array
    {
        $erros = [];

        $formato = $config['format'] ?? 'hex';
        $allowAlpha = $config['allow_alpha'] ?? false;

        switch ($formato) {
            case 'hex':
                $pattern = $allowAlpha ? '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$/' : '/^#[A-Fa-f0-9]{6}$/';
                if (!preg_match($pattern, $valor)) {
                    $erros[] = 'Cor em formato hexadecimal inválida';
                }
                break;
                
            case 'rgb':
                $pattern = $allowAlpha ? '/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*(,\s*[01]?\.?\d*)?\s*\)$/' : '/^rgb\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*\)$/';
                if (!preg_match($pattern, $valor)) {
                    $erros[] = 'Cor em formato RGB inválida';
                }
                break;
                
            case 'hsl':
                $pattern = $allowAlpha ? '/^hsla?\(\s*\d{1,3}\s*,\s*\d{1,3}%\s*,\s*\d{1,3}%\s*(,\s*[01]?\.?\d*)?\s*\)$/' : '/^hsl\(\s*\d{1,3}\s*,\s*\d{1,3}%\s*,\s*\d{1,3}%\s*\)$/';
                if (!preg_match($pattern, $valor)) {
                    $erros[] = 'Cor em formato HSL inválida';
                }
                break;
        }

        return [
            'valido' => empty($erros),
            'erros' => $erros
        ];
    }

    /**
     * Validar password
     */
    private function validarPassword(string $valor, array $config): array
    {
        $erros = [];

        $minLength = $config['min_length'] ?? 8;
        if (strlen($valor) < $minLength) {
            $erros[] = "Password deve ter pelo menos {$minLength} caracteres";
        }

        if ($config['require_uppercase'] ?? true) {
            if (!preg_match('/[A-Z]/', $valor)) {
                $erros[] = 'Password deve conter pelo menos uma letra maiúscula';
            }
        }

        if ($config['require_lowercase'] ?? true) {
            if (!preg_match('/[a-z]/', $valor)) {
                $erros[] = 'Password deve conter pelo menos uma letra minúscula';
            }
        }

        if ($config['require_numbers'] ?? true) {
            if (!preg_match('/[0-9]/', $valor)) {
                $erros[] = 'Password deve conter pelo menos um número';
            }
        }

        if ($config['require_symbols'] ?? false) {
            if (!preg_match('/[^A-Za-z0-9]/', $valor)) {
                $erros[] = 'Password deve conter pelo menos um símbolo';
            }
        }

        return [
            'valido' => empty($erros),
            'erros' => $erros
        ];
    }
}