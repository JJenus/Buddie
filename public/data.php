<?php
// Load libSSE via autoloader
require_once '../constants.php';
require_once '../vendor/autoload.php';

use Sse\Event;
use Sse\SSE;
use Buddie\Buddie\Buddie;

/*$buddie = new Buddie();
if($buddie->reviews())
	  echo json_encode($buddie->reviews);
else echo "No data";*/


// A simple time event to push server time to clients 
class ScheduleEvent implements Event {
  public $buddie; 
	public function check(){
	  $this->buddie = new Buddie();
	  if($this->buddie->reviews()){
	    if (!empty($this->buddie->reviews)) {
	      // code...
	      return true;
	    }
	  } 
		// Time always updates, so always return true
		return false;
	}

	public function update(){
		// Send formatted time
		return json_encode($this->buddie->reviews);
	}
}

// Create the SSE handler
$sse = new SSE();

// You can limit how long the SSE handler to save resources 
//$sse->exec_limit = 10;
$sse->sleep_time = 6;

// Add the event handler to the SSE handler
$sse->addEventListener('ScheduleEvent', new ScheduleEvent());

// Kick everything off!
$sse->start();