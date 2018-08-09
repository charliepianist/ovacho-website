<?php 
require 'keys.php';

function visible_dump($var) {
    echo '<br><br><div style="color: black;">';
    var_dump($var);
    echo '</div><br>';
}

function format_ctr($ctr) {
    return '<div style="background:white;border:1px solid #D1D1D1;border-right:2px solid #D1D1D1;border-bottom:2px solid #D1D1D1;color:black;width:300px;margin-left:auto;margin-right:auto;font:11px monospace;margin-top:20px;margin-bottom:20px;padding:10px;"><pre>' . $ctr . '</pre></div>';
}

function send_email($address = 'ovachoinvestments@gmail.com', $subject = 'Default Subject', $message = 'Default Message', $from_email = 'automated@ovacho.com', $from_name = 'Ovacho Investments', $add_headers = '') {
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $headers[] = 'From: ' . $from_name . ' <' . $from_email . '>';
    //add any additional headers
    if($add_headers) {
        $headers = array_merge($headers, $add_headers);
    }

    //send the email
    return wp_mail($address, $subject, $message, $headers);
}

function three_day_email($id, $time = 0) {
    if($time === 0) $time = time();
    return ($time >= get_user_meta($id, 'subscription_end_time', true) - 259200) && get_user_meta($id, 'three_day_email_sent', true) != 'true';
}

function charge_subscription($id) {
    $user_meta = get_user_meta($id);

    switch($user_meta['subscription_type'][0]) {
        case 'classic':
            if(stored_payment_method($id) == 'card') {
            $amount = get_monthly_amount($id);
            $product_code = '099102';
            $description = 'Classic Subscription (1 Month)';
            if(firstTwoMonths() && $user_meta['discount'][0] == 'true') { 
                $amount = '15.00';
                $product_code = '099100';
                $description = 'Discounted Classic Subscription (1 Month)';
            }

            //referral credit (if credit is greater than cost, it is handled in subscription-refresher.php)
            $referral_credit = get_referral_credit($id);
            if($referral_credit > 0) {
                $amount -= $referral_credit;
                $amount = number_format($amount, 2);
                switch($amount) {
                    case '5.00':
                    $product_code = '099103';
                    break;
                    case '10.00':
                    $product_code = '099104';
                    break;
                    case '15.00':
                    $product_code = '099100';
                    break;
                }
                $description = 'Discounted Classic Subscription (1 Month)';
            }

            $discount_amount = number_format(20.0 - $amount, 2);
            $discount_indicator = '0';
            if($discount_amount !== '0.00') $discount_indicator = '1';
            //$customer_reference = 'USD';
            $reference_no = uniqid('', TRUE);

            //JSON data
            $data = array(
              'gateway_id' => PAYEEZY_GATEWAY_ID,
              'password' => PAYEEZY_API_PASSWORD,
              'transaction_type' => '00', //purchase
              'transarmor_token' => $user_meta['token'][0],
              'cardholder_name' => $user_meta['cardholder_name'][0],
              'amount' => $amount,
              'credit_card_type' => $user_meta['card_type'][0],
              'cc_expiry' => $user_meta['expiry_date'][0],
              'reference_no' => $reference_no,
              'customer_ref' =>  $reference_no, // x_po_num
              'reference_3' => $user_meta['discord_id'][0], //discord_id
              'client_email' => get_user_by('id', $id)->data->user_email,
              'partial_redemption' => 'false', //no partial redemption
              'ecommerce_flag' => '2', // MOTO recurring payments
              'tax1_amount' => '0.00', //for level 2 data
              'level3' => array( //LEVEL 3 DATA
                'discount_amount' => $amount,
                'duty_amount' => '0.00',
                'freight_amount' => '0.00',
                'line_items' => array(
                    array(
                        'commodity_code' => '91528',
                        'description' => $description,
                        'discount_amount' => $discount_amount,
                        'discount_indicator' => $discount_indicator,
                        'gross_net_indicator' => '1', //tax included
                        'line_item_total' => $amount,
                        'product_code' => $product_code, //product code
                        'quantity' => '1',
                        'unit_cost' => $amount,
                        'unit_of_measure' => 'EA',
                    ),
                ),
              ),
            );
            $data_string = json_encode($data);
            //echo $data_string . '<br>';

            $date = date('c');
            $url = 'https://api.globalgatewaye4.firstdata.com/transaction/v27';
            $request_url = '/transaction/v27';
            $key_id = '587044';
            $key = PAYEEZY_HMAC_KEY;
            $method = 'POST';
            $content_digest = sha1($data_string);
            $content_type = 'application/json';

            /*echo '<br><br><br><div style="color:black;">';
            echo $data_string;
            echo '<br>';
            echo $content_digest;
            echo '<br>';
            echo $date;
            echo '<br>';
            echo base64_encode(hash_hmac('sha1', $method . "\n" . $content_type . "\n" . $content_digest . "\n" . $date . "\n" . $request_url, $key, TRUE));
            echo '</div>';*/

            //Initiate cURL
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  

            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: ' . $content_type,
                'Content-Length: ' . strlen($data_string),
                'x-gge4-content-sha1: ' . $content_digest,
                'x-gge4-date: ' . $date,
                'Authorization: ' . 'GGE4_API ' . $key_id . ':' . base64_encode(hash_hmac('sha1', $method . "\n" . $content_type . "\n" . $content_digest . "\n" . $date . "\n" . $request_url, $key, TRUE)),
            ));

            $response = json_decode(curl_exec($ch));
            curl_close($ch);
            var_dump($response);

            //add to user meta
            add_transaction($id, get_var_dump($response));

            $links_html = '<p style="color:#898989; text-align:center;"><a style="color:#898989;" href="' . site_url('privacy-policy') . '">PRIVACY POLICY</a> | <a style="color:#898989;" href="' . site_url('terms') . '">TERMS</a>';

            //transaction successful?
            if($response->transaction_error == 0 && $response->transaction_approved == 1) {

                //remove referral credit
                add_referral_credit($id, -1 * $referral_credit);

                //cancel subscription button
                $links_html .= ' | <a style="color:#898989;" href="' . site_url('account') . '">UNSUBSCRIBE</a></p>';

                $referral_credit_str = '. ';
                if($referral_credit > 0) $referral_credit_str = ' (discounted due to referral credit).<br>Your previous referral credit: <strong>$' . $referral_credit . '</strong><br>Your new referral credit: <strong>$0.00</strong><br>';
                //send email for success
                send_email(get_user_email($id), 'Thank you! Your subscription was renewed automatically.', '<p style="text-align:center;">Your subscription with us has been automatically renewed' . $referral_credit_str . 'Here is your official receipt:</p>' . 
                    format_ctr($response->ctr) . 
                    $links_html);

                return strtotime('+1 month');
            }
            
            //error in transaction
            //send email for error
            $links_html .= '</p>';
            send_email(get_user_email($id), 'There was an error renewing your subscription.', '<p style="text-align:center;">There was an error processing the transaction to renew your subscription. Here is your official receipt:</p>' . 
                format_ctr($response->ctr) . 
                '<p style="text-align:center;"><a href="' . site_url('pricing') . '">Renew your subscription?</a></p>' .
                $links_html);
            }
        break;
    }
    return FALSE;
}

function test_token($token, $cardholder_name, $expiry, $card_type) {
    $ref_num = uniqid('', TRUE);
    $data = array(
              'gateway_id' => PAYEEZY_GATEWAY_ID,
              'password' => PAYEEZY_API_PASSWORD,
              'transaction_type' => '00', //purchase
              'transarmor_token' => $token,
              'cardholder_name' => $cardholder_name,
              'amount' => '0.01',
              'credit_card_type' => $card_type,
              'cc_expiry' => $expiry,
              'reference_no' => $ref_num,
              'customer_ref' =>  $ref_num, // x_po_num
              'reference_3' => 0, //discord_id
              'client_email' => 'ovachoinvestments@gmail.com',
              'partial_redemption' => 'false', //no partial redemption
              'ecommerce_flag' => '2', 
              'tax1_amount' => '0.00', //for level 2 data
              'level3' => array( //LEVEL 3 DATA
                'discount_amount' => '0.00',
                'duty_amount' => '0.00',
                'freight_amount' => '0.00',
                'line_items' => array(
                    array(
                        'commodity_code' => '91528',
                        'description' => 'Tokenization Test',
                        'discount_amount' => '0.00',
                        'discount_indicator' => '0',
                        'gross_net_indicator' => '1', //tax included
                        'line_item_total' => '0.01',
                        'product_code' => '099999', //product code
                        'quantity' => '1',
                        'unit_cost' => '0.01',
                        'unit_of_measure' => 'EA',
                    ),
                ),
              ),
            );
            $data_string = json_encode($data);
            //echo $data_string . '<br>';

            $date = date('c');
            $url = 'https://api.globalgatewaye4.firstdata.com/transaction/v27';
            $request_url = '/transaction/v27';
            $key_id = '587044';
            $key = PAYEEZY_HMAC_KEY;
            $method = 'POST';
            $content_digest = sha1($data_string);
            $content_type = 'application/json';

            /*echo '<br><br><br><div style="color:black;">';
            echo $data_string;
            echo '<br>';
            echo $content_digest;
            echo '<br>';
            echo $date;
            echo '<br>';
            echo base64_encode(hash_hmac('sha1', $method . "\n" . $content_type . "\n" . $content_digest . "\n" . $date . "\n" . $request_url, $key, TRUE));
            echo '</div>';*/

            //Initiate cURL
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  

            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: ' . $content_type,
                'Content-Length: ' . strlen($data_string),
                'x-gge4-content-sha1: ' . $content_digest,
                'x-gge4-date: ' . $date,
                'Authorization: ' . 'GGE4_API ' . $key_id . ':' . base64_encode(hash_hmac('sha1', $method . "\n" . $content_type . "\n" . $content_digest . "\n" . $date . "\n" . $request_url, $key, TRUE)),
            ));

            $response = json_decode(curl_exec($ch));
            var_dump($response);
            curl_close($ch);
}

function tokenize($card_number, $cardholder_name, $expiry) {
    $data = array(
              'gateway_id' => PAYEEZY_GATEWAY_ID,
              'password' => PAYEEZY_API_PASSWORD,
              'transaction_type' => '01', //pre-auth
              'cc_number' => $card_number,
              'cardholder_name' => $cardholder_name,
              'amount' => '0.00',
              'cc_expiry' => $expiry,
            );
            $data_string = json_encode($data);
            //echo $data_string . '<br>';

            $date = date('c');
            $url = 'https://api.globalgatewaye4.firstdata.com/transaction/v27';
            $request_url = '/transaction/v27';
            $key_id = '587044';
            $key = PAYEEZY_HMAC_KEY;
            $method = 'POST';
            $content_digest = sha1($data_string);
            $content_type = 'application/json';

            //Initiate cURL
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  

            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: ' . $content_type,
                'Content-Length: ' . strlen($data_string),
                'x-gge4-content-sha1: ' . $content_digest,
                'x-gge4-date: ' . $date,
                'Authorization: ' . 'GGE4_API ' . $key_id . ':' . base64_encode(hash_hmac('sha1', $method . "\n" . $content_type . "\n" . $content_digest . "\n" . $date . "\n" . $request_url, $key, TRUE)),
            ));

            $response = json_decode(curl_exec($ch));
            curl_close($ch);
            var_dump($response);
            return $response->transarmor_token;
} 

//=====================REFERRAL====================

function get_forms_amount($id) {
    //returns '20.00' if user does not exist
    $amount = get_next_amount($id);
    $referral_credit = get_referral_credit($id);
    if($referral_credit > 0) {
      if($referral_credit > $amount) $referral_credit = $amount;
      $amount -= $referral_credit;
      $amount = number_format($amount, 2);
    }
    return $amount;
}

function add_referral_credit($id, $amount) {
    if(get_userdata($id)) {
        $credit = get_referral_credit($id);
        update_user_meta($id, 'referral_credit', $credit + $amount);
        return true;
    }
    return false;
} 

function get_referral_credit($id) {
    $credit = get_user_meta($id, 'referral_credit', true);
    if($credit) return number_format($credit, 2);
    return '0.00';
}

function get_paid_referred_users_count($id) {
    $arr = get_user_meta($id, 'referred_paid', true);
    if($arr) return count($arr);
    return 0;
}

function get_referral_id($id) {
    //multiply (id + 10000) by 21646753, then convert to base 36
    return base_convert(bcmul($id + 10000, '21646753'), 10, 36);
}

function reverse_referral_id($ref_id) {
    //convert to base 10, then divide by 2164753, then subtract 10000
    $base10 = base_convert($ref_id, 36, 10);
    if(bcmod($base10, '21646753') === '0') {
        return bcsub(bcdiv(base_convert($ref_id, 36, 10), '21646753'), 10000);
    }
}

function has_subscribed($id) {
    if(get_user_meta($id, 'subscription_end_time', true) || get_user_meta($id, 'has_subscribed', true)) return true;
    return false;
}

function get_referrer_id($id) {
    return get_user_meta($id, 'ref_id', true);
}

function referred_first_time($id) {
    if(get_referrer_id($id)) {
        if(!has_subscribed($id)) return true;
    }
    return false;
}

function add_referred_user($ref_id, $id) {
    $arr = get_user_meta($ref_id, 'referred', true);
    if(!$arr) $arr = array();
    array_push($arr, get_username($id) . ' (' . $id . ')');
    update_user_meta($ref_id, 'referred', $arr);
    update_user_meta($id, 'ref_id', $ref_id);
}

function add_referred_paid_user($ref_id, $id) {
    $arr = get_user_meta($ref_id, 'referred_paid', true);
    if(!$arr) $arr = array();
    array_push($arr, get_username($id) . ' (' . $id . ')');
    update_user_meta($ref_id, 'referred_paid', $arr);
    update_user_meta($id, 'ref_id', $ref_id);
}

//USER FUNCTIONS
function get_username($id) {
    return get_user_by('id', $id)->data->display_name;
}

function get_monthly_amount($id) {
    $amount = get_user_meta($id, 'monthly_amount', true);
    if($amount) return $amount;
    if(get_user_subscription($id) == 'classic') return DEFAULT_SUBSCRIPTION_AMOUNT;
}

//should be able to handle custom monthly amounts? (not currently used for that purpose)
function get_next_amount($id) {
    $amount = get_user_meta($id, 'monthly_amount', true);
    if((get_user_meta($id, 'discount', true) == 'true' && firstTwoMonths()) || referred_first_time($id)) $amount = '15.00';
    if($amount) return $amount;
    return DEFAULT_SUBSCRIPTION_AMOUNT;
}

//=====================SUBSCRIPTION===================

function get_active_subscriber_count() {
    $users = get_users();
    $count = 0;
    foreach($users as $user) {
        if(get_user_subscription($user->id) === 'classic' && get_user_meta($user->id, 'subscription_active', true) === 'true') {
            $count++;
        }
    }
    return $count;
}

function get_real_subscriber_count() {
    $users = get_users();
    $count = 0;
    foreach($users as $user) {
        if(get_user_subscription($user->id) === 'classic' && get_user_meta($user->id, 'transactions', true)) {
            $count++;
        }
    }
    return $count;
}

function get_subscriber_count() {
    $users = get_users();
    $count = 0;
    foreach($users as $user) {
        if(get_user_subscription($user->id) === 'classic') {
            $count++;
        }
    }
    return $count;
}

function get_user_count() {
    return count(get_users());
}

function get_user_email($id) {
    $email = get_user_meta($id, 'email', true);
    if($email) return $email;
    return get_user_by('id', $id)->data->user_email;
}

function stored_payment_method($id) {
    $user_meta = get_user_meta($id);
    if($user_meta['token'] && $user_meta['cardholder_name'] && $user_meta['expiry_date'] && $user_meta['card_type']) return 'card';
    if(strcasecmp($user_meta['card_type'], 'paypal') == 0) return 'paypal';
    return NULL;
}

function nice_stored_payment_method($id) {
    switch(stored_payment_method($id)) {
        case 'card':
        $token = get_user_meta($id, 'token', true);
        return 'Card ending in ' . substr($token, -4);
        break;
    }
    return 'N/A';
}

function revoke_subscription($id) {
    update_user_meta($id, 'subscription_type', 'basic');
    update_user_meta($id, 'subscription_active', 'false');
    remove_discord_user(get_user_meta($id, 'discord_id', true));
}

function has_subscription_expired($id, $time = 0) {
    if($time === 0) $time = time();
    $end_time = get_user_meta($id, 'subscription_end_time', true);

    if($end_time <= $time) return TRUE;
    return FALSE;
}

function user_canceled_subscription($id) {
    return get_user_meta($id, 'subscription_active', true) === 'false';
}

function is_subscription_active($id) {
    if(get_user_meta($id, 'subscription_active', true) == 'true') return TRUE;
    return FALSE;
}
function get_user_subscription($id) {
    $sub_type = get_user_meta($id, 'subscription_type', true);
    if(!$sub_type) return 'basic';
    return $sub_type;
}

function add_transaction($id, $toAdd) {
    $trans_array = get_user_meta($id, 'transactions', true);
    if(!$trans_array) {
        $trans_array = array();
    }
    array_push($trans_array, $toAdd);
    array_push($trans_array, get_referral_credit($id));
    update_user_meta($id, 'transactions', $trans_array);
}

function get_var_dump($var) {
    ob_start();
    var_dump($var);
    return ob_get_clean();
}

function get_discord_username($id) {
    return get_discord_user($id)->username;
}

function get_discord_user($id) {
    return json_decode(get_discord_user_raw($id));
}

function get_discord_user_raw($id) {

    $discord_api_url = 'https://discordapp.com/api/users/' . $id;
    //Initiate cURL
    $ch = curl_init($discord_api_url);
     
    //We want the result / output returned.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     
    //Http headers
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'User-Agent: OvachoRoleManager (https://www.ovacho.com, 1)',
        'Authorization: Bot ' . DISCORD_BOT_TOKEN,
    ));
     
    //Execute the request.
    $response = curl_exec($ch); 

    curl_close($ch);

    return $response;
}

function identify_discord() {
    $discord_api_url = 'https://discordapp.com/api/gateway/bot';
    //Initiate cURL
    $ch = curl_init($discord_api_url);
     
    //We want the result / output returned.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     
    //Http headers
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'User-Agent: OvachoRoleManager (https://www.ovacho.com, 1)',
        'Authorization: Bot ' . DISCORD_BOT_TOKEN,
    ));
     
    //Execute the request.
    $response = curl_exec($ch);

    //Get gateway URL
    $gateway_url = json_decode($response)->url;

    curl_close($ch);
}

function send_discord_message_to_user($id, $message) {
    $channel_id = create_dm_channel($id);
    send_discord_message($channel_id, $message);
}

function send_discord_message($channel_id, $message) {
    $discord_api_url = 'https://discordapp.com/api/channels/' . $channel_id . '/messages';

    $data = array('content' => $message);

    $data_string = json_encode($data);

    //Initiate cURL
    $ch = curl_init($discord_api_url);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  

    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'User-Agent: OvachoRoleManager (https://www.ovacho.com, 1)',
        'Authorization: Bot '. DISCORD_BOT_TOKEN,
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string),
    ));

    $response = curl_exec($ch);

    curl_close($ch);
}

function create_dm_channel($id) {
    $discord_api_url = 'https://discordapp.com/api/users/@me/channels';

    $data = array('recipient_id' => $id);

    $data_string = json_encode($data);

    //Initiate cURL
    $ch = curl_init($discord_api_url);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  

    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'User-Agent: OvachoRoleManager (https://www.ovacho.com, 1)',
        'Authorization: Bot ' . DISCORD_BOT_TOKEN,
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string),
    ));

    //get response from discord API
    $response = curl_exec($ch);
    $response_obj = json_decode($response);

    curl_close($ch);

    //return DM channel id
    return $response_obj->id;
}

//subscription_type user meta -> 'basic' or 'classic'
function add_discord_user($id) {

    $discord_api_url = 'https://discordapp.com/api/guilds/409179607665999872/members/' . $id . '/roles/439322496253165568';
    //Initiate cURL
    $ch = curl_init($discord_api_url);
     
    //Use the CURLOPT_PUT option to tell cURL that
    //this is a PUT request.
    curl_setopt($ch, CURLOPT_PUT, true);
     
    //We want the result / output returned.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     
    //Http headers
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'User-Agent: OvachoRoleManager (https://www.ovacho.com, 1)',
        'Authorization: Bot ' . DISCORD_BOT_TOKEN,
    ));
     
    //Execute the request.
    $response = curl_exec($ch);

    curl_close($ch);
}

function remove_discord_user($id) {
    $discord_api_url = 'https://discordapp.com/api/guilds/409179607665999872/members/' . $id . '/roles/439322496253165568';
    //Initiate cURL
    $ch = curl_init($discord_api_url);
     
    //Use the CURLOPT_PUT option to tell cURL that
    //this is a PUT request.
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
     
    //We want the result / output returned.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     
    //Http headers
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'User-Agent: OvachoRoleManager (https://www.ovacho.com, 1)',
        'Authorization: Bot ' . DISCORD_BOT_TOKEN,
    ));
     
    //Execute the request.
    $response = curl_exec($ch);

    curl_close($ch);
}

//seconds
function firstWeek($time = 0) {
    if($time === 0) $time = time();
    return $time < 1530763200;
}

function firstTwoMonths() {
    return time() < 1535428800;
}

function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

//for watchlist/recap page, makes WP HTML into site HTML
function o_format_watchlist_recap_img($o_unformatted) {
	//uri of base image, minus extension
    $o_formatted = o_linked_extract_img_src_srcset($o_unformatted, 'class = "image-3" sizes="100vw"');
	return $o_formatted;
}

//return the_content instead of echo
function get_the_content_with_formatting ($more_link_text = '(more...)', $stripteaser = 0, $more_file = '') {
	$content = get_the_content($more_link_text, $stripteaser, $more_file);
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
	return $content;
}

//filter charts/analysis
//add_filter('the_content', 'o_filter_charts_analysis');
function o_filter_charts_analysis($o_content = '', $o_permalink = '#') {
	//separate into two images
	$o_first_img_index = strpos($o_content, 'img class="');
	$o_second_img_index = strpos($o_content, '<img class="', $o_first_img_index + 3);

	//reformat first image
	$o_first = substr($o_content, 0, $o_second_img_index);
    $o_formatted_content = o_linked_extract_img_src_srcset($o_first, 'class = "image-3" sizes="100vw"');

	//reformat second image
	$o_second = substr($o_content, $o_second_img_index);
    $o_formatted_content = $o_formatted_content . '<br>' . o_linked_extract_img_src_srcset($o_second, 'class = "image-3" sizes="100vw"');
	return $o_formatted_content;
	
}

function o_linked_extract_img_src_srcset($o_str, $o_attributes) {
    $o_src_index = strpos($o_str, 'src="');
    $o_end_src_index = strpos($o_str, '"', $o_src_index + 5);
    $o_srcset_index = strpos($o_str, 'srcset="');
    $o_end_srcset_index = strpos($o_str, '"', $o_srcset_index + 8);
    $o_href = substr($o_str, $o_src_index + 5, $o_end_src_index - $o_src_index - 5);

    $o_formatted_str = '<a href="' . $o_href . '"><img ' . $o_attributes . ' ' . substr($o_str, $o_src_index, $o_end_src_index - $o_src_index + 1) . ' ' . substr($o_str, $o_srcset_index, $o_end_srcset_index - $o_srcset_index + 1) . '></a>';
    return $o_formatted_str;
}



add_image_size( '1600-width', 1600, 1067);
add_image_size( '1080-width', 1080, 720);
add_image_size( '500-width', 500, 333);

function load_styles() {
  wp_enqueue_style('style', get_stylesheet_uri());
  wp_enqueue_style('webflow_ovacho', get_stylesheet_directory_uri() . '/css/ovacho.webflow.css');
  wp_enqueue_style('normalize', get_stylesheet_directory_uri() . '/css/normalize.css');
  //wp_enqueue_style('fonts', 'https://fonts.googleapis.com/css?family=Merriweather+Sans:100,200,400,700,700i,800,800i');
}

add_action('wp_enqueue_scripts', 'load_styles');

//disable autop (converting <br> into <p>)
remove_filter ('the_content', 'wpautop');

// register custom post type 'watchlist'
add_action( 'init', 'create_watchlist_post' );
function create_watchlist_post() {
    register_post_type('watchlist',
      array(
        'labels' => array( 'name' => __('Watchlists')),
        'public' => true,
        'rewrite' => array('slug' => 'watchlists'),
        'hierarchical' => false,
        'supports' => array(
            'title',
            'editor',
            'revisions',)
        )
    );
    flush_rewrite_rules();
}

// register custom post type 'basic-watchlist'
add_action( 'init', 'create_basic_watchlist_post' );
function create_basic_watchlist_post() {
    register_post_type('basic-watchlist',
      array(
        'labels' => array( 'name' => __('Basic Watchlists')),
        'public' => true,
        'rewrite' => array('slug' => 'basic-watchlists'),
        'hierarchical' => false,
        'supports' => array(
            'title',
            'editor',
            'revisions',)
        )
    );
    flush_rewrite_rules();
}

// register custom post type 'recap'
add_action( 'init', 'create_recap_post' );
function create_recap_post() {
    register_post_type('recap',
      array(
        'labels' => array( 'name' => __('Recaps')),
        'public' => true,
        'rewrite' => array('slug' => 'recaps'),
        'hierarchical' => false,
        'supports' => array(
	        'title',
	        'editor',
	        'revisions',)
    	)
	);
	flush_rewrite_rules();
}

// register custom post type 'chart-analysis'
add_action( 'init', 'create_chart_analysis_post' );
function create_chart_analysis_post() {
    register_post_type('chart-analysis',
      array(
        'labels' => array( 'name' => __('Chart/Analyses')),
        'public' => true,
        'rewrite' => array('slug' => 'chart-analysis'),
        'hierarchical' => false,
        'taxonomies' => array('tickers'),
        'supports' => array(
            'title',
            'editor',
            'revisions',)
        )
    );
    flush_rewrite_rules();
}

// register custom post type 'basic-chart-analysis'
add_action( 'init', 'create_basic_chart_analysis_post' );
function create_basic_chart_analysis_post() {
    register_post_type('basic-chart-analysis',
      array(
        'labels' => array( 'name' => __('Basic Chart/Analyses')),
        'public' => true,
        'rewrite' => array('slug' => 'basic-chart-analysis'),
        'hierarchical' => false,
        'taxonomies' => array('tickers'),
        'supports' => array(
            'title',
            'editor',
            'revisions',)
        )
    );
    flush_rewrite_rules();
}

//hook into the init action and call create_tickers_taxonomy when it fires
 
add_action( 'init', 'create_tickers_taxonomy', 0 );
 
function create_tickers_taxonomy() {
  $labels = array(
    'name' => _x( 'Tickers', 'taxonomy general name' ),
    'singular_name' => _x( 'Ticker', 'taxonomy singular name' ),
    'search_items' =>  __( 'Search Tickers' ),
    'popular_items' => __( 'Popular Tickers' ),
    'all_items' => __( 'All Tickers' ),
    'parent_item' => null,
    'parent_item_colon' => null,
    'edit_item' => __( 'Edit Ticker' ), 
    'update_item' => __( 'Update Ticker' ),
    'add_new_item' => __( 'Add New Ticker' ),
    'new_item_name' => __( 'New Ticker Name' ),
    'separate_items_with_commas' => __( 'Separate tickers with commas' ),
    'add_or_remove_items' => __( 'Add or remove tickers' ),
    'choose_from_most_used' => __( 'Choose from the most used tickers' ),
    'menu_name' => __( 'Tickers' ),
  ); 
 
//register taxonomy
  register_taxonomy('tickers','post',array(
    'hierarchical' => false,
    'labels' => $labels,
    'show_ui' => true,
    'show_admin_column' => true,
    'update_count_callback' => '_update_post_term_count',
    'query_var' => true,
    'rewrite' => array( 'slug' => 'ticker' ),
  ));
}

/*$page_id = "";
$product_pages_args = array(
'meta_key' => '_wp_page_template',
'meta_value' => 'login.php'
);

$product_pages = get_pages($product_pages_args);
foreach ( $product_pages as $product_page ) {
$page_id .= $product_page->ID;
}*/

//add a new form element
add_action( 'register_form', 'myplugin_register_form' );
function myplugin_register_form() {

    $full_name = ( ! empty( $_POST['full_name'] ) ) ? sanitize_text_field( $_POST['full_name'] ) : '';
    $ref_id = ( ! empty( $_POST['ref_id'] ) ) ? $_POST['ref_id'] : '';
        
        ?>
        <p>
            <label for="full_name"><?php _e( 'Full Name', 'mydomain' ) ?><br>
                <input type="text" name="full_name" id="full_name" class="input" value="<?php echo esc_attr(  $full_name  ); ?>" size="25"></label>
        </p>
        <p>
            <label for="ref_id"><?php _e( 'Referrer Id', 'mydomain' ) ?><br>
                <input type="text" name="ref_id" id="ref_id" class="input" value="<?php echo $ref_id; ?>" size="25"></label>
        </p>
        <?php
    }

    //validation
    add_filter( 'registration_errors', 'myplugin_registration_errors', 10, 3 );
    function myplugin_registration_errors( $errors, $sanitized_user_login, $user_email ) {
        
        if ( empty( $_POST['full_name'] ) || ! empty( $_POST['full_name'] ) && trim( $_POST['full_name'] ) == '' ) {
        $errors->add( 'name_error', sprintf('<strong>%s</strong>: %s',__( 'ERROR', 'mydomain' ),__( 'You must include a full name.', 'mydomain' ) ) );
        }
        
        if ( empty( $_POST['pwd'] ) || ! empty( $_POST['pwd'] ) && trim( $_POST['pwd'] ) == '' ) {
        $errors->add( 'pwd_error', sprintf('<strong>%s</strong>: %s',__( 'ERROR', 'mydomain' ),__( 'You must include a password.', 'mydomain' ) ) );
        }
        
        if ( empty( $_POST['confirm_pwd'] ) || ! empty( $_POST['confirm_pwd'] ) && trim( $_POST['confirm_pwd'] ) == '' ) {
        $errors->add( 'confirm_pwd_error', sprintf('<strong>%s</strong>: %s',__( 'ERROR', 'mydomain' ),__( 'You must confirm your password.', 'mydomain' ) ) );
        }

        if(!empty($_POST['confirm_pwd']) && !empty($_POST['pwd'])) {
            if(strcmp($_POST['confirm_pwd'],$_POST['pwd']) !== 0) $errors->add( 'equal_pwds_error', sprintf('<strong>%s</strong>: %s',__( 'ERROR', 'mydomain' ),__( 'Your passwords must match.', 'mydomain' ) ) );
        }

        if($errors->get_error_code()) {
            $o_redirect_url = get_bloginfo('url') . '/register/?';
            if(!empty($_POST['ref_id'])) $o_redirect_url .= 'ref=' . get_referral_id($_POST['ref_id']) . '&';
            $o_num = 0;
            foreach($errors->get_error_codes() as $o_code) {
                if($o_num !== 0) $o_redirect_url .= '&';
                $o_redirect_url .= $o_code . '=true';
                $o_num++;
            }
            wp_redirect($o_redirect_url);
        }
        return $errors;
    }

    //save extra registration user meta
    add_action( 'user_register', 'o_user_register' );
    function o_user_register( $user_id ) {
        if (!empty( $_POST['full_name'])) {
            update_user_meta( $user_id, 'first_name', sanitize_text_field( $_POST['full_name'] ) );
        }
        if(!empty($_POST['pwd'])) {
            wp_set_password(sanitize_text_field($_POST['pwd']), $user_id);
        }
        if(!empty($_POST['ref_id'])) {
            $ref_id = $_POST['ref_id'];
            //make sure user didn't refer self (if they figure out equation to find referral id)
            if($ref_id != $user_id) {
                update_user_meta($user_id, 'ref_id', $ref_id);
                //referred people
                add_referred_user($ref_id, $user_id);
            }
        }
        $redirect_url = site_url( 'login' );
        $redirect_url = add_query_arg( 'register', 'true', $redirect_url );
        wp_redirect($redirect_url);
    }


//=============DISABLE TOOLBAR FOR NON-ADMINS==================
add_action('after_setup_theme', 'remove_admin_bar');
 
function remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
}
}

//=============REDIRECT FROM WP-LOGIN.PHP TO LOGIN PAGE========

function goto_login_page() {
//global $page_id;
//$login_page = site_url( '/?page_id='. $page_id. '/' );
$login_page = get_bloginfo('url') . '/login/';
$page = basename($_SERVER['REQUEST_URI']);

if( $page == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET') {
wp_redirect($login_page);
exit;
}
}
add_action('init','goto_login_page');

function login_failed() {
//global $page_id;
//$login_page = site_url( '/?page_id='. $page_id. '/' );
$login_page = site_url('login/?login=failed');
wp_redirect($login_page);
//wp_redirect( $login_page . '&login=failed' );
exit;
}
add_action( 'wp_login_failed', 'login_failed' );

function blank_username_password( $user, $username, $password ) {
    //global $page_id;
    //$login_page = site_url( '/?page_id='. $page_id. '/' );
    $page = basename($_SERVER['REQUEST_URI']);
    if(strpos($page, 'checkemail=registered') !== false) {
        $login_page = site_url('login/?checkemail=registered');
        wp_redirect($login_page);
        exit;
    }else {
        $login_page = site_url('login/?login=blank');
        if( $username == "" || $password == "" ) {
        wp_redirect($login_page);
        exit;
        }
    }
}
add_filter( 'authenticate', 'blank_username_password', 1, 3);

function logout_page() {
//global $page_id;
//$login_page = site_url( '/?page_id='. $page_id. '/' );
$login_page = site_url('login');
wp_redirect( $login_page );
exit;
}
add_action('wp_logout', 'logout_page');

//===============CUSTOM PASSWORD RESET==================
add_action( 'login_form_lostpassword', 'o_password_reset');
function o_password_reset() {
    if ('POST' == $_SERVER['REQUEST_METHOD'] ) {
        $errors = retrieve_password();
        if ( is_wp_error( $errors ) ) {
            // Errors found
            $redirect_url = site_url( 'reset-password' );
            $redirect_url = add_query_arg( 'errors', join( ',', $errors->get_error_codes() ), $redirect_url );
        } else {
            // Email sent
            $redirect_url = site_url('login');
            $redirect_url = add_query_arg( 'checkemail', 'confirm', $redirect_url );
        }
 
        wp_redirect( $redirect_url );
        exit;
    }
}

add_filter( 'retrieve_password_message', 'replace_retrieve_password_message', 10, 4 );
function replace_retrieve_password_message( $message, $key, $user_login, $user_data ) {
    // Create new message
    $msg  = __( 'Hello!', 'personalize-login' ) . "\r\n\r\n";
    $msg .= sprintf( __( 'You asked us to reset your password for %s.', 'personalize-login' ), $user_login ) . "\r\n\r\n";
    $msg .= __( "If you didn't request a password reset, just ignore this email and nothing will happen.", 'personalize-login' ) . "\r\n\r\n";
    $msg .= __( 'To reset your password, visit the following address:', 'personalize-login' ) . "\r\n";
    $msg .= site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . "\r\n\r\n";
    $msg .= __( 'Thanks!', 'personalize-login' ) . "\r\n";
    //$msg .= '<img src="' . site_url() . '/wp-content/uploads/2018/04/Ovacho-Logo-4-TRANS-PNG-CROP-LOGO-p-1080.png">';
    $msg .= __(get_bloginfo('name'), 'personalize-login' ) . "\r\n"; 

    //echo $key;

    return $msg;
}

add_filter('retrieve_password_title', 'replace_retrieve_password_title', 10, 1);
function replace_retrieve_password_title($title) {
    $o_title = __( 'Password reset for ' . get_bloginfo('name'), 'personalize-retrieve-title' );
    return $o_title;
}

add_action( 'login_form_rp', 'redirect_to_custom_password_reset');
add_action( 'login_form_resetpass', 'redirect_to_custom_password_reset');

function redirect_to_custom_password_reset() {
    if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
        // Verify key / login combo
        $user = check_password_reset_key( $_REQUEST['key'], $_REQUEST['login'] );
        if ( ! $user || is_wp_error( $user ) ) {
            if ( $user && $user->get_error_code() === 'expired_key' ) {
                wp_redirect( site_url( 'login/?login=expiredkey' ) );
            } else {
                wp_redirect( site_url( 'login/?login=invalidkey' ) );
            }
            exit;
        }
 
        $redirect_url = site_url( 'reset-password' );
        $redirect_url = add_query_arg( 'login', esc_attr( $_REQUEST['login'] ), $redirect_url );
        $redirect_url = add_query_arg( 'key', esc_attr( $_REQUEST['key'] ), $redirect_url );
        $redirect_url = add_query_arg( 'form', 'newpass', $redirect_url );
 
        wp_redirect( $redirect_url );
        exit;
    }
}

add_action( 'login_form_rp', 'do_password_reset');
add_action( 'login_form_resetpass', 'do_password_reset');

function do_password_reset() {
    if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
        $rp_key = $_REQUEST['rp_key'];
        $rp_login = $_REQUEST['rp_login'];
 
        $user = check_password_reset_key( $rp_key, $rp_login );
 
        if ( ! $user || is_wp_error( $user ) ) {
            if ( $user && $user->get_error_code() === 'expired_key' ) {
                wp_redirect( site_url( 'login/?login=expiredkey' ) );
            } else {
                wp_redirect( site_url( 'login/?login=invalidkey' ) );
            }
            exit;
        }
 
        if ( isset( $_POST['pass1'] ) ) {
            if ( $_POST['pass1'] != $_POST['pass2'] ) {
                // Passwords don't match
                $redirect_url = site_url( 'reset-password' );
 
                $redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
                $redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
                $redirect_url = add_query_arg( 'error', 'password_reset_mismatch', $redirect_url );
                $redirect_url = add_query_arg( 'form', 'newpass', $redirect_url );
 
                wp_redirect( $redirect_url );
                exit;
            }
 
            if ( empty( $_POST['pass1'] ) ) {
                // Password is empty
                $redirect_url = site_url( 'reset-password' );
 
                $redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
                $redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
                $redirect_url = add_query_arg( 'error', 'password_reset_empty', $redirect_url );
                $redirect_url = add_query_arg( 'form', 'newpass', $redirect_url );
 
                wp_redirect( $redirect_url );
                exit;
            }
 
            // Parameter checks OK, reset password
            reset_password( $user, $_POST['pass1'] );
            wp_redirect( site_url( 'login?password=changed' ) );
        } else {
            echo "Invalid request.";
        }
 
        exit;
    }
}

add_filter( 'wp_new_user_notification_email', 'custom_wp_new_user_notification_email', 10, 3 );

function custom_wp_new_user_notification_email( $wp_new_user_notification_email, $user, $blogname ) {
    $wp_new_user_notification_email['subject'] = sprintf('Welcome to %s!', $blogname);
    $wp_new_user_notification_email['message'] = sprintf( "Hi %s,\r\nThank you for joining us at %s! We're glad to have you in our community of dedicated traders and committed analysts. If you have any questions, please feel free to contact us on any of our platforms below or email us at ovachoinvestments@gmail.com.\r\n\r\nUsername: %s\r\nEmail: %s\r\n\r\nJoin us on Social Media!\r\nFacebook: " . FACEBOOK_URL . "\r\nStockTwits: " . STOCKTWITS_URL . "\r\nDiscord: https://discord.gg/hKKTKKe\r\n\r\nDisclaimer: " . DISCLAIMER_URL . "\r\n\r\n- %s", $user->first_name, $blogname, $user->user_login, $user->user_email, $blogname );
    return $wp_new_user_notification_email;

    /*
Hi <name>,
Thank you for joining us at Ovacho Investments! We’re glad to have you in our community of dedicated traders and committed analysts. If you have any questions, please feel free to contact us on any of our platforms or email us at ovachoinvestments@gmail.com

Username: <username>
Email: <email>

Join us on Social Media!
Facebook: https://www.facebook.com/groups/OvachoInvestments/
StockTwits: https://stocktwits.com/OvachoInvestments/
Discord: https://discord.gg/hKKTKKe

Disclaimer: https://docs.google.com/document/d/1Y4Wc7x-l9d1hCRKGumCRwUZfD8g5ePuwe1W_psLkB2c/edit?usp=sharing


    */
}

?>