<?php

class Audit
{
    public static function log(string $action, string $entityType, int $entityId, ?string $changes = null): void
    {
        Database::insert('audit_log', [
            'admin_user_id' => Auth::id(),
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'changes' => $changes,
            'ip_address' => Security::clientIp(),
        ]);
    }

    public static function diff(array $before, array $after): string
    {
        $diff = [];
        foreach ($after as $key => $value) {
            if (array_key_exists($key, $before) && $before[$key] !== $value) {
                $diff[$key] = ['old' => $before[$key], 'new' => $value];
            }
        }
        return json_encode($diff);
    }
}
