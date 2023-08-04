<?php

class Daily_Quotes {

    public function register() {
        // Schedule the Daily Post Event
        add_action( 'init', 'schedule_emails_event' );
    }

    function schedule_emails_event() {
        // Use the action_scheduler_recurring function to schedule the event.
        // Set the interval to 'daily' and the time to '10:00' for 10 AM.
        action_scheduler_recurring( '10:00', 'daily', [], 'send_emails' );
    }

    function send_emails() {
        $working_days = array(0, 1, 2, 3, 4);
        $now = new DateTime();
        $weekday = (int)$now->format('w');
    
        if (in_array($weekday, $working_days)) {
            $final_quote_list = array();
            $quotes_list = file('quotes.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($quotes_list as $quote) {
                $clean_quote = trim($quote);
                $final_quote_list[] = $clean_quote;
            }
    
            $random_quote = $final_quote_list[array_rand($final_quote_list)];
    
            $emails = file('mailing_list.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($emails as $email) {
                $to_addr = trim($email);
                $subject = "Quote of the day";
                $message = "Subject: $subject\r\n$random_quote\r\n\r\nKind Regards,\r\nSamuel KK.";
                $headers = "From: kibirigekalules@gmail.com\r\n";
    
                if (wp_mail($to_addr, $subject, $message, $headers)) {
                    echo "Email sent to: $to_addr\n";
                } else {
                    echo "Failed to send email to: $to_addr\n";
                }
            }
        }
    }

}