<?php 

require "trello-api.php";

//trell0 api authentication
$key = '*************************';
$token = '********************************************';
$trello = new trello_api($key, $token);


//get all the boards from trello 
$allboards = $trello->request('GET', ('member/me/boards'));

//set a default start date and end date
$startDefaultDate = false;
$endDefaultDate = false;

$output = new stdClass();

$taskId = new stdClass();
$taskName = new stdClass();
$resource = new stdClass();
$startDate = new stdClass();
$endDate = new stdClass();
$duration = new stdClass();
$percentage = new stdClass();
$dependencies = new stdClass();

$taskId->id = "task_id";
$taskId->label = "Task ID";
$taskId->type = "string";

$taskName->id = "task_name";
$taskName->label = "Task Name";
$taskName->type = "string";

$resource->id = "resource";
$resource->label = "Resource";
$resource->type = "string";

$startDate->id = "start_date";
$startDate->label = "Start Date";
$startDate->type = "date";

$endDate->id = "end_date";
$endDate->label = "End Date";
$endDate->type = "date";

$duration->id = "duration";
$duration->label = "Duration";
$duration->type = "number";

$percentage->id = "percentage";
$percentage->label = "Percentage";
$percentage->type = "number";

$dependencies->id = "dependencies";
$dependencies->label = "Dependencies";
$dependencies->type = "string";

$output->boards = array();

foreach ($allboards as $singleboard) {

  //get a board ID from each board
  $boardId = $singleboard->id;

  // get all cards from each board
  $allcard = $trello->request('GET', ('boards/'.$boardId.'/cards')); 

  //get board name and total number of cards from each board
  $someboardname = $singleboard->name;
  $boardcardcount = count($allcard);

  $board = new stdClass();

  $board->name = $someboardname;
  $board->number_of_cards = $boardcardcount;

  $board->cols = array(
    $taskId,
    $taskName,
    $resource,
    $startDate,
    $endDate,
    $duration,
    $percentage,
    $dependencies
  );

  $board->rows = array();

  foreach ($allcard as $cardIndex => $row) {

    //get start date and end date from each card elegrant field 
    $cardDesc = str_replace("---
[Elegantt data. What's this?](http://bit.ly/elegantt-for-trello-whats-this)
[](Elegantt_data:dont_delete", "", $row->desc);

    $b = str_replace(")", "", $cardDesc);
    $b = json_decode($b, true);

    
    if (isset($b['psd']))  {
      $startTime = substr($b['psd'],0,strpos($b['psd'], 'T'));

      if($startDefaultDate === false){
        $startDefaultDate = $startTime;
      } else {
        if(strtotime($startTime) < strtotime($startDefaultDate)){ 
          $startDefaultDate = $startTime;
        }
      } 
    }
    

    if (isset($b['ped']))  {
      $endTime = substr($b['ped'],0,strpos($b['ped'], 'T'));
        
      if($endDefaultDate === false){
        $endDefaultDate = $endTime;
      } else {
        if(strtotime($endTime) > strtotime($endDefaultDate)){ 
          $endDefaultDate = $endTime;
        }
      } 
    }
    
    

    $outputRow = new stdClass();

    $outputRow->c = array();

    $cardTaskID = new stdClass();
    $cardTaskID->v = 'task_' . $cardIndex;
    $outputRow->c[] = $cardTaskID;

    $cardTaskName = new stdClass();
    $cardTaskName->v = $row->name;
    $outputRow->c[] = $cardTaskName;

    $cardResource = new stdClass();
    $cardResource->v = !empty($row->labels) ? $row->labels[0]->name : null;
    $outputRow->c[] = $cardResource;

    $cardStartTime = new stdClass();
    $cardStartTime->v = isset($startTime) ? 'Date(' . str_replace('-', ',', $startTime) . ')' : null;
    $outputRow->c[] = $cardStartTime;

    $cardEndTime = new stdClass();
    $cardEndTime->v = isset($endTime) ? 'Date(' . str_replace('-', ',', $endTime) . ')' : null;
    $outputRow->c[] = $cardEndTime;

    $cardDuration = new stdClass();
    $cardDuration->v = null;
    $outputRow->c[] = $cardDuration;

    $cardPercentage = new stdClass();
    $cardPercentage->v = 100;
    $outputRow->c[] = $cardPercentage;

    $cardDependency = new stdClass();
    $cardDependency->v = null;
    $outputRow->c[] = $cardDependency;

    $board->rows[] = $outputRow;
  }

  $output->boards[] = $board;

  
 }

$outputRow = new stdClass();

$outputRow->c = array();

$cardTaskID = new stdClass();
$cardTaskID->v = 'task_999';
$outputRow->c[] = $cardTaskID;

$cardTaskName = new stdClass();
$cardTaskName->v = "dummy card";
$outputRow->c[] = $cardTaskName;

$cardResource = new stdClass();
$cardResource->v = 'Dummy resource';
$outputRow->c[] = $cardResource;

$cardStartTime = new stdClass();
$cardStartTime->v = isset($startDefaultDate) ? 'Date(' . str_replace('-', ',', $startDefaultDate) . ')' : null;
$outputRow->c[] = $cardStartTime;

$cardEndTime = new stdClass();
$cardEndTime->v = isset($endDefaultDate) ? 'Date(' . str_replace('-', ',', $endDefaultDate) . ')' : null;
$outputRow->c[] = $cardEndTime; 

$cardDuration = new stdClass();
$cardDuration->v = null;
$outputRow->c[] = $cardDuration;

$cardPercentage = new stdClass();
$cardPercentage->v = 100;
$outputRow->c[] = $cardPercentage;

$cardDependency = new stdClass();
$cardDependency->v = null;
$outputRow->c[] = $cardDependency;



foreach ($output->boards as $board) {

  $board->rows[] = $outputRow;
}

print json_encode($output);

?>