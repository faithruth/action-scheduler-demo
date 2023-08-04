<?php

class Daily_Jokes {

    public function register() {
        // Schedule the Daily Post Event
        add_action( 'init', 'schedule_daily_joke_post_event' );
    }

    function schedule_daily_joke_post_event() {
        // Use the action_scheduler_recurring function to schedule the event.
        // Set the interval to 'daily' and the time to '10:00' for 10 AM.
        action_scheduler_recurring( '10:00', 'daily', [], 'send_daily_joke_post' );
    }

    // Step 2: Define the Joke Retrieval Function
    function fetch_random_joke() {
        // API URL to fetch a random joke
        $limit = 3;
        $api_url = 'https://api.api-ninjas.com/v1/jokes?limit=' . $limit;
        $args = array(
            'headers' => array(
                'X-Api-Key': 'IbupV65tPPjoEWGmOB9e8Q==EZKPneY56oFi1qKP',
            ),
            'timeout' => 30,
        );
        
        // Make the API request using wp_remote_get()
        $response = wp_remote_get( $api_url, $args );

        if ( is_wp_error( $response ) ) {
            // Handle API request error, if any
            return 'Failed to fetch a joke. Please try again later.';
        }

        // Get the response body as an array
        $jokes_data = json_decode( wp_remote_retrieve_body( $response ), true );

        // Check if the response contains jokes and extract a random one
        if ( isset( $jokes_data['data'] ) && is_array( $jokes_data['data'] ) ) {
            $jokes = $jokes_data['data'];
            $random_joke = $jokes[ array_rand( $jokes ) ];

            // Return the joke text
            return $random_joke['joke'];
        } else {
            return 'No jokes found.';
        }
    }

    // Step 5: Compose and Send the Daily Post
    function send_daily_joke_post() {
        $joke = fetch_random_joke();
        $subject = 'Your Daily Joke';
        $message = 'Here\'s your daily joke:' . "\n\n" . $joke;

        // Use wp_mail() to send the post.
        wp_mail( 'recipient@example.com', $subject, $message );
    }
}