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
    $this->reviews = [];
    $this->pause = false;
    $this->quit = false;
    $this->logger = new Logger();
    $Config = new Config();
    $Config->load($this->username);
    $this->commands = json_decode(json_encode( $Config->get("commands")), true);
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
    # not enabled yet
  }
  
  private function strip_hashtags($tweet, $tags){
    if (empty($tags)) {
      return $tweet;
    } 
    foreach ($tags as $tag){
      $_tags[] = "#".$tag->text;
    }
    $_tags[] = "@";
    return str_replace($_tags, '', $tweet);
  }
  
  public function getRating($tweet){
    preg_match("/#rating_\d/", $tweet, $matches);
    if (empty($matches)) {
      return null;
    }
    return explode('_', $matches[0])[1];
  }
  
  public function getBusiness ($tweet){
    preg_match("/(@\w+)/", $tweet, $matches);
    if (empty($matches)) {
      return null;
    }
    return $matches[0];
  }
  
  public function reviews(){
    if ($this->pause) {
      $this->logger->write("Please fix errors and restart.");
      return false;
    }
    if($reviews = (new ReviewBot($this->username))->run()){
      foreach ($reviews as $review){
        $_review = $this->strip_hashtags(
          $review->text, 
          $review->entities->hashtags
        );
        
        $this->reviews[] = [
          "user" => [
            "name" => $review->user->name, 
            "id" => $review->user->id, 
            "screen_name" => $review->user->screen_name, 
            "image" => $review->user->profile_image_url
          ], 
          "review" => [
            "text" => $_review, 
            "rating" => $this->getRating($review->text), 
            "business" => str_replace("@",'', $this->getBusiness($review->text)), 
          ], 
          "tweet" => str_replace("\n", ' ', $review->text),
          "created_at" => date("D, M j, Y H:i:s", strtotime($review->created_at)) 
        ]; 
        
      }
      if (! empty($this->reviews)) {
        return true;
      }
    }else {
      $this->pause = true;
      $this->reviews = "No results found";
    } 
    return false;
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
      case 'show-reviews':
        if(gettype($this->reviews) == "array") {
          foreach($this->reviews as $review){
            print_r($review);
          }  
        }else $this->logger->output($this->reviews);
         
        break;
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
      
      case "help":
        foreach ($this->commands as $key=> $value) {
          // code...
          $this->logger->output("$key: $value");
        }
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