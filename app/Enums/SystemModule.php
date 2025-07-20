<?php

namespace App\Enums;

enum SystemModule: string
{
    case DASHBOARD = 'dashboard';
    case PARLAMENTARES = 'parlamentares';
    case COMISSOES = 'comissoes';
    case SESSOES = 'sessoes';
    case USUARIOS = 'usuarios';
    case MODELOS = 'modelos';

    public function getRoutes(): array
    {
        return match($this) {
            self::DASHBOARD => [
                'dashboard.index' => 'Painel Principal'
            ],
            self::PARLAMENTARES => [
                'parlamentares.index' => 'Listar Parlamentares',
                'parlamentares.create' => 'Criar Parlamentar',
                'parlamentares.edit' => 'Editar Parlamentar',
                'parlamentares.mesa-diretora' => 'Mesa Diretora'
            ],
            self::COMISSOES => [
                'comissoes.index' => 'Listar Comissões',
                'comissoes.create' => 'Criar Comissão',
                'comissoes.edit' => 'Editar Comissão'
            ],
            self::SESSOES => [
                'sessoes.index' => 'Listar Sessões',
                'sessoes.create' => 'Criar Sessão',
                'sessoes.edit' => 'Editar Sessão'
            ],
            self::USUARIOS => [
                'usuarios.index' => 'Listar Usuários',
                'usuarios.create' => 'Criar Usuário',
                'usuarios.edit' => 'Editar Usuário'
            ],
            self::MODELOS => [
                'modelos.index' => 'Listar Modelos',
                'modelos.create' => 'Criar Modelo',
                'modelos.edit' => 'Editar Modelo'
            ],
        };
    }

    public function getLabel(): string
    {
        return match($this) {
            self::DASHBOARD => 'Dashboard',
            self::PARLAMENTARES => 'Parlamentares',
            self::COMISSOES => 'Comissões',
            self::SESSOES => 'Sessões',
            self::USUARIOS => 'Usuários',
            self::MODELOS => 'Modelos',
        };
    }

    public function getIcon(): string
    {
        return match($this) {
            self::DASHBOARD => 'home',
            self::PARLAMENTARES => 'users',
            self::COMISSOES => 'user-group',
            self::SESSOES => 'calendar',
            self::USUARIOS => 'user-circle',
            self::MODELOS => 'template',
        };
    }

    public function getIconClass(): string
    {
        return match($this) {
            self::DASHBOARD => 'fas fa-tachometer-alt',
            self::PARLAMENTARES => 'fas fa-user-tie',
            self::COMISSOES => 'fas fa-users',
            self::SESSOES => 'fas fa-calendar-alt',
            self::USUARIOS => 'fas fa-user-cog',
            self::MODELOS => 'fas fa-copy',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::DASHBOARD => 'blue',
            self::PARLAMENTARES => 'purple',
            self::COMISSOES => 'green',
            self::SESSOES => 'red',
            self::USUARIOS => 'indigo',
            self::MODELOS => 'gray',
        };
    }

    public function getDescription(): string
    {
        return match($this) {
            self::DASHBOARD => 'Painel principal com métricas e visão geral',
            self::PARLAMENTARES => 'Gestão de parlamentares e mesa diretora',
            self::COMISSOES => 'Administração das comissões parlamentares',
            self::SESSOES => 'Gerenciamento de sessões plenárias',
            self::USUARIOS => 'Administração de usuários do sistema',
            self::MODELOS => 'Modelos de documentos',
        };
    }

    public static function getAllWithRoutes(): array
    {
        return array_map(fn($module) => [
            'value' => $module->value,
            'label' => $module->getLabel(),
            'icon' => $module->getIcon(),
            'iconClass' => $module->getIconClass(),
            'color' => $module->getColor(),
            'description' => $module->getDescription(),
            'routes' => $module->getRoutes(),
            'routeCount' => count($module->getRoutes()),
        ], self::cases());
    }

    public static function getRouteToModuleMap(): array
    {
        $map = [];
        foreach (self::cases() as $module) {
            foreach ($module->getRoutes() as $route => $name) {
                $map[$route] = $module->value;
            }
        }
        return $map;
    }

    public static function findModuleByRoute(string $route): ?self
    {
        foreach (self::cases() as $module) {
            if (array_key_exists($route, $module->getRoutes())) {
                return $module;
            }
        }
        return null;
    }
}