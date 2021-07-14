<?php
namespace Buddie\Buddie;

use Buddie\Service\Logger;
use Buddie\Service\Config;
//use Buddie\Twitter\Auth;
use Buddie\Twitter\Ratelimit;
use Buddie\Twitter\Search;
use Buddie\Twitter\Filter;
use Buddie\Twitter\Retweet;

/**
 * Retweetbot class - to find and retweet posts based on given search terms
 *
 * Create a json file with ratelimit, time, 
 * update limit_used in rate limit and requests/reset in 15 minutes
 * 
 * @param config:min_rate_limit
 * @param config:search_strings
 */
class RetweetBot
{
    public function __construct($username)
    {
        $this->logger = new Logger;
        $this->sUsername = $username;
    }
    
    public function run()
    {
        if (empty($this->sUsername)) {
            $this->logger->output('Username not set! Halting.');
            exit;
        }
        
        $this->logger->output("Initializing search for reviews... ");

        //load config from username.json file
        $oConfig = new Config();
        if ($oConfig->load($this->sUsername)) {

            //check rate limit before anything else
            if ((new Ratelimit($oConfig))->check()) {

                //check correct username
                #if ((new Auth($oConfig))->isUserAuthed($this->sUsername)) {
                $this->logger->output("Searching... ");

                    //search for new tweets
                    $aTweets = (new Search($oConfig))
                        ->search("search_strings");

                    //filter out unwanted tweets/users
                    $aTweets = (new Filter($oConfig))
                        ->setFilters()
                        ->filter($aTweets);

                    if ($aTweets) {
                        //retweet remaining tweets
                        (new Retweet($oConfig))->post($aTweets);
                        $this->logger->output('done!');
                        return true;
                    }

                    return false;
                #}
            }
        }
    }
}
