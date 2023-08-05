<?php

class ASD_Daily_Quotes {

    public function register() {
        // Schedule the Daily Post Event
        add_action( 'init', array( $this, 'schedule_emails_event' ) );
        add_action( 'asd_send_emails', array( $this, 'asd_send_emails' ) );
    }

    public function schedule_emails_event() {
        // post a new joke every 3 hrs.
        $now    = new DateTime();
        if ( false === as_has_scheduled_action( 'asd_send_emails' ) ) {
            as_schedule_recurring_action(
                $now->getTimestamp(),
                HOUR_IN_SECONDS * 24,
                'asd_send_emails',
                array(),
                '',
                true
            );
        }
    }

    public function asd_send_emails() {
        $working_days = array(0, 1, 2, 3, 4, 5, 6);
        $now = new DateTime();
        $weekday = (int)$now->format('w');
        if ( in_array( $weekday, $working_days ) ) {
            $final_quote_list = array();
            $quotes_list = file( AS_DEMO_DIR_PATH . '/quotes.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

            foreach ($quotes_list as $quote) {
                $clean_quote = trim($quote);
                $final_quote_list[] = $clean_quote;
            }
    
            $random_quote = $final_quote_list[ array_rand( $final_quote_list ) ];
            $emails = file( AS_DEMO_DIR_PATH . '/mailing_list.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
            foreach ($emails as $email) {
                $subject = "Quote of the day";
                $body = "{$random_quote}<br><br>Kind Regards,<br>Imokol Faith Ruth.";
                $to = trim( $email, '"' );
                $headers = array('Content-Type: text/html; charset=UTF-8','From: Imokol Faith Ruth <faithruth27@gmail.com>');

                wp_mail( $to, $subject, $body, $headers );
                
            }
        }
    }

}
$quotes = new ASD_Daily_Quotes();
$quotes->register();