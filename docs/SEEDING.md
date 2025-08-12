# Seeders - Dados Gerais da Câmara

## Visão Geral

O sistema possui seeders configurados para preservar as configurações dos **Dados Gerais da Câmara** após executar `migrate:fresh --seed`. Isso garante que as informações institucionais não sejam perdidas durante a recriação do banco de dados.

## Seeders Relacionados

### 1. DadosGeraisParametrosSeeder
- **Localização**: `database/seeders/DadosGeraisParametrosSeeder.php`
- **Função**: Cria a estrutura do módulo "Dados Gerais" com submódulos e campos
- **Executa**: Automaticamente no `DatabaseSeeder`

### 2. DadosGeraisValoresSeeder ✨ NOVO
- **Localização**: `database/seeders/DadosGeraisValoresSeeder.php`
- **Função**: Popula os valores padrão das configurações da Câmara
- **Executa**: Automaticamente após o `DadosGeraisParametrosSeeder`

## Valores Padrão Configurados

### Identificação
- **Nome da Câmara**: "Câmara Municipal Caraguatatuba"
- **Sigla**: "CMC" 
- **CNPJ**: "50.444.108/0001-41"

### Endereço
- **Logradouro**: "Praça da República, 40"
- **Número**: "40"
- **Bairro**: "Centro"
- **Cidade**: "Caraguatatuba"
- **Estado**: "SP"
- **CEP**: "11660-020"

### Contatos
- **Telefone Principal**: "(12) 3882-5588"
- **Telefone Secundário**: "(12) 3882-5589"
- **E-mail Institucional**: "atendimento@camaracaraguatatuba.sp.gov.br"
- **E-mail de Contato**: "presidencia@camaracaraguatatuba.sp.gov.br"
- **Website**: "www.camaracaraguatatuba.sp.gov.br"

### Funcionamento
- **Horário de Funcionamento**: "Segunda a Sexta, 8h às 17h"
- **Horário de Atendimento**: "Segunda a Sexta, 8h às 16h"

### Gestão
- **Legislatura Atual**: "2021-2024"
- **Número de Vereadores**: 9

## Como Usar

### Comando Completo
```bash
docker exec -it legisinc-app php artisan migrate:fresh --seed
```

### Apenas o Seeder de Valores
```bash
docker exec -it legisinc-app php artisan db:seed --class=DadosGeraisValoresSeeder
```

## Personalização

### Para Sua Câmara Municipal

1. **Edite o arquivo** `database/seeders/DadosGeraisValoresSeeder.php`
2. **Modifique os valores** nos métodos:
   - `seedIdentificacao()` - Nome, sigla e CNPJ
   - `seedEndereco()` - Endereço completo
   - `seedContatos()` - Telefones e e-mails
   - `seedFuncionamento()` - Horários
   - `seedGestao()` - Dados da gestão atual

3. **Execute o seeder**:
```bash
docker exec -it legisinc-app php artisan db:seed --class=DadosGeraisValoresSeeder
```

### Exemplo de Personalização

```php
private function seedIdentificacao(ParametroService $service): void
{
    $valores = [
        'nome_camara' => 'Câmara Municipal de São Paulo',
        'sigla_camara' => 'CMSP', 
        'cnpj' => '12.345.678/0001-90',
    ];
    
    foreach ($valores as $campo => $valor) {
        $service->salvarValor('Dados Gerais', 'Identificação', $campo, $valor);
    }
}
```

## Interface de Configuração

Após o seeding, acesse a interface web para ajustar os dados:

🔗 **URL**: `/parametros-dados-gerais-camara`

A interface permite editar todas as configurações de forma visual, organizadas em abas:
- **Identificação** - Nome, sigla, CNPJ
- **Endereço** - Logradouro, bairro, cidade, CEP
- **Contatos** - Telefones, e-mails, website
- **Funcionamento** - Horários de funcionamento e atendimento
- **Gestão** - Presidente, partido, legislatura, vereadores

## Verificação

Para verificar se os dados foram aplicados corretamente:

1. **Via Browser**: Acesse `/parametros-dados-gerais-camara` e verifique se os campos estão preenchidos
2. **Via Console**: Execute o seeder e observe as mensagens de sucesso
3. **Via Banco**: Consulte a tabela `parametros_valores` para verificar os registros

## Notas Importantes

- ✅ **Cache Bypass**: O sistema usa cache bypass para garantir que os dados salvos sejam sempre exibidos
- ✅ **Preservação**: Os valores são preservados automaticamente no `migrate:fresh --seed`
- ✅ **Customização**: Facilmente adaptável para diferentes câmaras municipais
- ⚙️ **Ordem**: O `DadosGeraisValoresSeeder` executa após o `DadosGeraisParametrosSeeder` para garantir que a estrutura existe