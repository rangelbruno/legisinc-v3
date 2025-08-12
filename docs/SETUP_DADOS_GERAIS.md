# Configuração dos Dados Gerais da Câmara

Este documento descreve como o módulo "Dados Gerais da Câmara" foi configurado no sistema.

## 🗄️ Estrutura do Banco de Dados

O módulo "Dados Gerais" cria automaticamente:

- **1 Módulo**: `Dados Gerais`
- **5 Submódulos**: 
  - Identificação (nome_camara, sigla_camara, cnpj)
  - Endereço (endereco, numero, complemento, bairro, cidade, estado, cep)
  - Contatos (telefone, telefone_secundario, email_institucional, email_contato, website)
  - Funcionamento (horario_funcionamento, horario_atendimento)
  - Gestão (presidente_nome, presidente_partido, legislatura_atual, numero_vereadores)
- **Total de 19 campos** configurados

## 🔄 Execução Automática

O seeder `DadosGeraisParametrosSeeder` está configurado para executar automaticamente quando rodar:

```bash
docker exec -it legisinc-app php artisan migrate:fresh --seed
```

### Arquivos Envolvidos:

1. **Seeder Principal**: `database/seeders/DadosGeraisParametrosSeeder.php`
2. **DatabaseSeeder**: Configurado em `database/seeders/DatabaseSeeder.php` (linha 34)
3. **Interface**: Card especial em `/admin/parametros` com borda azul e badge "Prioritário"
4. **Rota Dedicada**: `/parametros-dados-gerais-camara`

## ✅ Recursos Implementados

- ✅ **Entrada manual de dados** (sem APIs externas)
- ✅ **5 abas organizadas** por categoria
- ✅ **SweetAlerts informativos** ao salvar
- ✅ **Validação de campos obrigatórios**
- ✅ **Sistema de cache otimizado**
- ✅ **Interface responsiva**
- ✅ **Integração com sistema de permissões**

## 🎨 Aparência Visual

- **Card principal**: Borda azul (`border-primary border-2`)
- **Badge**: "Prioritário" em azul
- **Ícone**: `ki-bank` (ícone de banco/instituição)
- **Botão**: Azul com texto "Configurar"

## 🧪 Como Testar

1. **Reset completo do banco**:
   ```bash
   docker exec -it legisinc-app php artisan migrate:fresh --seed
   ```

2. **Verificar criação**:
   - Acesse `/admin/parametros`
   - Verifique se aparece o card "Dados Gerais da Câmara"
   - Clique em "Configurar" para acessar `/parametros-dados-gerais-camara`

3. **Testar salvamento**:
   - Preencha os campos de qualquer aba
   - Clique em "Salvar Configurações"
   - Verifique se aparece o SweetAlert de sucesso

## 🔧 Manutenção

Para modificar os campos ou submódulos:

1. Edite `database/seeders/DadosGeraisParametrosSeeder.php`
2. Execute: `php artisan db:seed --class=DadosGeraisParametrosSeeder --force`
   
**Nota**: O seeder tem proteção contra duplicação - se o módulo já existir, ele pula a criação.

## 📋 Status

- [x] Seeder criado e configurado
- [x] Interface implementada
- [x] SweetAlerts configurados
- [x] Integrado ao DatabaseSeeder
- [x] Testado e funcionando
- [x] Documentado

---
*Última atualização: 2025-08-11*