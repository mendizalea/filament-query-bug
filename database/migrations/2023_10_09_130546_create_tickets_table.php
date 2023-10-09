<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->string('description');
            $table->enum('status', ['pending', 'wait', 'active']);
            $table->foreignIdFor(User::class, 'requested_by')
                ->constrained('users');
            $table->foreignIdFor(User::class, 'owned_by')
                ->nullable()
                ->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
