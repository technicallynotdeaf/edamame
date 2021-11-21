<?php defined('_JEXEC') or die();

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

abstract class EdamameHelper 
{

  /* Create the DB connection to the joomla database... */
  function getDB() {
   
     $db = JFactory::getDbo();
     
     return $db;

  }
  
  
  function getSuppliers($db) {
  
    $query = $db->getQuery(true);
    $query->select('*');
    $query->from('#__com_edamame_suplliers');

    // echo "\n<br/>Question Lookup Query: " . (string)$query;
    $db->setQuery((string)$query);
    
    $supplier_list = $db->loadObjectList();
    
    return $supplier_list;

  } 


  function getParties($jurisdiction_id = 0, $db) {
             
    $query = $db->getQuery(true);
    $query->select('*');
    if(!($jurisdiction_id == 0)) {
      $query->where($db->quoteName((string)$jurisdiction_id) . " >= " . $db->quote('1'));
    }
    $query->from('#__com_edamame_parties');
    
    // echo "\n<br/>Party Lookup Query: " . (string)$query;
    $db->setQuery((string)$query);
    
    $party_list = $db->loadObjectList();
    
    return $party_list;
    
  }
  
  function getPartyName($party_id, $db) {
        
    if($party_id == 0) {
      return "Unknown political party";
    }
             
    $query = $db->getQuery(true);
    $query->select('party_id, name, acronym');
    $query->where($db->quoteName('party_id') . " = " . $db->quote(strval($party_id)));
    $query->from('#__com_edamame_parties');
    
    // echo "\n<br/>Party Lookup Query: " . (string)$query;
    //$db->setQuery((string)$query);
    $db->setQuery($query);
    
    $party = $db->loadObject();
    
    $namestr = $party->name . " (" . $party->acronym . ")";

    return $namestr;

  }
  
  function getStatements($question_id, $party_id, $jurisdiction_id, $db){
             
    $query = $db->getQuery(true);
    $query->select('*');
    $query->from('#__com_edamame_policystatements');

    // Filter by just statements that are relevant to requested jurisdiction... 
    $query->where($db->quoteName('jurisdiction') . " = " . $db->quote(strval($jurisdiction_id)));
    
    // Filter by just statements that are relevant to requested question... 
    $query->andWhere($db->quoteName('question_id') . " = " . $db->quote(strval($question_id)));
    
    // Filter by just statements that are relevant to requested party... 
    $query->andWhere($db->quoteName('party_id') . " = " . $db->quote(strval($party_id)));
    
    
    //echo "\n<br/>policy statement details list Lookup Query: " . (string)$query;
    $db->setQuery((string)$query);
    
    $statements = $db->loadObjectList();
    
    return $statements;
    
  }
  
  function getVotes($question_id, $party_id, $jurisdiction_id, $db){
             
    $query = $db->getQuery(true);
    $query->select('*');
    $query->from('#__com_edamame_votinghistory');

    // Filter by just statements that are relevant to requested jurisdiction... 
    $query->where($db->quoteName('jurisdiction') . " = " . $db->quote(strval($jurisdiction_id)));
    
    // Filter by just statements that are relevant to requested question... 
    $query->andWhere($db->quoteName('question_id') . " = " . $db->quote(strval($question_id)));
    
    // Filter by just statements that are relevant to requested party... 
    $query->andWhere($db->quoteName('party_id') . " = " . $db->quote(strval($party_id)));
    
    
    //echo "\n<br/>past vote details list lookup Query: " . (string)$query;
    $db->setQuery((string)$query);
    
    $past_votes = $db->loadObjectList();
    
    return $past_votes;
    
  }
  
  
  function addStatement($statement_obj, $db){
			
	// statement_id is a variable where the db engine dumps the 
	// returned key of the new object from the table
    $result = $db->insertObject('#__com_edamame_policystatements', $statement_obj, 'statement_id');	
		
    $statement_id = $statement_obj->row_id;		
     
  }
    
  function updateStatement($question_id, $statement_obj, $db){
     
    echo "<p>Saving statement as requested... </p>";
		
    if(!empty($statement_obj->statement)) {
      echo "statement = " . $statement_obj->statement;
      $result = $db->updateObject('#__com_edamame_policystatements', $statement_obj, 'statement_id');
    }
  
  }
  
  

  function get_position($question_id, $party_id, $jurisdiction_id, $db) {
  
    $score = 0; // let's start with zero
    
    $statement_score = EdamameHelper::getStatementScore($question_id, $party_id, $jurisdiction_id, $db);
    $voting_score =  EdamameHelper::getVotingScore($question_id, $party_id, $jurisdiction_id, $db);
    
    $score = $statement_score + $voting_score;
    
    //echo "\n<br/>Score for Question " . $question_id . ", party " . $party_id;
    //echo " is " . $score;
    
    if($score == 0) {
      $imagename = "no_data.png";
    }
    elseif($score > 0 && $score < 1) {
      $imagename = "maybe_yes.png";
    }
    elseif($score < 0 && $score > -1) {
      $imagename = "maybe_no.png";
    }
    elseif($score <= -1 ) {
      $imagename = "no.png";
    }
    elseif($score >= 1 ) {
      $imagename = "yes.png";
    }
    else{
    	$imagename = "no_data.png";
    }
  
    $image_location = Uri::root() . "/media/com_edamame/images/";

    $imagestr = HTMLHelper::image( $image_location . $imagename , 'No Data');
    
    return $imagestr; 
  }
  
  function getStatementScore($question_id, $party_id, $jurisdiction_id, $db) {
  
    $statement_score = 0;
  
    ## Look up relevant FEDERAL policy statements... Fed jurisdiction code is 9
    ## (jurisdiction code zero means 'undefined')

    $query = $db->getQuery(true);
    $query->select('*');
    
    $jurisd_string = "%" . strval($jurisdiction_id) . "%";
    
    $query->where('party_id = ' . $db->quote($party_id) . 
                   ' and question_id = '.  $db->quote($question_id) .
        ' and ' . $db->quoteName('jurisdiction') . " = " . '9');
    $query->from('#__com_edamame_policystatements');
    
    $db->setQuery((string)$query);
    
    $statements = $db->loadObjectList();
    
    foreach ($statements as $st) {
      $statement_score += $st->score;
    }
  
    ## Look up relevant STATE policy statements... 
    ## (jurisdiction code zero means 'undefined')

    $query = $db->getQuery(true);
    $query->select('*');
    
    $jurisd_string = "%" . strval($jurisdiction_id) . "%";
    
    $query->where('party_id = ' . $db->quote($party_id) . 
                   ' and question_id = '.  $db->quote($question_id) .
        ' and ' . $db->quoteName('jurisdiction') . " = " . strval($jurisdiction_id));
    $query->from('#__com_edamame_policystatements');
    
    $db->setQuery((string)$query);
    
    $statements = $db->loadObjectList();
    
    foreach ($statements as $st) {
      $statement_score += $st->score;
    }
  
    //echo "\n<br>Score based on policy statements (" . EdamameHelper::getStateAbbrev($jurisdiction_id) . " and Federal): " . $statement_score; 
    
    return $statement_score;
  }
  
  function getVotingScore($question_id, $party_id, $jurisdiction_id, $db) {
    
    $voting_score = 0;
  
    ## Look up relevant FEDERAL voting history... Fed jurisdiction code is 9
    ## (jurisdiction code zero means 'undefined')

    $query = $db->getQuery(true);
    $query->select('*');
    $jurisd_string = "%" . strval($jurisdiction_id) . "%";
    $query->where('party_id = ' . $db->quote($party_id) . 
                   ' and question_id = '.  $db->quote($question_id) .
        ' and ' . $db->quoteName('jurisdiction') . " = " . $db->quote((string)9));
    $query->from('#__com_edamame_votinghistory');
    
    
    //echo "\n<br/>Fed Votes Lookup Query (1): " . (string)$query;
    $db->setQuery((string)$query);
    
    $past_votes = $db->loadObjectList();
    
    foreach ($past_votes as $st) {
      $voting_score += $st->score;
    }
    
    ## Look up relevant STATE voting history... 

    $query = $db->getQuery(true);
    $query->select('*');
    $jurisd_string = "%" . strval($jurisdiction_id) . "%";
    $query->where('party_id = ' . $db->quote($party_id) . 
        ' and question_id = '.  $db->quote($question_id) .
        ' and ' . $db->quoteName('jurisdiction') . " = " . $db->quote((string)$jurisdiction_id));
    $query->from('#__com_edamame_votinghistory');
    
    //echo "\n<br/>State Votes Lookup Query (2): " . (string)$query;
    
    $db->setQuery((string)$query);
    
    $statements = $db->loadObjectList();
    
    foreach ($statements as $st) {
      $voting_score += $st->score;
    }
    
    // echo "\n<br/> PArty " . $party_id . " got a voting history score of " . $voting_score;
    
    //echo "\n<br>Score based on past votes (" . EdamameHelper::getStateAbbrev($jurisdiction_id) . " and Federal): " . $voting_score; 
    return $voting_score;
  }


  
}
