<?php

/* Define STDIN in case if it is not already defined by PHP for some reason */
if(!defined("STDIN")) {
  define("STDIN", fopen('php://stdin','rb'));
}

/**
 * 
 */
class Buddie{
 
  /**
   * 
   */
  public function __construct($bot = "Buddie")
  {
    $this->bot = $bot;
    $this->quit = false;
  }
  
  function commands(){
    return [
      "Command" => "Description", 
      "help" => "Display all possible commands.", 
      "quit" => "Stop the bot from running", 
      "run" => "Start fetch operation", 
      "admin" => "Request admin privilege", 
      "save" => "Save last operation", 
    ];
  } 
  
  function save($content=""){
    echo "save to a file ? y/N: ";
    $input = strtolower(trim(fread(STDIN, 80))); 
    if (!empty($input) || $input === "y") {
      echo "Enter file name: \n"; 
      $input = trim(fread(STDIN, 80));
      echo "saving... \n";
      if (file_put_contents(__DIR__.$input, $content)) {
        echo "Saved!\n";
      } else {
        echo "error occured \n";
      }
    } else {
      // code...
    }
    
  } 
  
  function command($command){
    switch ($command) {
      case 'help':
        $str="\n\n";
        for($i=0; $i<40; $i++)
          $str.="_";
        $str .="\n\n";
        foreach ($this->commands() as $key => $value) {
          $str .= "\t $key: $value \n";
        }
        for($i=0; $i<40; $i++)
          $str.="_"; 
        echo $str. "\n\n";
        break;
        
      case 'quit':
        echo "shutting down $this->bot...\n";
        echo "done\n\n";
        $this->quit = true;
        break;
      
      default:
        echo 
         "Unknown command. enter 'help' for more info. \n\n";
        break;
    }
  
  } 
  
  function listen(){
    echo "$this->bot: ";
    $command = trim(fread(STDIN, 80));
    $this->command($command);
  } 
  
  function run(){
    echo "Hello! I'm $this->bot \n";
    sleep(1);
    echo "Do you have a username? (enter Y/n): \n";
    $command = strtolower(trim(fread(STDIN, 80)));
    
    if (empty($command) || $command==="y") {
      sleep(1);
      echo "Please enter your username, start with an alphabet. I am not yet smart enough to detect a wrong username, \nso I'll need your help on this.\n";                   
      sleep(0.5);
      $tries = 0;
      do{
        $tries++;
        echo $tries==2 ? "Please enter username or 'skip' to skip : " : "username: \n"; 
        $this->user = trim(fread(STDIN, 80));
        if ($tries == 2 && "skip"===strtolower($this->user)) {
          $this->user = null;
          break;
        }
      }while(empty($this->user) && $tries < 2);
      echo "Hello $this->user, it's nice to have you here.\n"; 
      sleep(0.5);
    } else {
      echo "Oh I guess you don't have any.\n";
    }
    
    echo "Intializing...\n\n"; 
    sleep(0.5); 
    echo "Give $this->bot a command\n";
    echo "$this->bot is at your service. Enter 'help' to see possible actions.\n\n";
    
    do{
      $this->listen();
    }while(!$this->quit);
  } 
}

(new Buddie())->run();

  