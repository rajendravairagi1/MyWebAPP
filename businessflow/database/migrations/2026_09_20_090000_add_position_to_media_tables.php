<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Drag-and-drop reordering (see UnitMediaController::reorder() and
 * PropertyDealMediaController::reorder()) needs somewhere to persist the
 * order a builder chose - the `latest()` id-based ordering these two
 * media tables used before had no way to represent a custom order at
 * all.
 *
 * Existing rows are backfilled in their current (upload) order per
 * unit/deal + type, so nothing visibly reshuffles the first time this
 * runs - only future drag-and-drop changes will move things around.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unit_media', function (Blueprint $table) {
            if (! Schema::hasColumn('unit_media', 'position')) {
                $table->unsignedInteger('position')->default(0)->after('type');
            }
        });

        Schema::table('property_deal_media', function (Blueprint $table) {
            if (! Schema::hasColumn('property_deal_media', 'position')) {
                $table->unsignedInteger('position')->default(0)->after('type');
            }
        });

        $this->backfill('unit_media', 'project_unit_id');
        $this->backfill('property_deal_media', 'property_deal_id');
    }

    private function backfill(string $table, string $parentColumn): void
    {
        $groups = DB::table($table)
            ->select($parentColumn, 'type')
            ->distinct()
            ->get();

        foreach ($groups as $group) {
            $rows = DB::table($table)
                ->where($parentColumn, $group->$parentColumn)
                ->where('type', $group->type)
                ->orderBy('created_at')
                ->orderBy('id')
                ->get(['id']);

            foreach ($rows as $index => $row) {
                DB::table($table)->where('id', $row->id)->update(['position' => $index]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('unit_media', function (Blueprint $table) {
            if (Schema::hasColumn('unit_media', 'position')) {
                $table->dropColumn('position');
            }
        });

        Schema::table('property_deal_media', function (Blueprint $table) {
            if (Schema::hasColumn('property_deal_media', 'position')) {
                $table->dropColumn('position');
            }
        });
    }
};
