<?php

use App\Domain\StateMachine\SignalState;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        global $wpdb;
        $prefix = $wpdb->prefix;
        $table_basename = 'signal_state_transition_audit';
        $table_name = $prefix . $table_basename;
        Schema::create($table_basename, function (Blueprint $table) {
            $table->id();
            $table->bigInteger('signal_id');
            $table->enum('from_state', SignalState::cases());
            $table->enum('to_state', SignalState::cases());
            $table->boolean('is_succeeded');
            $table->text('failure_msg')->nullable();
            $table->timestamp('logged_at')->default(DB::raw('CURRENT_TIMESTAMP'));

            $table->index('signal_id');
        });

        DB::statement("
          CREATE TRIGGER prevent_signal_state_audit
          BEFORE DELETE on {$table_name}
          FOR EACH ROW
          BEGIN 
              SIGNAL SQLSTATE '45000'
              SET MESSAGE_TEXT = 'Signal audit cannot be deleted';
          END
          
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signal_state_transition_audi');
        DB::statement('DROP TRIGGER IF EXISTS prevent_signal_state_audit');
    }
};
