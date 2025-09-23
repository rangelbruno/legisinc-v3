<?php
/**
 * Script de teste para validar conectividade com AWS S3
 * Execute: php test-s3-connection.php
 */

require_once __DIR__ . '/vendor/autoload.php';

// Carregar configurações do .env.local
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '.env.local');
$dotenv->load();

use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;

echo "🧪 Testando conectividade com AWS S3...\n\n";

// Configurar S3
$s3Config = [
    'driver' => 's3',
    'key' => $_ENV['AWS_ACCESS_KEY_ID'],
    'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
    'region' => $_ENV['AWS_DEFAULT_REGION'],
    'bucket' => $_ENV['AWS_BUCKET'],
    'endpoint' => $_ENV['AWS_ENDPOINT_URL'] ?? null,
    'use_path_style_endpoint' => filter_var($_ENV['AWS_USE_PATH_STYLE_ENDPOINT'] ?? false, FILTER_VALIDATE_BOOLEAN),
];

echo "📋 Configurações S3:\n";
echo "   - Region: {$s3Config['region']}\n";
echo "   - Bucket: {$s3Config['bucket']}\n";
echo "   - Endpoint: " . ($s3Config['endpoint'] ?? 'padrão AWS') . "\n";
echo "   - Access Key: " . substr($s3Config['key'], 0, 8) . "...\n\n";

try {
    // Criar instância do S3
    $filesystem = new \League\Flysystem\Filesystem(
        new \League\Flysystem\AwsS3V3\AwsS3V3Adapter(
            new \Aws\S3\S3Client([
                'credentials' => [
                    'key' => $s3Config['key'],
                    'secret' => $s3Config['secret'],
                ],
                'region' => $s3Config['region'],
                'version' => 'latest',
                'endpoint' => $s3Config['endpoint'],
                'use_path_style_endpoint' => filter_var($s3Config['use_path_style_endpoint'], FILTER_VALIDATE_BOOLEAN),
            ]),
            $s3Config['bucket']
        )
    );

    echo "✅ Conexão S3 estabelecida com sucesso!\n";

    // Teste 1: Criar arquivo de teste
    $testFile = 'test/onlyoffice-s3-test-' . time() . '.txt';
    $testContent = "Teste de conectividade S3 - OnlyOffice PDF Export\nData: " . date('Y-m-d H:i:s');

    echo "\n🔄 Testando upload...\n";
    $filesystem->write($testFile, $testContent);
    echo "✅ Arquivo enviado: $testFile\n";

    // Teste 2: Verificar se arquivo existe
    echo "\n🔄 Testando verificação de arquivo...\n";
    if ($filesystem->fileExists($testFile)) {
        echo "✅ Arquivo confirmado no S3\n";
    } else {
        throw new Exception("Arquivo não encontrado após upload");
    }

    // Teste 3: Ler conteúdo
    echo "\n🔄 Testando leitura...\n";
    $readContent = $filesystem->read($testFile);
    if (strpos($readContent, 'OnlyOffice PDF Export') !== false) {
        echo "✅ Conteúdo lido com sucesso\n";
    } else {
        throw new Exception("Conteúdo não confere");
    }

    // Teste 4: Gerar URL temporária (simulação)
    echo "\n🔄 Testando geração de URL temporária...\n";

    $s3Client = new \Aws\S3\S3Client([
        'credentials' => [
            'key' => $s3Config['key'],
            'secret' => $s3Config['secret'],
        ],
        'region' => $s3Config['region'],
        'version' => 'latest',
        'endpoint' => $s3Config['endpoint'],
        'use_path_style_endpoint' => filter_var($s3Config['use_path_style_endpoint'], FILTER_VALIDATE_BOOLEAN),
    ]);

    $command = $s3Client->getCommand('GetObject', [
        'Bucket' => $s3Config['bucket'],
        'Key' => $testFile
    ]);

    $presignedUrl = (string) $s3Client->createPresignedRequest($command, '+1 hour')->getUri();
    echo "✅ URL temporária gerada: " . substr($presignedUrl, 0, 80) . "...\n";

    // Teste 5: Limpeza
    echo "\n🔄 Limpando arquivo de teste...\n";
    $filesystem->delete($testFile);
    echo "✅ Arquivo removido\n";

    echo "\n🎉 TODOS OS TESTES PASSARAM!\n";
    echo "✅ AWS S3 está configurado corretamente para OnlyOffice PDF Export\n";

} catch (Exception $e) {
    echo "\n❌ ERRO: " . $e->getMessage() . "\n";
    echo "\n📋 Verificações sugeridas:\n";
    echo "   1. Credenciais AWS (Access Key/Secret Key)\n";
    echo "   2. Nome do bucket existe e está acessível\n";
    echo "   3. Região está correta\n";
    echo "   4. Permissões IAM para S3\n";
    echo "   5. Conectividade de rede\n";
}

echo "\n" . str_repeat("-", 50) . "\n";
echo "Teste concluído em " . date('Y-m-d H:i:s') . "\n";
?>