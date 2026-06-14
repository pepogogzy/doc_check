<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;

/**
 * Service for writing audit log entries.
 *
 * Every significant action (document upload, analysis, rule changes) is
 * recorded here for traceability.
 */
class AuditService
{
    /**
     * Log an action to the audit_logs table.
     *
     * @param  string       $action      e.g. "document_uploaded", "analysis_completed"
     * @param  string       $entityType  e.g. "Document", "Rule", "DocumentAnalysis"
     * @param  int|null     $entityId
     * @param  array|null   $payload     Arbitrary extra data stored as JSON
     * @param  int|null     $userId      Null for system-triggered actions
     */
    public function log(
        string $action,
        string $entityType,
        ?int $entityId = null,
        ?array $payload = null,
        ?int $userId = null,
    ): AuditLog {
        $log = AuditLog::create([
            'user_id'     => $userId ?? auth()->id(),
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'payload'     => $payload,
        ]);

        Log::info("Audit: {$action} on {$entityType}#{$entityId}", [
            'user_id' => $log->user_id,
        ]);

        return $log;
    }
}
