<?php

use App\Models\Property;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('title');
        });

        Property::withTrashed()->orderBy('id')->chunk(100, function ($properties) {
            $seen = [];
            foreach ($properties as $property) {
                $base = Str::slug($property->title) ?: 'property';
                $slug = $base;
                $i = 2;
                while (in_array($slug, $seen, true)
                    || Property::withTrashed()->where('slug', $slug)->where('id', '!=', $property->id)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $seen[] = $slug;
                $property->slug = $slug;
                $property->saveQuietly();
            }
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
