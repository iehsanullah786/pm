<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Remove the description column if it exists
            if (Schema::hasColumn('tasks', 'description')) {
                $table->dropColumn('description');
            }

            // Ensure the due_date column is of type dateTime
            if (!Schema::hasColumn('tasks', 'due_date')) {
                $table->dateTime('due_date')->nullable(); // Add due_date column if it doesn't exist
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Optionally, you can reverse the changes made in the up() method
            $table->string('description')->nullable(); // Re-add the description column
            $table->dropColumn('due_date'); // Remove the due_date column
        });
    }
}