<?php
namespace Buddie\Buddie;

use Buddie\Service\Logger;
use Buddie\Service\Config;
//use Buddie\Twitter\Auth;
use Buddie\Twitter\Ratelimit;
use Buddie\Twitter\Search;
use Buddie\Twitter\Filter;

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

            //check rate limit before anything else
            if ((new Ratelimit($oConfig))->check("search")) {

                //check correct username
                #if ((new Auth($oConfig))->isUserAuthed($this->sUsername)) {

                    //search for new tweets
                    $reviews = (new Search($oConfig))
                        ->search("reviews");
                    //filter out unwanted tweets/users
                    $reviews = (new Filter($oConfig))
                        ->setFilters()
                        ->filter($reviews);

                    if (!empty($reviews)) {
                       return $reviews;
                    }
                    
                    $this->logger->output($reviews);

                    return false;
                #}
            }
        }
    }
}
