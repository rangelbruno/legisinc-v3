# Sistema Legisinc - Configuração e Templates

## Comando para Resetar e Configurar Tudo

```bash
docker exec -it legisinc-app php artisan migrate:fresh --seed
```

## ✅ O que este comando faz:

### 1. **Templates de Proposições** 
- Cria automaticamente 23 tipos de templates seguindo LC 95/1998
- **Template de Moção** é criado com todas as variáveis funcionais
- Arquivo salvo em: `private/templates/template_mocao_seeder.rtf`

### 2. **Dados da Câmara**
- Configura automaticamente os dados padrão:
  - **Nome**: Câmara Municipal Caraguatatuba  
  - **Endereço**: Praça da República, 40, Centro, Caraguatatuba-SP
  - **Telefone**: (12) 3882-5588
  - **Website**: www.camaracaraguatatuba.sp.gov.br
  - **CNPJ**: 50.444.108/0001-41

### 3. **Usuários do Sistema**
- **Admin**: bruno@sistema.gov.br / Senha: 123456
- **Parlamentar**: jessica@sistema.gov.br / Senha: 123456  
- **Legislativo**: joao@sistema.gov.br / Senha: 123456
- **Protocolo**: roberto@sistema.gov.br / Senha: 123456
- **Expediente**: expediente@sistema.gov.br / Senha: 123456
- **Assessor Jurídico**: juridico@sistema.gov.br / Senha: 123456

## 🏛️ Template de Moção - Variáveis Disponíveis

### Cabeçalho
- `${imagem_cabecalho}` - Imagem do cabeçalho
- `${cabecalho_nome_camara}` → **CÂMARA MUNICIPAL DE CARAGUATATUBA**
- `${cabecalho_endereco}` → **Praça da República, 40, Centro**
- `${cabecalho_telefone}` → **(12) 3882-5588**
- `${cabecalho_website}` → **www.camaracaraguatatuba.sp.gov.br**

### Proposição
- `${numero_proposicao}/${ano_atual}` → **0001/2025**
- `${ementa}` → Ementa da proposição
- `${texto}` → Conteúdo da proposição (IA ou manual)
- `${justificativa}` → Justificativa (opcional)

### Dados do Autor
- `${autor_nome}` → Nome do parlamentar
- `${autor_cargo}` → **Vereador**

### Data e Local  
- `${municipio}, ${dia} de ${mes_extenso} de ${ano_atual}`
- `${assinatura_padrao}` → **__________________________________**
- `${rodape_texto}` → Texto institucional do rodapé

## 🔄 Fluxo Completo de Funcionamento

1. **Administrador** cria templates com variáveis
2. **Parlamentar** cria proposição tipo "moção"
3. **Sistema** detecta tipo e busca template (ID: 6)
4. **Variáveis** são substituídas pelos dados corretos
5. **Documento** é gerado com estrutura formal
6. **Parlamentar** edita no OnlyOffice com template aplicado
7. **Legislativo** recebe documento formatado para análise

## ⚙️ Correções Aplicadas

### OnlyOfficeService.php (Linha 1804)
**Problema**: Sistema forçava template ABNT ignorando template do administrador
**Solução**: Template do administrador agora tem precedência

### PreventBackHistory.php
**Problema**: Middleware quebrava downloads do OnlyOffice  
**Solução**: Bypass para BinaryFileResponse e StreamedResponse

## 📋 Estrutura do Template Final

```rtf
CÂMARA MUNICIPAL DE CARAGUATATUBA
Praça da República, 40, Centro
(12) 3882-5588
www.camaracaraguatatuba.sp.gov.br

MOÇÃO Nº 0001/2025

EMENTA: [Ementa da proposição]

A Câmara Municipal manifesta:

[Texto da proposição criado pelo parlamentar]

[Justificativa se houver]

Resolve dirigir a presente Moção.

Caraguatatuba, 12 de agosto de 2025.

__________________________________
[Nome do Parlamentar]
Vereador
```

## 🎯 Resultado Final

✅ **Templates funcionando** com todas as variáveis  
✅ **Dados da câmara** configurados automaticamente  
✅ **OnlyOffice** integrado e funcional  
✅ **Fluxo parlamentar** → **legislativo** operacional  
✅ **Permissões** configuradas por perfil  
✅ **Migrate fresh** preserva toda configuração  

## 🚀 Como Testar

1. Execute: `docker exec -it legisinc-app php artisan migrate:fresh --seed`
2. Acesse: http://localhost:8001
3. Login: jessica@sistema.gov.br / 123456
4. Crie uma moção
5. Abra no editor OnlyOffice
6. Verifique se template está aplicado com variáveis substituídas

---

**Configuração preservada após migrate:fresh --seed** ✅