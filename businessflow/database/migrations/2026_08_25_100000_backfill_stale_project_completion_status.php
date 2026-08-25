<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * One-time data fix: some existing projects have every unit archived
 * (sold & paid off, or written off) but their own `status` column was
 * never flipped to 'completed' — it happened before Project::syncCompletionStatus()
 * existed, or through a path that didn't call it. Corrects those rows so
 * the status badge and the Projects list filter agree with reality.
 */
return new class extends Migration
{
    public function up(): void
    {
        $projectIds = DB::table('projects')->pluck('id');

        foreach ($projectIds as $projectId) {
            $hasAnyUnits = DB::table('project_units')->where('project_id', $projectId)->exists();
            $hasOpenUnits = DB::table('project_units')->where('project_id', $projectId)->whereNull('archived_at')->exists();

            if ($hasAnyUnits && ! $hasOpenUnits) {
                DB::table('projects')->where('id', $projectId)->where('status', '!=', 'completed')->update(['status' => 'completed']);
            }
        }
    }

    public function down(): void
    {
        // Not reversible — we don't know each project's prior status.
    }
};
