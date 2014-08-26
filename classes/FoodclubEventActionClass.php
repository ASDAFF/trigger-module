<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/classes/DoActionClass.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/fevent/classes/general/utils.php");

class CFoodclubEventAction{

    private $_fields = array();
    private $_user = array();
    private $trueEventCounter = 0;

    function __construct() {

    }

    function build($fields){
        self::setFields($fields);
        self::action();
    }

    function setFields($fields){
        if(empty($fields))
            throw new Exception('Нет событий.');

        $this->_fields = $fields;
    }

    function action(){
        foreach($this->_fields["EVENTS"] as $event){
            $bOk = true;
            if(strlen($event["CONDITIONS"]) > 0 ){
                $TriggerList = array();
                $arActionID = array();
                $TriggerActionList = array();
                $TrueEventCounter = 0;$NeedTrueEvent = 0;
                $bOk = false;

                $arConditions = unserialize($event["CONDITIONS"]);
                if(!empty($arConditions["CHILDREN"])){                        
                    $NeedTrueEvent = count($arConditions["CHILDREN"]);                    
                    self::countTrueEvents($arConditions["CHILDREN"]);
                    $TrueEventCounter = self::getTrueEventCounter();                    
                    $bOk = CFoodclubEventCompare::checkLogic($arConditions["DATA"],$TrueEventCounter,$NeedTrueEvent);
                }                
                if($bOk){
                    //Получаем триггеры
                    CFoodclubEventDoAction::checkAction($event);
                }
            }
        }
    }

    function getUserFields(){
        if(empty($this->_user)){
            if($int = CUser::GetID()){
                $rsUser = CUser::GetByID($int);
                if($arUser = $rsUser->Fetch()){
                    return $arUser;
                }else{
                    return array();
                }
            }else{
                return array();
            }
        }else{
            return $this->_user;
        }
    }

    function increas(){
        $this->trueEventCounter += 1;
    }

    function getTrueEventCounter(){
        return $this->trueEventCounter;
    }

    function recursive_array_search($needle,$haystack) {
        foreach($haystack as $key=>$value) {
            $current_key=$key;
            if($needle===$value OR (is_array($value) && self::recursive_array_search($needle,$value) !== false)) {
                return $current_key;
            }
        }
        return false;
    }

    function checkQuantityCond($arConditions) {
        $bOk = false;
        $arCheckValues = array();
        $arCheckQuantityValue = array();
        foreach ($arConditions["CHILDREN"] as $child) {
            if(strlen($child["CLASS_ID"]) > 0 && !empty($child["DATA"])){
                switch ($child["CLASS_ID"]) {
                  case 'CondQuantityValue':
                    $arCheckQuantityValue = $child["DATA"];
                    break;
                  
                  default:
                    $arCheckValues[] = $child;
                    break;
                }
            }
        }

        $trueEventCounter = 0;
        $needTrueEvent = count($arConditions["CHILDREN"]) - 1;        
        if(!empty($arCheckValues) && !empty($arCheckValues)){
            foreach ($arCheckValues as $child) {
                switch($child["CLASS_ID"]){
                    /*case "CondUserGroup":
                    //Проверяем группу пользователя                                        
                    if(CFoodclubEventCompare::compareArrayKey(
                        CUser::GetUserGroupArray(),
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ) && 
                    CFoodclubEventCompare::compare(
                        $arCheckQuantityValue["value"],
                        $arCheckQuantityValue["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                       $trueEventCounter++; 
                    }
                    break;
                    case "CondIBElement":
                    if(CFoodclubEventCompare::compare(
                        $this->_fields["ID"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ) && 
                    CFoodclubEventCompare::compare(
                        $arCheckQuantityValue["value"],
                        $arCheckQuantityValue["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                        $trueEventCounter++;
                    }
                    break;
                    case "CondIBSection":
                    //Проверяем группу пользователя                                        
                    if(CFoodclubEventCompare::compare(
                        $this->_fields["SECTION_ID"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ) && 
                    CFoodclubEventCompare::compare(
                        $arCheckQuantityValue["value"],
                        $arCheckQuantityValue["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                        $trueEventCounter++;
                    }
                    break;
                    case "CondIBIBlock":                      
                    //Проверяем группу пользователя                       
                    if(CFoodclubEventCompare::compare(
                        $this->_fields["IBLOCK_ID"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ) && 
                    CFoodclubEventCompare::compare(
                        $arCheckQuantityValue["value"],
                        $arCheckQuantityValue["logic"],
                        $child["DATA"]["value"]
                    ))
                    {                        
                        $trueEventCounter++;
                    }
                    break;
                    case "CondUser":
                    //Проверяем пользователя
                    if(CFoodclubEventCompare::compare(
                        CUser::GetID(),
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ) && 
                    CFoodclubEventCompare::compare(
                        $arCheckQuantityValue["value"],
                        $arCheckQuantityValue["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                        $trueEventCounter++;
                    }
                    break;*/
                    case "CondIBElementProperty":
                    if(CFoodclubEventCompare::compareConditions(array( "compareArrayKey" => array($this->_fields["PROPERTY_VALUES"],$child["DATA"]["logic"],$child["DATA"]["value"]), "compare" => array($arCheckQuantityValue["value"],$arCheckQuantityValue["logic"],$this->_fields["PROPERTY_VALUES"][ $child["DATA"]["value"] ]) ), $arConditions["DATA"])){
                        $trueEventCounter++;
                    }
                    /*if(CFoodclubEventCompare::compareArrayKey(
                        $this->_fields["PROPERTY_VALUES"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ) && 
                    CFoodclubEventCompare::compare(
                        $arCheckQuantityValue["value"],
                        $arCheckQuantityValue["logic"],
                        $this->_fields["PROPERTY_VALUES"][ $child["DATA"]["value"] ]
                    ))
                    {
                        $trueEventCounter++;
                    }*/
                    break;
                }
            }            
            $bOk = CFoodclubEventCompare::checkLogic($arConditions["DATA"],$trueEventCounter,$needTrueEvent);
        }
        return $bOk;
    }

    function checkChildCond($arConditions){
        $trueEventCounter = 0;
        $needTrueEvent = count($arConditions["CHILDREN"]);
        if(!empty($arConditions["CHILDREN"])){
            foreach ($arConditions["CHILDREN"] as $child) {
                switch($child["CLASS_ID"]){
                    case "CondIBTags":
                    if(CFoodclubEventCompare::compare(
                        $this->_fields["TAGS"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                       $trueEventCounter++; 
                    }
                    break;
                    case "CondIBModifiedBy":
                    if(CFoodclubEventCompare::compare(
                        $this->_fields["MODIFIED_BY"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                       $trueEventCounter++; 
                    }
                    break;
                    case "CondUserDateRegister":
                    $user = self::getUserFields();
                    if($user["DATE_REGISTER"]){
                        if(CFoodclubEventCompare::compareDates(
                            $user["DATE_REGISTER"],
                            $child["DATA"]["logic"],
                            $child["DATA"]["value"]
                        ))
                        {
                           $trueEventCounter++; 
                        }
                    }
                    break;
                    case "CondUserLADate":
                    $user = self::getUserFields();
                    if(CFoodclubEventCompare::compareDates(
                        $user["LAST_ACTIVITY_DATE"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                       $trueEventCounter++; 
                    }
                    break;
                    case "CondIBTimestampX":
                    $date = (isset($this->_fields["TIMESTAMP_X"]) ? $this->_fields["TIMESTAMP_X"] : $this->_fields["~TIMESTAMP_X"]);
                    if(CFoodclubEventCompare::compareDates(
                        $date,
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                       $trueEventCounter++; 
                    }
                    break;
                    case "CondIBCreatedBy":
                    if(CFoodclubEventCompare::compare(
                        $this->_fields["CREATED_BY"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                       $trueEventCounter++; 
                    }
                    break;
                    case "CondIBDateCreate":
                    $date = (isset($this->_fields["DATE_CREATE"]) ? $this->_fields["DATE_CREATE"] : $this->_fields["~DATE_CREATE"]);
                    if(CFoodclubEventCompare::compareDates(
                        $date,
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                       $trueEventCounter++; 
                    }
                    break;
                    case "CondIBDetailText":
                    if(CFoodclubEventCompare::compare(
                        $this->_fields["DETAIL_TEXT"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                       $trueEventCounter++; 
                    }
                    break;
                    case "CondIBPreviewText":
                    if(CFoodclubEventCompare::compare(
                        $this->_fields["PREVIEW_TEXT"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                       $trueEventCounter++; 
                    }
                    break;
                    case "CondIBSort":
                    if(CFoodclubEventCompare::compare(
                        $this->_fields["SORT"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                       $trueEventCounter++; 
                    }
                    break;
                    case "CondIBDateActiveTo":
                    if(CFoodclubEventCompare::compareDates(
                        $this->_fields["ACTIVE_TO"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                       $trueEventCounter++; 
                    }
                    break;
                    case "CondIBDateActiveFrom":
                    if(CFoodclubEventCompare::compareDates(
                        $this->_fields["ACTIVE_FROM"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                       $trueEventCounter++; 
                    }
                    break;
                    case "CondIBActive":
                    if(CFoodclubEventCompare::compare(
                        $this->_fields["ACTIVE"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                       $trueEventCounter++; 
                    }
                    break;
                    case "CondIBName":
                    if(CFoodclubEventCompare::compare(
                        $this->_fields["NAME"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                       $trueEventCounter++; 
                    }
                    break;
                    case "CondIBXmlID":
                    if(CFoodclubEventCompare::compare(
                        $this->_fields["XML_ID"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                       $trueEventCounter++; 
                    }
                    break;
                    case "CondIBCode":
                    if(CFoodclubEventCompare::compare(
                        $this->_fields["CODE"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                       $trueEventCounter++; 
                    }
                    break;
                    case "CondUserGroup":
                    //Проверяем группу пользователя                                        
                    if(CFoodclubEventCompare::compareArrayValue(
                        CUser::GetUserGroupArray(),
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                       $trueEventCounter++; 
                    }
                    break;
                    case "CondIBElement":
                    if(CFoodclubEventCompare::compare(
                        $this->_fields["ID"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                        $trueEventCounter++;
                    }
                    break;
                    case "CondIBSection":
                    //Проверяем группу пользователя                                        
                    if(CFoodclubEventCompare::compare(
                        $this->_fields["SECTION_ID"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                        $trueEventCounter++;
                    }
                    break;
                    case "CondIBIBlock":                      
                    //Проверяем группу пользователя                       
                    if(CFoodclubEventCompare::compare(
                        $this->_fields["IBLOCK_ID"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {                        
                        $trueEventCounter++;
                    }
                    break;
                    case "CondUser":
                    //Проверяем пользователя
                    if(CFoodclubEventCompare::compare(
                        CUser::GetID(),
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                        $trueEventCounter++;
                    }
                    break;
                    case "CondIBElementProperty":
                    //Проверяем пользователя
                    if(CFoodclubEventCompare::compare(
                        $this->_fields["PROPERTY_VALUES"],
                        $child["DATA"]["logic"],
                        $child["DATA"]["value"]
                    ))
                    {
                        $trueEventCounter++;
                    }
                    break;
                }
            }

            $bOk = CFoodclubEventCompare::checkLogic($arConditions["DATA"],$trueEventCounter,$needTrueEvent);            
        }
        return $bOk;
    }

    //count true event tree elements
    function countTrueEvents($arConditions){
        //echo "<pre>";print_r($this->_fields);echo "</pre>";
        foreach($arConditions as $child){          
              if(strlen($child["CLASS_ID"]) > 0 && !empty($child["DATA"])){
                  switch($child["CLASS_ID"]){
                      case "CondIBTags":
                      if(CFoodclubEventCompare::compare(
                          $this->_fields["TAGS"],
                          $child["DATA"]["logic"],
                          $child["DATA"]["value"]
                      ))
                      {
                          self::increas();
                      }
                      break;
                      case "CondIBModifiedBy":
                      if(CFoodclubEventCompare::compare(
                          $this->_fields["MODIFIED_BY"],
                          $child["DATA"]["logic"],
                          $child["DATA"]["value"]
                      ))
                      {
                          self::increas();
                      }
                      break;
                      case "CondUserDateRegister":
                      $user = self::getUserFields();
                      if($user["DATE_REGISTER"]){
                          if(CFoodclubEventCompare::compareDates(
                              $user["DATE_REGISTER"],
                              $child["DATA"]["logic"],
                              $child["DATA"]["value"]
                          ))
                          {
                             self::increas();
                          }
                      }
                      break;
                      case "CondUserLADate":
                      $user = self::getUserFields();
                      if($user["LAST_ACTIVITY_DATE"]){
                          if(CFoodclubEventCompare::compareDates(
                              $user["LAST_ACTIVITY_DATE"],
                              $child["DATA"]["logic"],
                              $child["DATA"]["value"]
                          ))
                          {
                             self::increas();
                          }
                      }
                      break;
                      case "CondIBTimestampX":
                      $date = (isset($this->_fields["TIMESTAMP_X"]) ? $this->_fields["TIMESTAMP_X"] : $this->_fields["~TIMESTAMP_X"]);
                      if(CFoodclubEventCompare::compareDates(
                          $date,
                          $child["DATA"]["logic"],
                          $child["DATA"]["value"]
                      ))
                      {
                          self::increas();
                      }
                      break;
                      case "CondIBCreatedBy":
                      if(CFoodclubEventCompare::compare(
                          $this->_fields["CREATED_BY"],
                          $child["DATA"]["logic"],
                          $child["DATA"]["value"]
                      ))
                      {
                          self::increas();
                      }
                      break;
                      case "CondIBDateCreate":
                      $date = (isset($this->_fields["DATE_CREATE"]) ? $this->_fields["DATE_CREATE"] : $this->_fields["~DATE_CREATE"]);
                      if(CFoodclubEventCompare::compareDates(
                          $date,
                          $child["DATA"]["logic"],
                          $child["DATA"]["value"]
                      ))
                      {
                          self::increas();
                      }
                      break;
                      case "CondIBDetailText":
                      if(CFoodclubEventCompare::compare(
                          $this->_fields["DETAIL_TEXT"],
                          $child["DATA"]["logic"],
                          $child["DATA"]["value"]
                      ))
                      {
                          self::increas();
                      }
                      break;
                      case "CondIBPreviewText":
                      if(CFoodclubEventCompare::compare(
                          $this->_fields["PREVIEW_TEXT"],
                          $child["DATA"]["logic"],
                          $child["DATA"]["value"]
                      ))
                      {
                          self::increas();
                      }
                      break;
                      case "CondIBSort":
                      if(CFoodclubEventCompare::compare(
                          $this->_fields["SORT"],
                          $child["DATA"]["logic"],
                          $child["DATA"]["value"]
                      ))
                      {
                          self::increas();
                      }
                      break;
                      case "CondIBDateActiveTo":
                      if(CFoodclubEventCompare::compareDates(
                          $this->_fields["ACTIVE_TO"],
                          $child["DATA"]["logic"],
                          $child["DATA"]["value"]
                      ))
                      {
                          self::increas();
                      }
                      break;
                      case "CondIBDateActiveFrom":
                      if(CFoodclubEventCompare::compareDates(
                          $this->_fields["ACTIVE_FROM"],
                          $child["DATA"]["logic"],
                          $child["DATA"]["value"]
                      ))
                      {
                          self::increas();
                      }
                      break;
                      case "CondIBActive":
                      if(CFoodclubEventCompare::compare(
                          $this->_fields["ACTIVE"],
                          $child["DATA"]["logic"],
                          $child["DATA"]["value"]
                      ))
                      {
                          self::increas();
                      }
                      break;
                      case "CondIBName":                      
                      if(CFoodclubEventCompare::compare(
                          $this->_fields["NAME"],
                          $child["DATA"]["logic"],
                          $child["DATA"]["value"]
                      ))
                      {
                          self::increas();
                      }
                      break;
                      case "CondIBXmlID":
                      if(CFoodclubEventCompare::compare(
                          $this->_fields["XML_ID"],
                          $child["DATA"]["logic"],
                          $child["DATA"]["value"]
                      ))
                      {
                          self::increas();
                      }
                      break;
                      case "CondIBCode":
                      if(CFoodclubEventCompare::compare(
                          $this->_fields["CODE"],
                          $child["DATA"]["logic"],
                          $child["DATA"]["value"]
                      ))
                      {
                          self::increas();
                      }
                      break;
                      case "CondGroup":
                      //Проверяем группу условий
                      if(CFoodclubEventUtils::recursive_array_search("CondQuantityValue",$child["CHILDREN"]) !== false){
                          //check child event elements with property value
                          if(self::checkQuantityCond($child)){
                              self::increas();
                          }
                      }else{
                          //check child event elements
                          if(self::checkChildCond($child)){
                              self::increas();
                          }
                      }
                      break;
                      case "CondUserGroup":
                      //Проверяем группу пользователя                                        
                      if(CFoodclubEventCompare::compareArrayValue(
                          CUser::GetUserGroupArray(),
                          $child["DATA"]["logic"],
                          $child["DATA"]["value"]
                      ))
                      {
                          self::increas();
                      }
                      break;
                      case "CondIBElement":
                      if(CFoodclubEventCompare::compare(
                          $this->_fields["ID"],
                          $child["DATA"]["logic"],
                          $child["DATA"]["value"]
                      ))
                      {
                          self::increas();
                      }
                      break;
                      case "CondIBSection":
                      //Проверяем группу пользователя                                        
                      if(CFoodclubEventCompare::compare(
                          $this->_fields["SECTION_ID"],
                          $child["DATA"]["logic"],
                          $child["DATA"]["value"]
                      ))
                      {
                          self::increas();
                      }
                      break;
                      case "CondIBIBlock":                      
                      //Проверяем группу пользователя                       
                      if(CFoodclubEventCompare::compare(
                          $this->_fields["IBLOCK_ID"],
                          $child["DATA"]["logic"],
                          $child["DATA"]["value"]
                      ))
                      {                        
                          self::increas();
                      }
                      break;
                      case "CondUser":
                      //Проверяем пользователя
                      if(CFoodclubEventCompare::compare(
                          CUser::GetID(),
                          $child["DATA"]["logic"],
                          $child["DATA"]["value"]
                      ))
                      {
                          self::increas();
                      }
                      break;
                  }
             }
        }        
    }
}

/*abstract class AbstractPageBuilder {
    abstract function getPage();
}

abstract class AbstractPageDirector {
    abstract function __construct(AbstractPageBuilder $builder_in);
    abstract function buildPage();
    abstract function getPage();
}

class Event {
    private $page = NULL;
    private $page_title = NULL;
    private $page_heading = NULL;
    private $page_text = NULL;

    private $arFields = array();

    function __construct() {
    }
    function showPage() {
      return $this->page;
    }
    function setTitle($title_in) {
      $this->page_title = $title_in;
    }

    function setFields($arFields){
      $this->arFields = $arFields;
    }

    function setHeading($heading_in) {
      $this->page_heading = $heading_in;
    }
    function setText($text_in) {
      $this->page_text .= $text_in;
    }
    function formatPage() {
       $this->page  = '<html>';
       $this->page .= '<head><title>'.$this->page_title.'</title></head>';
       $this->page .= '<body>';
       $this->page .= '<h1>'.$this->page_heading.'</h1>';
       $this->page .= $this->page_text;
       $this->page .= '</body>';
       $this->page .= '</html>';
    }
}

class EventBuilder extends AbstractPageBuilder {
    private $event = NULL;
    function __construct() {
      $this->event = new Event();
    }

    function setFields($arFields) {
      $this->event->setFields($arFields);
    }

    function setTitle($title_in) {
      $this->page->setTitle($title_in);
    }
    function setHeading($heading_in) {
      $this->page->setHeading($heading_in);
    }
    function setText($text_in) {
      $this->page->setText($text_in);
    }
    function formatPage() {
      $this->page->formatPage();
    }
    function getPage() {
      return $this->page;
    }
}

class EventDirector extends AbstractPageDirector {
    private $builder = NULL;
    public function __construct(AbstractPageBuilder $builder_in) {
         $this->builder = $builder_in;
    }
    public function buildEvent() {

      if(!empty($arFields["EVENTS"])){
          
          foreach($arFields["EVENTS"] as $event){
              $bOk = true;
              
              if(strlen($event["CONDITIONS"]) > 0 ){
                  $TriggerList = array();
                  $arActionID = array();
                  $TriggerActionList = array();
                  $TrueEventCounter = 0;$NeedTrueEvent = 0;
                  $bOk = false;

                  $arConditions = unserialize($event["CONDITIONS"]);
                  if(!empty($arConditions["CHILDREN"])){                        
                      $NeedTrueEvent = count($arConditions["CHILDREN"]);
                      CFoodclubEventDoAction::countTrueEvents($arConditions["CHILDREN"]);
                      $do = new CFoodclubEventDoAction;
                      $TrueEventCounter = $do->getTrueEventCounter();

                      $bOk = CFoodclubEventDoAction::checkLogic($arConditions["DATA"],$TrueEventCounter,$NeedTrueEvent);                        
                  }

                  if($bOk){
                      //Получаем триггеры
                      CFoodclubEventDoAction::checkAction($event);
                  }
              }
          }
      }

      $this->builder->setTitle('Testing the HTMLPage');
      $this->builder->setHeading('Testing the HTMLPage');
      $this->builder->setText('Testing, testing, testing!');
      $this->builder->setText('Testing, testing, testing, or!');
      $this->builder->setText('Testing, testing, testing, more!');
      $this->builder->formatPage();
    }
    public function getPage() {
      return $this->builder->getPage();
    }
}



class Event {
 
    private $_fields = array();    
 
    public function setFields($fields) {
        if(empty($fields))
          throw new Exception('Нет событий.');

        $this->_fields = $fields;
    }

    public function buildEvents(){
        foreach($this->_fields["EVENTS"] as $event){
            $bOk = true;
            
            if(strlen($event["CONDITIONS"]) > 0 ){
                $TriggerList = array();
                $arActionID = array();
                $TriggerActionList = array();
                $TrueEventCounter = 0;$NeedTrueEvent = 0;
                $bOk = false;

                $arConditions = unserialize($event["CONDITIONS"]);
                if(!empty($arConditions["CHILDREN"])){                        
                    $NeedTrueEvent = count($arConditions["CHILDREN"]);
                    CFoodclubEventDoAction::countTrueEvents($arConditions["CHILDREN"]);
                    $do = new CFoodclubEventDoAction;
                    $TrueEventCounter = $do->getTrueEventCounter();

                    $bOk = CFoodclubEventDoAction::checkLogic($arConditions["DATA"],$TrueEventCounter,$NeedTrueEvent);                        
                }

                if($bOk){
                    //Получаем триггеры
                    CFoodclubEventDoAction::checkAction($event);
                }
            }
        }
    }
}
 

abstract class BuilderEvent {
 
    protected $_fields;
 
    public function getEvents() {
        return $this->_fields;
    }

    public function createNewEvent() {
        $this->_fields = new Event ();
    }
 
    abstract public function buildFields($fields);
    abstract public function buildEvents();
}
 

class BuilderEventIblockElement extends BuilderEvent {
 
    public function buildFields($fields) {
        $this->_fields->setFields ( $fields );
    }

    public function buildEvents() {
        $this->_fields->buildEvents ();
    }
}



case "CondUserGroup":
//Проверяем группу пользователя                                        
if(CFoodclubEventCompare::compareArray(
    CUser::GetUserGroupArray(),
    $child["DATA"]["logic"],
    $child["DATA"]["value"]
))
{
    self::increas();
}
break;
case "CondIBElement":
if(CFoodclubEventCompare::compare(
    $arFields["ID"],
    $child["DATA"]["logic"],
    $child["DATA"]["value"]
))
{
    self::increas();
}
break;
case "CondIBSection":
//Проверяем группу пользователя                                        
if(CFoodclubEventCompare::compare(
    $arFields["SECTION_ID"],
    $child["DATA"]["logic"],
    $child["DATA"]["value"]
))
{
    self::increas();
}
break;
case "CondIBIBlock":
//Проверяем группу пользователя                                        
if(CFoodclubEventCompare::compare(
    $arFields["IBLOCK_ID"],
    $child["DATA"]["logic"],
    $child["DATA"]["value"]
))
{
    self::increas();
}
break;
case "CondUser":
 

class BuilderPizzaSpicy extends BuilderPizza {
 
    public function buildPastry() {
        $this->_pizza->setPastry ( "puff" );
    }
    public function buildSauce() {
        $this->_pizza->setSauce ( "hot" );
    }
    public function buildGarniture() {
        $this->_pizza->setGarniture ( "pepperoni+salami" );
    }
 
}

class PizzaBuilder {
    private $_builderPizza;
 
    public function setBuilderPizza(BuilderPizza $mp)
    {
        $this->_builderPizza = $mp;
    }
    public function getPizza()
    {
        return $this->_builderPizza->getPizza();
    }
    public function constructPizza() {
        $this->_builderPizza->createNewPizza ();
        $this->_builderPizza->buildPastry ();
        $this->_builderPizza->buildSauce ();
        $this->_builderPizza->buildGarniture ();
    }
}

// Инициализация разносчика
$pizzaBuilder = new PizzaBuilder();
 
// Инициализация доступных продуктов
$builderPizzaHawaii  = new BuilderPizzaHawaii();
$builderPizzaPiquante = new BuilderPizzaSpicy();
 
// Подготовка и получение продукта
$pizzaBuilder->setBuilderPizza( $builderPizzaHawaii );
$pizzaBuilder->constructPizza();
$pizza = $pizzaBuilder->getPizza();*/
?>