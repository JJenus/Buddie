<?php
namespace Buddie\Buddie;

use Buddie\Service\Logger;
use Buddie\Service\Config;
use Buddie\Twitter\Search;


/**
 * Reviews class - generic framework to find trustbuddie reviews.                                  
 *
 * @param config:min_rate_limit
 * @param config:search_strings
 */
class ReviewBot
{
  /**
   * TODO: Update rate limit on each call
  */
    public function __construct($username)
    {
        $this->logger = new Logger;
        $this->sUsername = $username;
    }

    public function run()
    {
        if (empty($this->sUsername)) {
            $this->logger->output("Username not set! \n");
            exit;
        }

        //load config from username.json file
        $oConfig = new Config();
        if ($oConfig->load($this->sUsername)) {
          //search for new tweets
          $reviews = (new Search($oConfig))
              ->search("reviews");

          if (!empty($reviews)) {
             return $reviews;
          }
          
          return false;
        }
    }
}
