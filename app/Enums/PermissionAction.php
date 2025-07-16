<?php

namespace App\Enums;

enum PermissionAction: string
{
    case VIEW = 'view';
    case CREATE = 'create';
    case EDIT = 'edit';
    case DELETE = 'delete';
    case MANAGE = 'manage';

    public function getLabel(): string
    {
        return match($this) {
            self::VIEW => 'Visualizar',
            self::CREATE => 'Criar',
            self::EDIT => 'Editar',
            self::DELETE => 'Excluir',
            self::MANAGE => 'Gerenciar',
        };
    }

    public function getDescription(): string
    {
        return match($this) {
            self::VIEW => 'Permite visualizar o conteúdo',
            self::CREATE => 'Permite criar novos registros',
            self::EDIT => 'Permite editar registros existentes',
            self::DELETE => 'Permite excluir registros',
            self::MANAGE => 'Acesso completo (todas as ações)',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::VIEW => 'blue',
            self::CREATE => 'green',
            self::EDIT => 'yellow',
            self::DELETE => 'red',
            self::MANAGE => 'purple',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::VIEW => 'eye',
            self::CREATE => 'plus',
            self::EDIT => 'pencil',
            self::DELETE => 'trash',
            self::MANAGE => 'cog',
        };
    }

    public function getIconClass(): string
    {
        return match($this) {
            self::VIEW => 'fas fa-eye',
            self::CREATE => 'fas fa-plus',
            self::EDIT => 'fas fa-edit',
            self::DELETE => 'fas fa-trash',
            self::MANAGE => 'fas fa-cogs',
        };
    }

    public function getLevel(): int
    {
        return match($this) {
            self::VIEW => 1,
            self::CREATE => 2,
            self::EDIT => 3,
            self::DELETE => 4,
            self::MANAGE => 5,
        };
    }

    public function implies(): array
    {
        return match($this) {
            self::VIEW => [],
            self::CREATE => [self::VIEW],
            self::EDIT => [self::VIEW],
            self::DELETE => [self::VIEW],
            self::MANAGE => [self::VIEW, self::CREATE, self::EDIT, self::DELETE],
        };
    }

    public function isImpliedBy(PermissionAction $action): bool
    {
        return in_array($this, $action->implies());
    }

    public static function getAllWithDetails(): array
    {
        return array_map(fn($action) => [
            'value' => $action->value,
            'label' => $action->getLabel(),
            'description' => $action->getDescription(),
            'color' => $action->getColor(),
            'icon' => $action->getIcon(),
            'iconClass' => $action->getIconClass(),
            'level' => $action->getLevel(),
            'implies' => array_map(fn($implied) => $implied->value, $action->implies()),
        ], self::cases());
    }
}