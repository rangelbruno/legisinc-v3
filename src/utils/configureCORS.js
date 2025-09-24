import { S3Client, PutBucketCorsCommand } from '@aws-sdk/client-s3';
import dotenv from 'dotenv';
import { fileURLToPath } from 'url';
import { dirname, join } from 'path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

// Carregar variáveis de ambiente
dotenv.config({ path: join(__dirname, '../../.env.local') });

const configureCORS = async () => {
  const s3Client = new S3Client({
    region: process.env.AWS_REGION || 'sa-east-1',
    credentials: {
      accessKeyId: process.env.AWS_ACCESS_KEY_ID,
      secretAccessKey: process.env.AWS_SECRET_ACCESS_KEY,
    },
  });

  const corsConfiguration = {
    CORSRules: [
      {
        AllowedHeaders: ['*'],
        AllowedMethods: ['GET', 'HEAD', 'POST', 'PUT', 'DELETE'],
        AllowedOrigins: [
          'http://localhost:3000',
          'http://localhost:3001',
          'https://legisinc.com.br',
          'https://www.legisinc.com.br',
          'https://editor.legisinc.com.br',
          process.env.NEXT_PUBLIC_URL || 'http://localhost:3000'
        ],
        ExposeHeaders: [
          'ETag',
          'x-amz-server-side-encryption',
          'x-amz-request-id',
          'x-amz-id-2',
          'Content-Length',
          'Content-Type'
        ],
        MaxAgeSeconds: 3000,
      },
    ],
  };

  try {
    const command = new PutBucketCorsCommand({
      Bucket: process.env.AWS_S3_BUCKET_NAME || 'legisinc-documentos',
      CORSConfiguration: corsConfiguration,
    });

    const response = await s3Client.send(command);
    console.log('✅ CORS configurado com sucesso no bucket S3');
    return response;
  } catch (error) {
    console.error('❌ Erro ao configurar CORS:', error);
    throw error;
  }
};

// Executar se chamado diretamente
configureCORS()
  .then(() => process.exit(0))
  .catch(() => process.exit(1));

export { configureCORS };