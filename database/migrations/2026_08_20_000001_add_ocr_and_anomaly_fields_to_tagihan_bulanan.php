<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tagihan_bulanan', function (Blueprint $table) {
            if (!Schema::hasColumn('tagihan_bulanan', 'ocr_confidence')) {
                $table->float('ocr_confidence')->nullable()->after('foto_meteran');
            }
            if (!Schema::hasColumn('tagihan_bulanan', 'anomaly_score')) {
                $table->float('anomaly_score')->nullable()->after('ocr_confidence');
            }
            if (!Schema::hasColumn('tagihan_bulanan', 'status_validasi')) {
                $table->string('status_validasi', 30)->default('NORMAL')->after('anomaly_score');
            }
            if (!Schema::hasColumn('tagihan_bulanan', 'catatan_anomali')) {
                $table->text('catatan_anomali')->nullable()->after('status_validasi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_bulanan', function (Blueprint $table) {
            $table->dropColumn([
                'ocr_confidence',
                'anomaly_score',
                'status_validasi',
                'catatan_anomali',
            ]);
        });
    }
};
