<?php

namespace Database\Seeders;

use App\Models\WebMedia;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WebMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WebMedia::create([
            'page' => 'Home',
            'section' => 'Our Highlight of the Month',
            'link' => 'https://images.unsplash.com/photo-1530842025973-11b5f5013b2e?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1534&q=80',
            'type' => 'image'
        ]);

        WebMedia::create([
            'page' => 'Home',
            'section' => 'Our Highlight of the Month',
            'link' => 'https://images.unsplash.com/photo-1612730376550-e8f1275e1b51?ixid=MXwxMjA3fDB8MHx0b3BpYy1mZWVkfDEzfHRvd0paRnNrcEdnfHxlbnwwfHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=60',
            'type' => 'image'
        ]);

        WebMedia::create([
            'page' => 'Home',
            'section' => 'Our Highlight of the Month',
            'link' => 'https://images.unsplash.com/photo-1526231237819-de846f3a7e16?ixid=MXwxMjA3fDB8MHx0b3BpYy1mZWVkfDE2fHRvd0paRnNrcEdnfHxlbnwwfHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=60vv',
            'type' => 'image'
        ]);

        WebMedia::create([
            'page' => 'Home',
            'section' => 'Portfolio',
            'link' => 'https://images.unsplash.com/photo-1507126117511-e87526de90e2?ixid=MXwxMjA3fDB8MHx0b3BpYy1mZWVkfDE3fHRvd0paRnNrcEdnfHxlbnwwfHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=60',
            'type' => 'image'
        ]);

        WebMedia::create([
            'page' => 'Home',
            'section' => 'Portfolio',
            'link' => 'https://images.unsplash.com/photo-1614492052748-7c182718eaa0?ixid=MXwxMjA3fDB8MHx0b3BpYy1mZWVkfDI1fHRvd0paRnNrcEdnfHxlbnwwfHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=60',
            'type' => 'image'
        ]);

        WebMedia::create([
            'page' => 'Home',
            'section' => 'Portfolio',
            'link' => 'https://images.unsplash.com/photo-1601511086638-a6d6946ed7fd?ixid=MXwxMjA3fDB8MHx0b3BpYy1mZWVkfDI5fHRvd0paRnNrcEdnfHxlbnwwfHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=60',
            'type' => 'image'
        ]);

        WebMedia::create([
            'page' => 'Home',
            'section' => 'Portfolio',
            'link' => 'https://www.youtube.com/embed/T75IKSXVXlc?rel=0&showinfo=0&controls=0&fs=0&modestbranding=1&color=white&iv_load_policy=3&autohide=1&enablejsapi=1',
            'type' => 'video'
        ]);

        WebMedia::create([
            'page' => 'Home',
            'section' => 'Portfolio',
            'link' => 'https://www.youtube.com/embed/2sr9MGkkeks?controls=0&fs=0&modestbranding=1&color=white&iv_load_policy=3&autohide=1&enablejsapi=1',
            'type' => 'video'
        ]);

        WebMedia::create([
            'page' => 'Porfolio',
            'section' => 'Studio Photoshoots',
            'link' => 'https://images.unsplash.com/photo-1507126117511-e87526de90e2?ixid=MXwxMjA3fDB8MHx0b3BpYy1mZWVkfDE3fHRvd0paRnNrcEdnfHxlbnwwfHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=60',
            'type' => 'image'
        ]);

        WebMedia::create([
            'page' => 'Portfolio',
            'section' => 'Outdoor Photoshoots',
            'link' => 'https://images.unsplash.com/photo-1614492052748-7c182718eaa0?ixid=MXwxMjA3fDB8MHx0b3BpYy1mZWVkfDI1fHRvd0paRnNrcEdnfHxlbnwwfHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=60',
            'type' => 'image'
        ]);

        WebMedia::create([
            'page' => 'Portfolio',
            'section' => 'Events',
            'link' => 'https://images.unsplash.com/photo-1601511086638-a6d6946ed7fd?ixid=MXwxMjA3fDB8MHx0b3BpYy1mZWVkfDI5fHRvd0paRnNrcEdnfHxlbnwwfHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=60',
            'type' => 'image'
        ]);

        WebMedia::create([
            'page' => 'Portfolio',
            'section' => 'Birthdays',
            'link' => 'https://images.unsplash.com/photo-1507126117511-e87526de90e2?ixid=MXwxMjA3fDB8MHx0b3BpYy1mZWVkfDE3fHRvd0paRnNrcEdnfHxlbnwwfHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=60',
            'type' => 'video'
        ]);

        WebMedia::create([
            'page' => 'Portfolio',
            'section' => 'Graduations',
            'link' => 'https://images.unsplash.com/photo-1614492052748-7c182718eaa0?ixid=MXwxMjA3fDB8MHx0b3BpYy1mZWVkfDI1fHRvd0paRnNrcEdnfHxlbnwwfHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=60',
            'type' => 'video'
        ]);

        WebMedia::create([
            'page' => 'Portfolio',
            'section' => 'Weddings',
            'link' => 'https://images.unsplash.com/photo-1601511086638-a6d6946ed7fd?ixid=MXwxMjA3fDB8MHx0b3BpYy1mZWVkfDI5fHRvd0paRnNrcEdnfHxlbnwwfHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=60',
            'type' => 'video'
        ]);

        WebMedia::create([
            'page' => 'Downloads',
            'section' => 'Lease Agreement',
            'link' => 'https://images.unsplash.com/photo-1601511086638-a6d6946ed7fd?ixid=MXwxMjA3fDB8MHx0b3BpYy1mZWVkfDI5fHRvd0paRnNrcEdnfHxlbnwwfHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=60',
            'type' => 'document'
        ]);
    }
}
