<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Asset;
use Illuminate\Support\Str;

class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'required|string',
            'tag' => 'required|numeric|unique:assets,tag,'.$id,
            'description' => 'string',
        ];
    }
}
