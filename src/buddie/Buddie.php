<?php
namespace Buddie\Buddie;
use Buddie\Buddie\RetweetBot;
use Buddie\Buddie\ReviewBot;
use Buddie\Service\Config;
use Buddie\Service\Logger;
/**
 * 
 */
class Buddie {
  public $username = "Buddie";
  public $bot = "Buddie";
  
  /**
   * 
  */
  public function __construct()
  {
    $this->pause = false;
    $this->quit = false;
    $this->logger = new Logger();
    $Config = new Config();
    $Config->load($this->username);
    $this->commands = json_decode(json_encode( $Config->get("commands")), true);
     
    $this->logger->output(json_encode($this->commands));
  }
  
  public function retweet(){
    if ($this->pause) {
      $this->logger->output("Please fix retweet errors and restart.");
      return false;
    }
    if(!(new RetweetBot($this->username))->run()){
      $this->pause = true;
    } 
  }
  
  private function tweet(){
    # TODO: search RSS and tweet
  }
  
  public function reviews(){
    if ($this->pause) {
      $this->logger->output("Please fix errors and restart.");
      return false;
    }
    if(!(new ReviewBot($this->username))->run()){
      $this->pause = true;
    } 
  }
  
  function listen(){
    $this->logger->output("$this->bot: ");
    $command = trim(fread(STDIN, 80));
    $this->command($command);
  } 
  
  public function run(){
    if (!is_cli()) {
      $this->logger->output("This method can only be called from the command line") ;
      return;
    }
    while(!$this->quit){
      $this->listen();
    } 
  } 
  
  function command($command){
    switch ($command) {
      case 'reviews':
        $this->logger->output("fetching reviews...");
        $this->reviews();
        break;
        
      case 'tweet':
        $this->logger->output("Tweeting...");
        $this->tweet();
        break;
      
      case 'retweet':
        $this->logger->output("Initializing retweetbot...");
        $this->tweet();
        break;
      
      case 'quit':
        $this->logger->output("shutting down $this->bot...");
        $this->quit = true;
        break;
      
      default:
        if(isset($this->commands[$command])) {
          $this->logger->output("Command deprecated or temporary down");
        } 
        $this->logger->output("Unknown command. enter 'help' for more info.");                 
        break;
    }
  } 
  
}