<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'ADMIN';
    case LEGISLATIVO = 'LEGISLATIVO';
    case PARLAMENTAR = 'PARLAMENTAR';
    case RELATOR = 'RELATOR';
    case PROTOCOLO = 'PROTOCOLO';
    case ASSESSOR = 'ASSESSOR';
    case CIDADAO_VERIFICADO = 'CIDADAO_VERIFICADO';
    case PUBLICO = 'PUBLICO';

    public function getLevel(): int
    {
        return match($this) {
            self::ADMIN => 100,
            self::LEGISLATIVO => 80,
            self::PARLAMENTAR => 70,
            self::RELATOR => 65,
            self::PROTOCOLO => 50,
            self::ASSESSOR => 40,
            self::CIDADAO_VERIFICADO => 20,
            self::PUBLICO => 10,
        };
    }

    public function canAccessRole(UserRole $targetRole): bool
    {
        return $this->getLevel() >= $targetRole->getLevel();
    }

    public function getDefaultPermissions(): array
    {
        return match($this) {
            self::ADMIN => ['*'],
            self::LEGISLATIVO => [
                'dashboard.view',
                'parlamentares.*',
                'comissoes.*',
                'projetos.*',
                'sessoes.*',
                'modelos.*'
            ],
            self::PARLAMENTAR => [
                'dashboard.view',
                'parlamentares.view',
                'comissoes.view',
                'projetos.view',
                'projetos.create',
                'sessoes.view',
                'proposicoes.create',       // Criar Proposição
                'proposicoes.view_own',     // Minhas Proposições
                'proposicoes.sign'          // Assinatura
            ],
            self::RELATOR => [
                'dashboard.view',
                'parlamentares.view',
                'comissoes.view',
                'projetos.view',
                'projetos.create',
                'projetos.edit',
                'sessoes.view'
            ],
            self::PROTOCOLO => [
                'dashboard.view',
                'projetos.view',
                'projetos.create',
                'sessoes.view',
                'sessoes.create'
            ],
            self::ASSESSOR => [
                'dashboard.view',
                'parlamentares.view',
                'comissoes.view',
                'projetos.view',
                'sessoes.view'
            ],
            self::CIDADAO_VERIFICADO => [
                'dashboard.view',
                'parlamentares.view',
                'projetos.view',
                'sessoes.view'
            ],
            self::PUBLICO => [
                'dashboard.view'
            ],
        };
    }

    public function getLabel(): string
    {
        return match($this) {
            self::ADMIN => 'Administrador',
            self::LEGISLATIVO => 'Assessoria Legislativa',
            self::PARLAMENTAR => 'Parlamentar',
            self::RELATOR => 'Relator',
            self::PROTOCOLO => 'Protocolo',
            self::ASSESSOR => 'Assessor',
            self::CIDADAO_VERIFICADO => 'Cidadão Verificado',
            self::PUBLICO => 'Público Geral',
        };
    }

    public function getDescription(): string
    {
        return match($this) {
            self::ADMIN => 'Acesso completo a todas as funcionalidades do sistema',
            self::LEGISLATIVO => 'Acesso administrativo exceto gestão de usuários',
            self::PARLAMENTAR => 'Acesso para visualização e criação de projetos',
            self::RELATOR => 'Acesso estendido para relatoria de projetos',
            self::PROTOCOLO => 'Acesso focado em protocolo e sessões',
            self::ASSESSOR => 'Acesso limitado para assessoria',
            self::CIDADAO_VERIFICADO => 'Acesso público verificado',
            self::PUBLICO => 'Acesso público básico',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::ADMIN => 'red',
            self::LEGISLATIVO => 'blue',
            self::PARLAMENTAR => 'purple',
            self::RELATOR => 'indigo',
            self::PROTOCOLO => 'green',
            self::ASSESSOR => 'yellow',
            self::CIDADAO_VERIFICADO => 'gray',
            self::PUBLICO => 'slate',
        };
    }

    public static function getAllCases(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->getLabel(),
            'description' => $case->getDescription(),
            'level' => $case->getLevel(),
            'color' => $case->getColor(),
        ], self::cases());
    }
}