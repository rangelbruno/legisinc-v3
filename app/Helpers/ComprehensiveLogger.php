<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ComprehensiveLogger
{
    /**
     * Log user click events
     */
    public static function userClick(string $action, array $context = []): void
    {
        $baseContext = [
            'timestamp' => now()->toISOString(),
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'user_roles' => Auth::user()?->roles?->pluck('name')->toArray() ?? [],
            'ip_address' => self::safeRequest('ip'),
            'user_agent' => self::safeRequest('header', 'User-Agent'),
            'session_id' => self::safeRequest('session_id'),
        ];

        Log::channel('comprehensive')->info("👤 USER CLICK: {$action}", array_merge($baseContext, $context));
    }

    /**
     * Log OnlyOffice container interactions
     */
    public static function onlyOfficeContainer(string $message, array $context = []): void
    {
        $baseContext = [
            'timestamp' => now()->toISOString(),
            'container_info' => [
                'server_url' => config('onlyoffice.server_url'),
                'internal_url' => config('onlyoffice.internal_url'),
            ]
        ];

        Log::channel('comprehensive')->info("🐳 ONLYOFFICE CONTAINER: {$message}", array_merge($baseContext, $context));
    }

    /**
     * Log legislative approval processes
     */
    public static function legislativeApproval(string $stage, array $context = []): void
    {
        $baseContext = [
            'timestamp' => now()->toISOString(),
            'user_id' => Auth::id(),
            'user_email' => Auth::user()?->email,
            'user_roles' => Auth::user()?->roles?->pluck('name')->toArray() ?? [],
        ];

        Log::channel('comprehensive')->info("🏛️ LEGISLATIVO APPROVAL: {$stage}", array_merge($baseContext, $context));
    }

    /**
     * Log PDF regeneration events
     */
    public static function pdfRegeneration(string $message, array $context = []): void
    {
        $baseContext = [
            'timestamp' => now()->toISOString(),
            'user_id' => Auth::id(),
        ];

        Log::channel('comprehensive')->info("📄 PDF REGENERATION: {$message}", array_merge($baseContext, $context));
    }

    /**
     * Log system events
     */
    public static function system(string $level, string $message, array $context = []): void
    {
        $baseContext = [
            'timestamp' => now()->toISOString(),
            'system_event' => true,
        ];

        Log::channel('comprehensive')->{$level}("🔧 SISTEMA: {$message}", array_merge($baseContext, $context));
    }

    /**
     * Log admin actions
     */
    public static function admin(string $action, array $context = []): void
    {
        $baseContext = [
            'timestamp' => now()->toISOString(),
            'admin_user_id' => Auth::id(),
            'admin_email' => Auth::user()?->email,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->header('User-Agent'),
        ];

        Log::channel('comprehensive')->info("🔑 ADMIN: {$action}", array_merge($baseContext, $context));
    }

    /**
     * Log security events
     */
    public static function security(string $level, string $message, array $context = []): void
    {
        $baseContext = [
            'timestamp' => now()->toISOString(),
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->header('User-Agent'),
            'security_event' => true,
        ];

        Log::channel('comprehensive')->{$level}("🚨 SECURITY: {$message}", array_merge($baseContext, $context));
    }

    /**
     * Log template system events
     */
    public static function template(string $message, array $context = []): void
    {
        $baseContext = [
            'timestamp' => now()->toISOString(),
            'user_id' => Auth::id(),
        ];

        Log::channel('comprehensive')->info("🔍 TEMPLATE SYSTEM: {$message}", array_merge($baseContext, $context));
    }

    /**
     * Log RTF processing events
     */
    public static function rtfProcessing(string $level, string $message, array $context = []): void
    {
        $baseContext = [
            'timestamp' => now()->toISOString(),
            'processing_event' => true,
        ];

        Log::channel('comprehensive')->{$level}("📝 RTF Processing: {$message}", array_merge($baseContext, $context));
    }

    /**
     * Safely access request data to avoid errors in test contexts
     */
    private static function safeRequest(string $method, ...$args): mixed
    {
        try {
            $request = request();
            if (!$request) {
                return null;
            }

            return match($method) {
                'ip' => $request->ip(),
                'header' => $request->header(...$args),
                'session_id' => $request->session()?->getId(),
                default => null
            };
        } catch (\Exception $e) {
            // Em contextos de teste ou quando não há request, retornar valor padrão
            return match($method) {
                'ip' => 'test-context',
                'header' => 'test-user-agent',
                'session_id' => 'test-session',
                default => null
            };
        }
    }
}