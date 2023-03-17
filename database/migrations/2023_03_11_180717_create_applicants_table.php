<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('ref_no')->unique();
            $table->string('address');
            $table->string('city');
            $table->date('dob');
            $table->string('email');
            $table->integer('exam_centre_id');
            $table->string('guardian');
            $table->string('makthab');
            $table->integer('makthab_years')->nullable();
            $table->bigInteger('mobile');
            $table->bigInteger('mobile2');
            $table->string('name');
            $table->string('bc');
            $table->string('tc')->nullable();
            $table->string('image');
            $table->bigInteger('postalcode');
            $table->string('state');
            $table->string('slug');
            $table->string('status')->nullable();
            $table->foreignId('allotment_id')->nullable()->constrained('institutions')->onDelete('cascade');
            $table->string('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applicants');
    }
}
