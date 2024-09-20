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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', [1, 2, 3, 4, 5])->default(1); // loại ticket: 1 - hỗ trợ, 2 - yêu cầu dịch vụ, 3 - báo lỗi, 4 - góp ý, 5 - khác
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('server_id')->constrained('service_servers')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null'); // người tạo ticket
            $table->foreignId('replied_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('replied_content')->nullable();
            $table->timestamp('replied_at')->nullable(); // thời gian trả lời
            $table->enum('replied_status', [1, 2, 3])->default(1); // trạng thái phản hồi 1 - chưa phản hồi, 2 - đã phản hồi, 3 - đã xử lý
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high'])->default('low'); // mức độ ưu tiên
            $table->timestamp('closed_at')->nullable(); // thời gian đóng ticket
            $table->timestamps();
            $table->string('domain')->nullable(); // domain của ticket
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
