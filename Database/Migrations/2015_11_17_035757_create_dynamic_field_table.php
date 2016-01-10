<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDynamicfieldTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('dynamicfield__groups', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('dynamicfield__fields', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('data', 500);
            $table->string('type');
            $table->string('name');
            $table->string('order')->default(0);
            $table->foreign('group_id')->references('id')->on('dynamicfield__groups')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('dynamicfield__entities', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('entity_id')->unsigned();
            $table->integer('field_id')->unsigned();
            $table->string('entity_type', 200);
            $table->foreign('field_id')->references('id')->on('dynamicfield__fields')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('dynamicfield__field_translations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('locale', 5);
            $table->text('value');
            $table->foreign('entity_field_id')->references('id')->on('dynamicfield__entities')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('dynamicfield__repeater_fields', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('data', 500);
            $table->string('type');
            $table->string('name');
            $table->string('order')->default(0);
            $table->foreign('field_id')->references('id')->on('dynamicfield__fields')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('dynamicfield__repeater_translations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('locale', 5);
            $table->string('order')->default(0);
            $table->foreign('entity_repeater_id')->references('id')->on('dynamicfield__entities')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('dynamicfield__repeater_values', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('field_id')->unsigned();
            $table->text('value');
            $table->foreign('translation_id')->references('id')->on('dynamicfield__repeater_translations')->onDelete('cascade');
            $table->foreign('field_id')->references('id')->on('dynamicfield__repeater_fields')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('dynamicfield__rules', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('rule', 500);
            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('dynamicfield__groups')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('dynamicfield__repeater_values');
        Schema::drop('dynamicfield__repeater_translations');
        Schema::drop('dynamicfield__repeater_fields');
        Schema::drop('dynamicfield__field_translations');
        Schema::drop('dynamicfield__entities');
        Schema::drop('dynamicfield__fields');
        Schema::drop('dynamicfield__groups');
        Schema::drop('dynamicfield__rules');
    }
}
