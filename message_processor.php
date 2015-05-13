<?php

    /**
     * @author: Xingda Wang
     * @date: 13th / May / 2015
     * @version 1.0
     */
    
    $user_id = $_POST['userId'];
    $currency_from = $_POST['currencyFrom'];
    $currency_to = $_POST['currencyTo'];
    $amountSell = $_POST['amountSell'];
    $amountBuy = $_POST['amountBuy'];
    $rate = $_POST['rate'];
    $time_placed = $_POST['timePlaced'];
    $originating_country = $_POST['originatingCountry'];
    $json_data = "";
    
    $second = 200;
    $second_limit = 2;
    $last_api_request = $this->get_last_api_request(); // get from the DB
    $last_api_diff = time() - $last_api_request;
    $second_throttle = $this->get_throttle_second(); // get from the DB
    
    // get_last_api_request() and get_throttle_second are all functions that can lookup
    // from the database and give the correspoing value
    
    /**
     * Get new throttle number in one second
     * @param float $second_limit transactions allowed per second
     * @param float $second execution time for new transactions
     * @param int $second_throttle throttle for transactions per second
     * @param int $last_api_diff api request time difference between last's and current's
     * @return float $new_second_throttle waiting time of the current transactions
     *
     */
    function getWaitingTime($second_limit, $second, $second_throttle, $last_api_diff) {
        if(is_null($second_limit)){
            $new_second_throttle = 0;
        } else {
            $new_second_throttle = $second_throttle - $last_api_diff;
            $new_second_throttle = $new_second_throttle < 0 ? 0 : $new_second_throttle;
            $new_second_throttle += $second / $second_limit;
        }
        return $new_second_throttle;
    }
    
    /**
     * If the transaction is beyond limit, the new comer trasactions need to be waited
     * @param string $user_id user id
     * @param string $currency_from country where the currency is from
     * @param string $currency_to country where the currency is to
     * @param float $amountSell amount of the money that is sold
     * @param float $amountBuy amount of the monney that wants to be bought
     * @param timestamp $time_placed the placed time
     * @param string $originating_country originating country
     * @return string array $json_data generated json array
     */
    function doTransaction($second, $new_second_throttle){
        if($new_second_throttle > $second) {
            sleep($new_second_throttle - $second);
        }
    }

    
    // Save the values back to the database
    $this->save_last_api_request( time() ); // Save current api request time
    $this->save_throttle_minute( $new_second_throttle );    // Save the new throttle in to DB

    // save_last_api_request() and save_throttle_minute are all functions that save the current oprations
    // into the database.
    
    
    // Test data
    # $second = 200;
    #$second_limit = 2; # If the $second_limit changes to 1, it will beyond throttle, that is to say, wait.
    #$last_api_request = 50;
    #$last_api_diff = 100;
    #$second_throttle = 200;
    
    $new_second_throttle = getWaitingTime($second_limit, $second, $second_throttle, $last_api_diff);
    $json_data = doTransaction($second, $new_second_throttle);

    $array = Array(
                   'user_id' => $user_id,
                   'currency_from'=>$currency_from,
                   'currency_to' => $currency_to,
                    'amount_sell' =>$amountSell,
                    'amount_buy' => $amountBuy,
                    'rate' => $rate,
                    'time_placed' => $time_placed,
                    'originating_country' => $originating_country
                    );
    
    //die(json_encode($array));

    // Posted to an url
    $url="http://10.211.55.5/old/message_frontend.php";
  
    $content = json_encode($array);
    
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER,
            array("Content-type: application/json"));
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
    
    $json_response = curl_exec($curl);
    curl_close($curl);
    
?>