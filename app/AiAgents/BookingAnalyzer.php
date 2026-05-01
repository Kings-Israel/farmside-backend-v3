<?php

namespace App\AiAgents;

use LarAgent\Agent;

class BookingAnalyzer extends Agent
{
    protected $model = 'gpt-4.1-nano';

    protected $history = 'in_memory';

    protected $provider = 'default';

    protected $tools = [];

    protected $responseSchema = [
        'event_name' => [
            'type' => 'string',
            'description' => 'The name of the event or booking.'
        ],
        'event_type' => [
            'type' => 'string',
            'description' => 'The type of the event or booking. Can be either "Photo Shoot", "Video Shoot" or "Photo and Video Shoot".'
        ],
        'event' => [
            'type' => 'string',
            'description' => 'The specific event or booking type. If the event type is "Photo Shoot", the event can be either "Portrait", "Fashion", "Product", "Event" or "Other". If the event type is "Video Shoot", the event can be either "Commercial", "Music Video", "Documentary", "Event" or "Other". If the event type is "Photo and Video Shoot", the event can be either "Commercial", "Music Video", "Documentary", "Event" or "Other".'
        ],
        'event_duration' => [
            'type' => 'string',
            'description' => 'The duration of the event or booking. Can be either "Less than 1 hour",
                "1-2 hours",
                "2-3 hours",
                "3-4 hours",
                "4-5 hours",
                "5-6 hours",
                "6-12 hours" or
                "Over 12 hours"'
        ],
        'event_date' => [
            'type' => 'string',
            'description' => 'The date of the event or booking.'
        ],
        'event_location' => [
            'type' => 'string',
            'description' => 'The location of the event or booking.'
        ],
        'event_duration' => [
            'type' => 'string',
            'description' => 'The duration of the event or booking.'
        ],
        'number_of_outfit_changes' => [
            'type' => 'integer',
            'description' => 'The number of outfit changes the customer will do.'
        ],
        'number_of_people' => [
            'type' => 'integer',
            'description' => 'The number of people attending the event or booking.'
        ],
        'event_description' => [
            'type' => 'string',
            'description' => 'A brief description of the event or booking.'
        ],
    ];

    public function instructions()
    {
        return "You are a booking analyzer agent. Your task is to analyze details provided by the users about their bookings and extract relevant information to categorize the type of event or booking. You will receive details such as the event name, event type, event duration, event date, event location, number of outfit changes, number of people attending, and a brief description of the event. Based on this information, you will categorize the event into one of the following types: 'Photo Shoot', 'Video Shoot', or 'Photo and Video Shoot'. Additionally, you will further classify the specific event type based on the provided details. Your output should be structured according to the defined response schema." ;
    }

    public function prompt($message)
    {
        return $message;
    }
}
