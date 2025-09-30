<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function resolveTable(): string
    {
        foreach (['general_settings', 'settings', 'general_setting'] as $name) {
            if (Schema::hasTable($name)) {
                return $name;
            }
        }

        throw new RuntimeException(
            "Nu găsesc tabela pentru setări (general_settings / settings / general_setting). " .
            "Verifică în phpMyAdmin numele exact al tabelei și asigură-te că `.env` pointează la baza corectă."
        );
    }

    public function up(): void
    {
        $tableName = $this->resolveTable();

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (!Schema::hasColumn($tableName, 'invoice_company_name'))       $table->string('invoice_company_name')->nullable();
            if (!Schema::hasColumn($tableName, 'invoice_company_tax_id'))     $table->string('invoice_company_tax_id')->nullable();   // CUI / VAT
            if (!Schema::hasColumn($tableName, 'invoice_company_reg_no'))     $table->string('invoice_company_reg_no')->nullable();   // Nr. RC
            if (!Schema::hasColumn($tableName, 'invoice_company_email'))      $table->string('invoice_company_email')->nullable();
            if (!Schema::hasColumn($tableName, 'invoice_company_phone'))      $table->string('invoice_company_phone')->nullable();

            if (!Schema::hasColumn($tableName, 'invoice_company_address_line1')) $table->string('invoice_company_address_line1')->nullable();
            if (!Schema::hasColumn($tableName, 'invoice_company_address_line2')) $table->string('invoice_company_address_line2')->nullable();
            if (!Schema::hasColumn($tableName, 'invoice_company_country'))       $table->string('invoice_company_country')->nullable();
            if (!Schema::hasColumn($tableName, 'invoice_company_state'))         $table->string('invoice_company_state')->nullable();
            if (!Schema::hasColumn($tableName, 'invoice_company_city'))          $table->string('invoice_company_city')->nullable();
            if (!Schema::hasColumn($tableName, 'invoice_company_zip'))           $table->string('invoice_company_zip')->nullable();

            if (!Schema::hasColumn($tableName, 'invoice_company_bank_name'))  $table->string('invoice_company_bank_name')->nullable();
            if (!Schema::hasColumn($tableName, 'invoice_company_iban'))       $table->string('invoice_company_iban')->nullable();
            if (!Schema::hasColumn($tableName, 'invoice_company_swift_bic'))  $table->string('invoice_company_swift_bic')->nullable();

            if (!Schema::hasColumn($tableName, 'invoice_prefix'))             $table->string('invoice_prefix')->nullable();
            if (!Schema::hasColumn($tableName, 'invoice_due_days'))           $table->unsignedInteger('invoice_due_days')->nullable();
            if (!Schema::hasColumn($tableName, 'invoice_footer_note'))        $table->text('invoice_footer_note')->nullable();
        });
    }

    public function down(): void
    {
        $candidates = ['general_settings', 'settings', 'general_setting'];
        $cols = [
            'invoice_company_name',
            'invoice_company_tax_id',
            'invoice_company_reg_no',
            'invoice_company_email',
            'invoice_company_phone',
            'invoice_company_address_line1',
            'invoice_company_address_line2',
            'invoice_company_country',
            'invoice_company_state',
            'invoice_company_city',
            'invoice_company_zip',
            'invoice_company_bank_name',
            'invoice_company_iban',
            'invoice_company_swift_bic',
            'invoice_prefix',
            'invoice_due_days',
            'invoice_footer_note',
        ];

        foreach ($candidates as $name) {
            if (Schema::hasTable($name)) {
                Schema::table($name, function (Blueprint $table) use ($name, $cols) {
                    foreach ($cols as $col) {
                        if (Schema::hasColumn($name, $col)) {
                            $table->dropColumn($col);
                        }
                    }
                });
                break;
            }
        }
    }
};
