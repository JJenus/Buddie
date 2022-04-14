<?php
namespace Buddie\Twitter;

use Buddie\Twitter\Ratelimit; 
use Buddie\Twitter\Filter;
/**
 * Search class, search Twitter for given terms
 *
 * @param config:search_max
 * @param config:search_strings
 */
class Search extends Base
{
    /**
     * Search Twitter with given search terms, return tweets
     *
     * @param array $aQuery
     *
     * @return array|false
     */
    public function search($search_str, $apply_filters = true)
    {
      if (!(new Ratelimit($this->oConfig))->check("search")) {
        return false;
      } 

      $oQuery = $this->oConfig->get($search_str);
  		if (empty($oQuery)) {
  			$this->logger->write(2, 'No search strings set');
  			$this->logger->output('No search strings!');
  			return false;
  		}

		  $sortedTweets = array();

      foreach ($oQuery as $i => $oSearch) {
          $sSearchString = $oSearch->search;
          $sMaxId = (!empty($oSearch->max_id) ? $oSearch->max_id : 1);

          $this->logger->output('Searching for max %d tweets with: %s..', $this->oConfig->get('search_max'), $sSearchString);

          $aArgs = array(
              'q'				=> $sSearchString,
              'result_type'	=> 'mixed',
              'count'			=> $this->oConfig->get('search_max'),
              'since_id'  => $sMaxId,
          );
          $oTweets = $this->oTwitter->get('search/tweets', $aArgs);

          if (empty($oTweets->search_metadata)) {
              $this->logger->write(2, sprintf('Twitter API call failed: GET /search/tweets (%s)', $oTweets->errors[0]->message), $aArgs);
              $this->logger->output(sprintf('- Unable to get search results, halting. (%s)', json_encode($oTweets->errors[0]->message)));

              return false;
          }

          if (empty($oTweets->statuses) || count($oTweets->statuses) == 0) {
              $this->logger->output('- No results since last search at %s.', json_encode ($oSearch));
          } else {
              //make sure we parse oldest tweets first
              $sortedTweets = array_merge($sortedTweets, array_reverse($oTweets->statuses));
          }

          //save data for next run
          $this->oConfig->set($search_str, $i, 'max_id', $oTweets->search_metadata->max_id_str);
          $this->oConfig->set($search_str, $i, 'timestamp', date('Y-m-d H:i:s'));
      }

      //filter out unwanted tweets/users
      if ($apply_filters) {
        $sortedTweets = (new Filter($this->oConfig))
          ->setFilters()
          ->filter($sortedTweets);
      }
      
      $this->logger->output('Search complete.');
      
      return $sortedTweets;
    }
}
