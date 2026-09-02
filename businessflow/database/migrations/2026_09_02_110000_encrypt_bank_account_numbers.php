<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Bank account numbers are the one field in this app worth encrypting at
 * rest — everything else (names, phone numbers, addresses) is either not
 * sensitive enough to justify the cost or is already only a file path,
 * not the document content itself. Existing plaintext values are
 * re-encrypted here (via raw DB queries, not the Eloquent casts, so this
 * runs correctly however old or new the deployed model code is) —
 * skipped if a value already decrypts cleanly, so running this twice
 * (or against a business that was migrated before) is harmless.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_accounts', function (Blueprint $table) {
            $table->text('account_number')->nullable()->change();
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->text('loan_account_number')->nullable()->change();
        });

        $this->encryptColumn('payment_accounts', 'account_number');
        $this->encryptColumn('loans', 'loan_account_number');
    }

    public function down(): void
    {
        $this->decryptColumn('payment_accounts', 'account_number');
        $this->decryptColumn('loans', 'loan_account_number');
    }

    private function encryptColumn(string $table, string $column): void
    {
        DB::table($table)->whereNotNull($column)->where($column, '!=', '')->orderBy('id')->each(function ($row) use ($table, $column) {
            if ($this->looksEncrypted($row->{$column})) {
                return;
            }

            DB::table($table)->where('id', $row->id)->update([
                $column => Crypt::encryptString($row->{$column}),
            ]);
        });
    }

    private function decryptColumn(string $table, string $column): void
    {
        DB::table($table)->whereNotNull($column)->where($column, '!=', '')->orderBy('id')->each(function ($row) use ($table, $column) {
            try {
                $plain = Crypt::decryptString($row->{$column});
            } catch (\Throwable) {
                return;
            }

            DB::table($table)->where('id', $row->id)->update([$column => $plain]);
        });
    }

    private function looksEncrypted(string $value): bool
    {
        try {
            Crypt::decryptString($value);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }
};
