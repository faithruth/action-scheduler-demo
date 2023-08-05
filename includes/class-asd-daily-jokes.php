<?php

class ASD_Daily_Jokes {

    public function register() {
        // Create joke page
        add_action( 'init', array( $this, 'create_joke_page' ) );
        // Schedule the Daily Post Event
        add_action( 'init', array( $this, 'schedule_daily_joke_post_event' ) );
        add_action( 'post_daily_joke_post', array( $this, 'post_daily_joke_post' ) );
    }

    /**
     * Create custom Joke page
     */
    public function create_joke_page() {
        // Define the custom page content
        $page_title = 'Daily Joke';
        $page_content = '';

        // Check if the page already exists by title
        $query = new WP_Query(
            array(
                'post_type'              => 'page',
                'title'                  => $page_title,
                'post_status'            => 'all',
                'posts_per_page'         => 1,
                'no_found_rows'          => true,
                'ignore_sticky_posts'    => true,
                'update_post_term_cache' => false,
                'update_post_meta_cache' => false,
                'orderby'                => 'post_date ID',
                'order'                  => 'ASC',
            )
        );
         
        if ( empty( $query->post ) ) {
            // Prepare page data
            $page_data = array(
                'post_title'    => $page_title,
                'post_content'  => $page_content,
                'post_status'   => 'publish',
                'post_type'     => 'page',
            );

            // Insert the new page
            wp_insert_post( $page_data );
        } 

    }

    public function schedule_daily_joke_post_event() {
        // post a new joke every 3 hrs.
        $now    = new DateTime();
        if ( false === as_has_scheduled_action( 'post_daily_joke_post' ) ) {
            as_schedule_recurring_action(
                $now->getTimestamp(),
                HOUR_IN_SECONDS * 3,
                'post_daily_joke_post',
                array(),
                '',
                true
            );
        }
    }

    /**
     * Fetch random joke from api ninjas
     */
    public function fetch_random_joke() {
        // API URL to fetch a random joke
        $limit = 1;
        $api_url = 'https://api.api-ninjas.com/v1/jokes?limit=' . $limit;
        $args = array(
            'headers' => array(
                'X-Api-Key' => 'IbupV65tPPjoEWGmOB9e8Q==EZKPneY56oFi1qKP',
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
        if ( isset( $jokes_data ) && is_array( $jokes_data) ) {
            $random_joke = $jokes_data[0];
            // Return the joke text
            return $random_joke['joke'];
        } else {
            return 'No jokes found.';
        }
    }

    /**
     * Post daily jokes
     */
    public function post_daily_joke_post() {
        $joke = $this->fetch_random_joke();
        // Define the custom page content
        $page_title = 'Daily Joke';
        // Check if the page already exists by title
        $query = new WP_Query(
            array(
                'post_type'              => 'page',
                'title'                  => $page_title,
                'post_status'            => 'all',
                'posts_per_page'         => 1,
                'no_found_rows'          => true,
                'ignore_sticky_posts'    => true,
                'update_post_term_cache' => false,
                'update_post_meta_cache' => false,
                'orderby'                => 'post_date ID',
                'order'                  => 'ASC',
            )
        );
         
        if ( ! empty( $query->post ) ) {
            // Prepare updated page data
            $updated_page_data = array(
                'ID'           => $query->post->ID,
                'post_content' => $joke,
            );
            // Update the page
            wp_update_post( $updated_page_data );
        } 

    }
}
$jokes = new ASD_Daily_Jokes();
$jokes->register();