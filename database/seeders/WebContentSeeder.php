<?php

namespace Database\Seeders;

use App\Models\WebContent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WebContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WebContent::create([
            'page' => 'Home',
            'section' => 'About Us',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. At tellus at urna condimentum mattis pellentesque. Sed vulputate mi sit amet. Egestas fringilla phasellus faucibus scelerisque eleifend donec pretium vulputate. Nunc aliquet bibendum enim facilisis gravida neque. Adipiscing elit ut aliquam purus sit amet. Sagittis id consectetur purus ut faucibus pulvinar elementum integer enim.'
        ]);

        WebContent::create([
            'page' => 'Home',
            'section' => 'Our Highlight of the Month',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
        ]);
    }
}
