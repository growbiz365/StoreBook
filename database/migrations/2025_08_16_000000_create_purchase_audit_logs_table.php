n <?php

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
        Schema::create('purchase_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('purchase_id');
            $table->unsignedBigInteger('user_id');
            $table->string('action'); // edit_started, line_added, line_removed, line_modified, etc.
            $table->text('description');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('changes')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['purchase_id', 'created_at']);
            $table->index(['business_id', 'action']);
        });

        // Add foreign key constraints after table creation
        Schema::table('purchase_audit_logs', function (Blueprint $table) {
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_audit_logs');
    }
};
