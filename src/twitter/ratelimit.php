<?php
//TODO: use oConfig class, not parameter
namespace Buddie\Twitter;

/**
 * Check ratelimit of our Twitter API account
 */
class Ratelimit extends Base {
    
    /**
     * Check rate limit, return false if too close
     *
     * @param int $iMinRateLimit
     *
     * @return bool
     */
    public function check($limit=null) {
        //return true;
        $this->calibrate();
        if ($limit === "search") {
          return $this->searchLimits();
        }else if($limit === "blocked"){
          return $this->searchLimits();
        } 
        
        return $this->searchLimits() && $this->blockedLimits(); ;
    }
    
    function calibrate(){
      if ($limits = $this->oConfig->get('rate_limits')) 
      {
        $this->searchLimit = $limits->search;
        $this->blockedLimit = $limits->blocked;
        if (date("Y-m-d h:m:s", $this->searchLimit->reset) <= date("Y-m-d h:m:s", strtotime("now")) ) {
          $this->fetchLimits(); 
        }
      }else{
        if (!$this->fetchLimits()) {
          $this->logger->output("Unable to get limits");
        }
      } 
    }
    
    public function blockedLimits(){
      #check if remaining calls for blocked users is lower than treshold (after reset: 15)
      # (limit - 1) for safety. 
  		if ($this->blockedLimit->remaining <= $this->blockedLimit->limit-1) {
  			$this->logger->write(3, sprintf('Rate limit for GET blocks/ids hit, waiting until %s', date('Y-m-d H:i:s', $this->blockedLimit->reset)));
  			$this->logger->output(sprintf('- Remaining %d/%d calls for blocked users! Aborting search until next reset at %s.',
  				$this->blockedLimit->remaining,
  				$this->blockedLimit->limit,
  				date('Y-m-d H:i:s', $this->blockedLimit->reset)
  			));
  
  			return false;
  		} else {
  			$this->logger->output('- Remaining %d/%d calls (blocked users), next reset at %s.',
  				$this->blockedLimit->remaining,
  				$this->blockedLimit->limit,
  				date('Y-m-d H:i:s', $this->blockedLimit->reset)
  			);
  			$this->blockedLimit->remaining--;
  			return true;
  		}
  		
    }
    
    private function save(){
      $this->oConfig->set(
        "rate_limits", 
  		   [
  		    "search" => $this->searchLimit, 
  		    "blocked" => $this->blockedLimit
  		  ]
  		);
  		$this->logger->output("saved") ;
    } 
    
    public function searchLimits(){
      if ($this->searchLimit->remaining <= 1) {
  			$this->logger->write(3, sprintf('Rate limit for GET search/tweets hit, waiting until %s', date('Y-m-d H:i:s', $this->searchLimit->reset)));
  			$this->logger->output(sprintf('- Remaining %d/%d calls! Aborting search until next reset at %s.',
  				$this->searchLimit->remaining,
  				$this->searchLimit->limit,
  				date('Y-m-d H:i:s', $this->searchLimit->reset)
  			));
  			
  			return false;
  		} else {
  			#$this->logger->output('- Remaining %d/%d calls (search), next reset at %s.', $this->searchLimit->remaining, $this->searchLimit->limit, date('Y-m-d H:i:s', $this->searchLimit->reset));
  		  $this->searchLimit->remaining = intval($this->searchLimit->remaining)-1;
  		  $this->save();
  		  return true;
  		} 
    } 
    
    private function fetchLimits(){
      $this->logger->output('Fetching rate limit status..');
		  $status = $this->oTwitter->get('application/rate_limit_status', array('resources' => 'search,blocks'));
		  #$this->logger->output(json_encode($status) ); 
		  if (empty($status)) {
		    return false;
		  }
		  $this->searchLimit = $status->resources->search->{'/search/tweets'};
		  $this->blockedLimit = $status->resources->blocks->{'/blocks/ids'};
		  
		  return true;
    }
    
    public function __destruct()
    {
       // $this->save();
    }
}
