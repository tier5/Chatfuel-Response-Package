<?php
namespace ChatFuel;

use PHPUnit\Framework\Exception;

class Chatfuel
{
  /**
   * @const string
   */
  const VERSION = '0.0.1';

  /***
   * @var array
   */
  protected $response = array();

  /**
   * Chatfuel constructor.
   *
   */
  public function __construct() {}

  /**
   * @return string
   */
  public function save() {
    return json_encode(array('messages' => $this->response));
  }

  /***
   * Response for sending text messages.
   * @param null $messages
   * @throws Exception
   */
  public function text($messages = null,$quick_replies = null)
  {
    if (is_null($messages)) {
      throw new ChatfuelException('Invalid input', 1);
    }

    try{
        $type = gettype($messages);
        if ($type === 'string') {
          if(!is_null($quick_replies)) {
            $this->response[] = array('text' => $messages, 'quick_replies' => $this->createQuickReply($quick_replies));
          } else {
            $this->response[] = array('text' => $messages);
          }

        } elseif ($type === 'array' || is_array($messages)) {
          foreach ($messages as $message) {
            $this->response[] = array('text' => $message);
          }
          if(!is_null($quick_replies)) {
            $this->response[] = array('quick_replies' => $this->createQuickReply($quick_replies));
          }
        } else {
          throw new ChatfuelException('Error! Invalid datatype provided. String expected.');
        }
    } catch (ChatfuelException $e) {
      throw new ChatfuelException('Error: Please try again after sometime.');
    }
  }

  /***
   * Response for sending images.
   * @param $urls
   * @throws Exception
   */
  public function image($urls,$quickreplies = null)
  {
    $this->validityCheck('image',$urls,$quickreplies);
  }

  /***
   * Response for sending videos
   * @param $urls
   * @throws Exception
   */
  public function video($urls)
  {
    $this->validityCheck('video',$urls);
  }

  /***
   * Response for sending audio
   * @param $urls
   * @throws Exception
   */
  public function audio($urls)
  {
    $this->validityCheck('audio',$urls);
  }

  /***
   * Response for sending file
   * @param $urls
   * @throws Exception
   */
  public function file($urls)
  {
    $this->validityCheck('file',$urls);
  }

  /***
   * Checks the validity of resources (images, audios, videos, files) before creating the required response
   * @param $choice
   * @param $urls
   * @throws ChatfuelException
   */
  private function validityCheck($choice,$urls,$quickreplies = null) {
    $choice = strtolower($choice);
    $validChoices = array('image','video','audio','file');

    if(in_array($choice,$validChoices)) {
      try{
        if(is_array($urls)) {
          foreach ($urls as $url) {
            if($this->isURL($url)) {
                $this->attachment($choice, array('url' => $url));
            } else {
              $this->text('Error: Invalid URL!');
            }
          }
          if(!is_null($quickreplies)){
            $this->response[] = array('quick_replies' => $this->createQuickReply($quickreplies));
          }
        } else if (gettype($urls) === 'string' && $this->isURL($urls)) {
          if(!is_null($quickreplies)) {
            $this->attachment($choice, array('url' => $urls));
            $this->response[] = array('quick_replies' => $this->createQuickReply($quickreplies));
          } else {
            $this->attachment($choice, array('url' => $urls));
          }
        } else {
          $this->text('Error: Invalid URL!');
        }
      } catch (ChatfuelException $e) {
        $this->text('Error: Please try again after sometime.');
      }
    } else {
      $this->text('Not a valid resource choice!');
    }
  }

  /***
   * Response for sending gallery
   * @param int $number
   * @throws ChatfuelException
   */
  public function gallery($galleryArray,$quickreplies = null) {
    try{
      for($i = 0; $i < count($galleryArray);$i++) {
        $this->createGallery((is_array($galleryArray[$i])) ? $galleryArray[$i] : []);
      }
      if(!is_null($quickreplies)) {
        $this->response[] = array('quick_replies' => $this->createQuickReply($quickreplies));
      }
    } catch (ChatfuelException $e) {
      throw new ChatfuelException ($e->getMessage());
    }
  }

  /***
   * Function for creating galleries response.
   * @param $elements
   * @return bool
   * @throws ChatfuelException
   */
  private function createGallery($elements)
  {
    if (is_array($elements)) {
      $this->attachment('template', array(
          'template_type'       => 'generic',
          'image_aspect_ratio'  => 'square',
          'elements'            => $this->createElements($elements,1)
      ));
      return TRUE;
    }
    return FALSE;
  }

  /***
   * Function for creating overall elements.
   * @param array $elementsArray
   * @return array
   * @throws ChatfuelException
   */
  private function createElements($elementsArray = [[]],$choice) {
    $elements = [];
    if(is_array($elementsArray) && count($elementsArray) > 0) {
      switch($choice){
          case 1: $elements = $this->createGalleryElements($elementsArray);
                  break;
          case 2: $elements = $this->createListElements($elementsArray);
                  break;
      }
    } else {
      throw new ChatfuelException('Please provide proper argument list.Make sure that the array structure for elements is correct.');
    }
    return $elements;
  }

  /***
   * Function for creating gallery elements.
   * @param $elementsArray
   * @throws ChatfuelException
   */
  private function createGalleryElements($elementsArray) {
    $elements = [[]];
    for($i = 0;$i < count($elementsArray); $i++) {
      if (count($elementsArray[$i]) > 1) {
        if($elementsArray[$i][0] !== null && $elementsArray[$i][0] !== '' && gettype($elementsArray[$i][0]) === 'string') {
          $elements[$i]['title'] =  $elementsArray[$i][0];
        } else {
          throw new ChatfuelException ('Invalid datatype provided for title. String expected.');
        }

        if($elementsArray[$i][1] !== null && $elementsArray[$i][1] !== '' && gettype($elementsArray[$i][1]) === 'string' && $this->isURL($elementsArray[$i][1])) {
          $elements[$i]['image_url'] =  $elementsArray[$i][1];
        }

        if($elementsArray[$i][2] !== null && $elementsArray[$i][2] !== '' && gettype($elementsArray[$i][2]) === 'string') {
          $elements[$i]['subtitle'] =  $elementsArray[$i][2];
        }

        if((gettype($elementsArray[$i][3]) === 'array' ||  is_array($elementsArray[$i][3])) && $elementsArray[$i][2] !== null && count($elementsArray[$i][3]) > 0) {
          $elements[$i]['default_action'] =  $this->createdefaultAction($elementsArray[$i][3]);
        }

        if((gettype($elementsArray[$i][4]) === 'array' ||  is_array($elementsArray[$i][4])) && count($elementsArray[$i][4]) > 0) {
          $elements[$i]['buttons'] =  $this->createGalleryButton($elementsArray[$i][4]);
        }

      } else {
        throw new ChatfuelException ('The array structure must contain an optional value (subtitle, image_url or button).');
      }
    }
    return $elements;
  }
  /***
   * Function for creating gallery buttons.
   * @param $elementArray
   * @return array
   * @throws ChatfuelException
   */
  private function createGalleryButton($elementArray) {
    $buttons = [[]];
    for($i = 0; $i < count($elementArray); $i++) {
      if(is_array($elementArray[$i]) && count($elementArray[$i]) > 0) {
        switch($elementArray[$i][0]) {
          case '1': if(count($elementArray[$i]) === 3 && array_key_exists(1,$elementArray[$i]) && array_key_exists(2,$elementArray[$i])) {
                      $buttons[$i] =$this->urlButton($elementArray[$i][1],$elementArray[$i][2]);
                    } else {
                        throw new ChatfuelException ('Please provide the proper array structure for the url button element.');
                    }
                    break;
          case '2': if(count($elementArray[$i]) >= 3 && array_key_exists(1,$elementArray[$i]) && array_key_exists(2,$elementArray[$i])) {
                      $buttons[$i] =$this->blockButton($elementArray[$i][1],$elementArray[$i][2],array_key_exists(3,$elementArray[$i]) ? $elementArray[$i][3] : null);
                    }else {
                      throw new ChatfuelException ('Please provide the proper array structure for the block button element.');
                    }
                    break;
          case '3': if(count($elementArray[$i]) >= 3 && array_key_exists(1,$elementArray[$i]) && array_key_exists(2,$elementArray[$i])) {
                      $buttons[$i] =$this->postbackButton($elementArray[$i][1],$elementArray[$i][2],array_key_exists(3,$elementArray[$i]) ? $elementArray[$i][3] : null);
                    }else {
                      throw new ChatfuelException ('Please provide the proper array structure for the postback button element.');
                    }
                    break;
          case '4': if(count($elementArray[$i]) === 3 && array_key_exists(1,$elementArray[$i]) && array_key_exists(2,$elementArray[$i])) {
                      $buttons[$i] =$this->callButton($elementArray[$i][1],$elementArray[$i][2]);
                    }else {
                      throw new ChatfuelException ('Please provide the proper array structure for the call button element.');
                    }
                    break;
          case '5': if(count($elementArray[$i]) === 1) {
                      $buttons[$i] =$this->shareButton();
                    }else {
                      throw new ChatfuelException ('Please provide the proper array structure for the share button element.');
                    }
                    break;
          default: throw new ChatfuelException ('Invalid choice for button element.');
        }
      } else {
        throw new ChatfuelException('Error! invalid data provided for the buttons');
      }
    }
    return $buttons;
  }

  /***
   * Function for creating default actions.
   * @param $elementArray
   * @throws ChatfuelException
   */
  private function createdefaultAction($elementArray) {
    $default_actions = [];
    if(count($elementArray) > 0 || count($elementArray) <3) {
        if(gettype($elementArray[0]) === 'string' && $elementArray[0] === 'web_url') {
          $default_actions['type'] = $elementArray[0];
        } else {
          throw new ChatfuelException('The type field is not valid. Please ensure it is a string or a valid type');
        }

        if(gettype($elementArray[1]) === 'string' && $this->isURL($elementArray[1])) {
          $default_actions['url'] = $elementArray[1];
        } else {
          throw new ChatfuelException('The type field is not valid. Please ensure it is a string or a valid type');
        }

        if(gettype($elementArray[2]) === 'bool') {
          $default_actions['messenger_extensions'] = $elementArray[2];
        } else {
          throw new ChatfuelException('The messenger_extensions field is not valid. Please ensure it is a boolean field');
        }

      } else {
      throw new ChatfuelException('The default action field must contain a type (string), url(string) and a messenger_extensions field (boolean)');
    }
    return $default_actions;
  }

  /***
   * Response for sending lists.
   * @param $elements
   * @param $top_element_style
   * @return bool
   */
  public function lists($elements,$quickreplies = null){
    try {
      $valid_top_element_style = array('large', 'compact');
      if(is_array($elements) && count($elements) > 0){
        for($i = 0; $i < count($elements); $i++) {
          if(gettype($elements[$i][0]) === 'string' && in_array($elements[$i][0],$valid_top_element_style)) {
            $this->createList((is_array($elements[$i][1])) ? $elements[$i][1] : []);
          } else {
            throw new ChatfuelException('Please provide proper array structure for the list.');
          }
        }

        if(!is_null($quickreplies)){
          $this->response[] = array('quick_replies' => $this->createQuickReply($quickreplies));
        }
      } else {
        throw new ChatfuelException('Please provide proper arguments.The array cannot be an empty array');
      }
    } catch( Exception $e) {
      throw new ChatfuelException($e->getMessage());
    }
  }

  /***
   * Function for creating lists.
   * @param $elements
   * @return bool
   */
  private function createList($elements) {
    if (is_array($elements)) {
      $this->attachment('template', array(
          'template_type'       => 'generic',
          'image_aspect_ratio'  => 'square',
          'elements'            => $this->createElements($elements,2)
      ));
      return TRUE;
    }
    return FALSE;
  }


  /***
   * Function for creating list elements.
   * @param $elementsArray
   * @throws ChatfuelException
   */
  private function createListElements($elementsArray) {
    $elements = [[]];
    for($i = 0;$i < count($elementsArray); $i++) {
      if (count($elementsArray[$i]) > 1) {
        if($elementsArray[$i][0] !== null && $elementsArray[$i][0] !== '' && gettype($elementsArray[$i][0]) === 'string') {
          $elements[$i]['title'] =  $elementsArray[$i][0];
        } else {
          throw new ChatfuelException ('Invalid datatype provided for title. String expected.');
        }

        if($elementsArray[$i][1] !== null && $elementsArray[$i][1] !== '' && gettype($elementsArray[$i][1]) === 'string' && $this->isURL($elementsArray[$i][1])) {
          $elements[$i]['image_url'] =  $elementsArray[$i][1];
        }

        if($elementsArray[$i][2] !== null && $elementsArray[$i][2] !== '' && gettype($elementsArray[$i][2]) === 'string') {
          $elements[$i]['subtitle'] =  $elementsArray[$i][2];
        }

        if((gettype($elementsArray[$i][3]) === 'array' ||  is_array($elementsArray[$i][3])) && count($elementsArray[$i][3]) > 0) {
          $elements[$i]['buttons'] =  $this->createListButton($elementsArray[$i][3]);
        }

      } else {
        throw new ChatfuelException ('The array structure must contain an optional value (subtitle, image_url or button).');
      }
    }
    return $elements;
  }

  /***
   * Function for creating list buttons.
   * @param $elementArray
   * @return array
   * @throws ChatfuelException
   */
  private function createListButton($elementArray) {
    $buttons = [[]];
    for($i = 0; $i < count($elementArray); $i++) {
      if(is_array($elementArray[$i]) && count($elementArray[$i]) > 0) {
        switch($elementArray[$i][0]) {
          case '1': if(count($elementArray[$i]) === 3 && array_key_exists(1,$elementArray[$i]) && array_key_exists(2,$elementArray[$i])) {
                      $buttons[$i] =$this->urlButton($elementArray[$i][1],$elementArray[$i][2]);
                    } else {
                      throw new ChatfuelException ('Please provide the proper array structure for the url button element.');
                    }
                    break;
          case '2': if(count($elementArray[$i]) >= 3 && array_key_exists(1,$elementArray[$i]) && array_key_exists(2,$elementArray[$i])) {
                      $buttons[$i] =$this->blockButton($elementArray[$i][1],$elementArray[$i][2],array_key_exists(3,$elementArray[$i]) ? $elementArray[$i][3] : null);
                    }else {
                      throw new ChatfuelException ('Please provide the proper array structure for the block button element.');
                    }
                      break;
          case '3': if(count($elementArray[$i]) >= 3 && array_key_exists(1,$elementArray[$i]) && array_key_exists(2,$elementArray[$i])) {
                      $buttons[$i] =$this->postbackButton($elementArray[$i][1],$elementArray[$i][2],array_key_exists(3,$elementArray[$i]) ? $elementArray[$i][3] : null);
                    }else {
                      throw new ChatfuelException ('Please provide the proper array structure for the postback button element.');
                    }
                    break;
          case '4': if(count($elementArray[$i]) === 3 && array_key_exists(1,$elementArray[$i]) && array_key_exists(2,$elementArray[$i])) {
                      $buttons[$i] =$this->callButton($elementArray[$i][1],$elementArray[$i][2]);
                    }else {
                      throw new ChatfuelException ('Please provide the proper array structure for the call button element.');
                    }
                    break;
          case '5': if(count($elementArray[$i]) === 1) {
                      $buttons[$i] =$this->shareButton();
                    }else {
                      throw new ChatfuelException ('Please provide the proper array structure for the share button element.');
                    }
                    break;
          default: throw new ChatfuelException ('Invalid choice for button element.');
        }
      } else {
        throw new ChatfuelException('Error! invalid data provided for the buttons');
      }
    }
    return $buttons;
  }

  /***
   * Response for sending normal buttons
   * @param $buttonsArray
   * @throws ChatfuelException
   */
  public function buttons($buttonsArray)
  {
    try{
      if(is_array($buttonsArray) && count($buttonsArray) > 0){
        for($i = 0; $i < count($buttonsArray); $i++) {
          if(is_array($buttonsArray[$i]) &&  gettype($buttonsArray[$i][0]) === 'string' &&  is_array($buttonsArray[$i][1])) {
            $this->createButtons($buttonsArray[$i]);
          } else {
            throw new ChatfuelException('Please provide proper array structure for the list.');
          }
        }
      } else {
        throw new ChatfuelException('Please provide proper arguments.The array cannot be an empty array');
      }
    } catch (ChatfuelException $e) {
      throw new ChatfuelException($e->getMessage());
    }
  }

  /**
   * Function for creating the attachment response for sending default buttons.
   * @param $buttonArray
   * @return bool
   */
  private function createButtons($buttonArray) {
    if (is_array($buttonArray)) {
      $this->attachment('template', array(
          'template_type' => 'button',
          'text'          => (array_key_exists(0,$buttonArray)) ? $buttonArray[0] : '',
          'buttons'       => $this->createDefaultButtons((array_key_exists(1,$buttonArray)) ? $buttonArray[1] : [])
      ));
      return TRUE;
    }
    return FALSE;
  }

  /***
   * Function for creating default buttons
   * @param $buttonArray
   */
  private function createDefaultButtons($buttonArray) {
    $buttons = [[]];
    for($i = 0; $i < count($buttonArray); $i++) {
      if(is_array($buttonArray[$i]) && count($buttonArray[$i]) > 0) {
        switch($buttonArray[$i][0]) {
          case '1': if(count($buttonArray[$i]) === 3 && array_key_exists(1,$buttonArray[$i]) && array_key_exists(2,$buttonArray[$i])) {
                      $buttons[$i] =$this->urlButton($buttonArray[$i][1],$buttonArray[$i][2]);
                    } else {
                      throw new ChatfuelException ('Please provide the proper array structure for the url button element.');
                    }
                    break;
          case '2': if(count($buttonArray[$i]) >= 3 && array_key_exists(1,$buttonArray[$i]) && array_key_exists(2,$buttonArray[$i])) {
                      $buttons[$i] =$this->blockButton($buttonArray[$i][1],$buttonArray[$i][2],array_key_exists(3,$buttonArray[$i]) ? $buttonArray[$i][3] : null);
                    }else {
                      throw new ChatfuelException ('Please provide the proper array structure for the block button element.');
                    }
                    break;
          case '3': if(count($buttonArray[$i]) >= 3 && array_key_exists(1,$buttonArray[$i]) && array_key_exists(2,$buttonArray[$i])) {
                      $buttons[$i] =$this->postbackButton($buttonArray[$i][1],$buttonArray[$i][2],array_key_exists(3,$buttonArray[$i]) ? $buttonArray[$i][3] : null);
                    }else {
                      throw new ChatfuelException ('Please provide the proper array structure for the postback button element.');
                    }
                    break;
          default: throw new ChatfuelException ('Invalid choice for button element.');
        }
      } else {
        throw new ChatfuelException('Error! invalid data provided for the buttons');
      }
    }
    return $buttons;
  }

  /***
   * Response for sending the specialised buttons
   * @param $buttonsArray
   * @throws ChatfuelException
   */
  public function spbuttons($buttonsArray)
  {
    try{
      if(is_array($buttonsArray) && count($buttonsArray) > 0){
        for($i = 0; $i < count($buttonsArray); $i++) {
          if(is_array($buttonsArray[$i])) {
            $this->createspButtons($buttonsArray[$i]);
          } else {
            throw new ChatfuelException('Please provide proper array structure for the list.');
          }
        }
      } else {
        throw new ChatfuelException('Please provide proper arguments.The array cannot be an empty array');
      }
    } catch (ChatfuelException $e) {
      throw new ChatfuelException($e->getMessage());
    }
  }

  /**
   * Function for creating the attachment response for sending default buttons.
   * @param $buttonArray
   * @return bool
   */
  private function createspButtons($buttonArray) {
    if (is_array($buttonArray)) {
      $this->attachment('template', array(
          'template_type' => 'generic',
          'elements' => $this->createButtonElements($buttonArray)
      ));
      return TRUE;
    }
    return FALSE;
  }

  /***
   * Response for creating specialised button elements.
   * @param $buttonArray
   * @return array
   * @throws ChatfuelException
   */
  private function createButtonElements($buttonArray) {
    $elements = [[]];
    for($i = 0;$i < count($buttonArray); $i++) {
      if (count($buttonArray[$i]) > 1) {
        if($buttonArray[$i][0] !== null && $buttonArray[$i][0] !== '' && gettype($buttonArray[$i][0]) === 'string') {
          $elements[$i]['title'] =  $buttonArray[$i][0];
        } else {
          throw new ChatfuelException ('Invalid datatype provided for title. String expected.');
        }

        if($buttonArray[$i][1] !== null && $buttonArray[$i][1] !== '' && gettype($buttonArray[$i][1]) === 'string' && $this->isURL($buttonArray[$i][1])) {
          $elements[$i]['image_url'] =  $buttonArray[$i][1];
        }

        if($buttonArray[$i][2] !== null && $buttonArray[$i][2] !== '' && gettype($buttonArray[$i][2]) === 'string') {
          $elements[$i]['subtitle'] =  $buttonArray[$i][2];
        }

        if((gettype($buttonArray[$i][3]) === 'array' ||  is_array($buttonArray[$i][3])) && $buttonArray[$i][2] !== null && count($buttonArray[$i][3]) > 0) {
          $elements[$i]['default_action'] =  $this->createdefaultAction($buttonArray[$i][3]);
        }

        if((gettype($buttonArray[$i][4]) === 'array' ||  is_array($buttonArray[$i][4])) && count($buttonArray[$i][4]) > 0) {
          $elements[$i]['buttons'] =  $this->createSpecialButtons($buttonArray[$i][4]);
        }

      } else {
        throw new ChatfuelException ('The array structure must contain an optional value (subtitle, image_url or button).');
      }
    }
    return $elements;
  }
  
  /***
   * Function for creating specialised buttons.
   * @param $buttonArray
   */
  private function createSpecialButtons($buttonArray) {
    $buttons = [[]];
    for($i = 0; $i < count($buttonArray); $i++) {
      if(is_array($buttonArray[$i]) && count($buttonArray[$i]) > 0) {
        switch($buttonArray[$i][0]) {
          case '4': if(count($buttonArray[$i]) === 3 && array_key_exists(1,$buttonArray[$i]) && array_key_exists(2,$buttonArray[$i])) {
                      $buttons[$i] =$this->callButton($buttonArray[$i][1],$buttonArray[$i][2]);
                    }else {
                      throw new ChatfuelException ('Please provide the proper array structure for the call button element.');
                    }
                    break;
          case '5': if(count($buttonArray[$i]) === 1) {
                      $buttons[$i] =$this->shareButton();
                    }else {
                      throw new ChatfuelException ('Please provide the proper array structure for the share button element.');
                    }
                    break;
          default: throw new ChatfuelException ('Invalid choice for button element.');
        }
      } else {
        throw new ChatfuelException('Error! invalid data provided for the buttons');
      }
    }
    return $buttons;
  }

  /***
   * Function for creating block buttons
   * @param $title
   * @param $block
   * @param null $setAttributes
   * @return array
   * @throws ChatfuelException
   */
  private function blockButton($title, $block, $setAttributes = NULL)
  {
    if(gettype($title) === 'string'){
      $button = array();
      $button['type'] = 'show_block';
      $button['title'] = $title;
    } else {
      throw new ChatfuelException('Invalid data provided for the block button. The title field in the array must be a string.');
    }

    if (is_array($block) && count($block) > 0) {
      $button['block_names'] = $block;
    } else if (gettype($block) === 'string') {
      $button['block_name'] = $block;
    } else {
      throw new ChatfuelException('Invalid data provided for the block button. The block field in the array must be a string or an array.');
    }


    if ( ! is_null($setAttributes) && is_array($setAttributes)) {
      $button['set_attributes'] = $setAttributes;
    } else if (is_null($setAttributes)) {

    } else {
      throw new ChatfuelException('Invalid data provided for the block button. The set_attributes field in the array must be an array.');
    }

    return $button;
  }

  /***
   * Function for creating url buttons
   * @param $title
   * @param $url
   * @return array
   * @throws ChatfuelException
   */
  private function urlButton($title, $url)
  {
    if ($this->isURL($url) && gettype($title) === 'string') {
      $button = array();
      $button['type'] = 'web_url';
      $button['url'] = $url;
      $button['title'] = $title;
      return $button;
    } else {
      throw new ChatfuelException('Invalid data provided for the url button.The url field must be a valid url and the title must be string');
    }
  }

  /***
   * Function for creating postback buttons
   * @param $title
   * @param $url
   * @param null $setAttributes
   * @return array
   * @throws ChatfuelException
   */
  private function postbackButton($title, $url , $setAttributes = NULL)
  {
    if ($this->isURL($url) && gettype($title) === 'string') {
      $button = array();
      $button['type'] = 'json_plugin_url';
      $button['url'] = $url;
      $button['title'] = $title;

      if ( ! is_null($setAttributes) && is_array($setAttributes)) {
        $button['set_attributes'] = $setAttributes;
      } else if (is_null($setAttributes)) {

      } else {
        throw new ChatfuelException('Invalid data provided for the postback button. The set_attributes field in the array must be an array.');
      }
      return $button;
    } else {
      throw new ChatfuelException('Invalid data provided for the postback button.The url field must be a valid url and the title must be string');
    }
  }

  /***
   * Function for creating call buttons
   * @param $phoneNumber
   * @param string $title
   * @return array
   * @throws ChatfuelException
   */
  private function callButton($phoneNumber, $title = 'Call')
  {
    if(gettype($phoneNumber) === 'string' && gettype($title) === 'string') {
      return array(
          'type'         => 'phone_number',
          'phone_number' => $phoneNumber,
          'title'        => $title
      );
    } else  {
      throw new ChatfuelException('Invalid data provided for the call button.The phone number field must be a valid phone number and the title must be string');
    }
  }

  /***
   * Function for creating share buttons
   * @return array
   */
  private function shareButton()
  {
    return array('type' => 'element_share');
  }

  /***
   * Function for creating quick replies
   * @param $quick_replies
   * @return array
   * @throws ChatfuelException
   */
  private function createQuickReply($quick_replies)
  {
    $replies = $this->quickreplyButtons($quick_replies);
    return $replies;
  }

  /***
   * Function for creating quick reply buttons
   * @param $quick_replies
   * @return array
   * @throws ChatfuelException
   */
  private function quickreplyButtons($quick_replies) {
    $buttons = [[]];
    for($i = 0; $i < count($quick_replies); $i++) {
      if(is_array($quick_replies[$i]) && count($quick_replies[$i]) > 0) {
        switch($quick_replies[$i][0]) {
          case '1': if(count($quick_replies[$i]) === 3 && array_key_exists(1,$quick_replies[$i]) && array_key_exists(2,$quick_replies[$i])) {
                      $buttons[$i] =$this->urlButton($quick_replies[$i][1],$quick_replies[$i][2]);
                    } else {
                      throw new ChatfuelException ('Please provide the proper array structure for the url button element.');
                    }
                    break;
          case '2': if(count($quick_replies[$i]) >= 3 && array_key_exists(1,$quick_replies[$i]) && array_key_exists(2,$quick_replies[$i])) {
                      $buttons[$i] =$this->blockButton($quick_replies[$i][1],$quick_replies[$i][2],array_key_exists(3,$quick_replies[$i]) ? $quick_replies[$i][3] : null);
                    }else {
                      throw new ChatfuelException ('Please provide the proper array structure for the block button element.');
                    }
                      break;
          case '3': if(count($quick_replies[$i]) >= 3 && array_key_exists(1,$quick_replies[$i]) && array_key_exists(2,$quick_replies[$i])) {
                      $buttons[$i] =$this->postbackButton($quick_replies[$i][1],$quick_replies[$i][2],array_key_exists(3,$quick_replies[$i]) ? $quick_replies[$i][3] : null);
                    }else {
                      throw new ChatfuelException ('Please provide the proper array structure for the postback button element.');
                    }
                    break;
          case '4': if(count($quick_replies[$i]) === 3 && array_key_exists(1,$quick_replies[$i]) && array_key_exists(2,$quick_replies[$i])) {
                      $buttons[$i] =$this->callButton($quick_replies[$i][1],$quick_replies[$i][2]);
                    }else {
                      throw new ChatfuelException ('Please provide the proper array structure for the call button element.');
                    }
                    break;
          case '5': if(count($quick_replies[$i]) === 1) {
                      $buttons[$i] =$this->shareButton();
                    }else {
                      throw new ChatfuelException ('Please provide the proper array structure for the share button element.');
                    }
                    break;
          default: throw new ChatfuelException ('Invalid choice for button element.');
        }
      } else {
        throw new ChatfuelException('Error! invalid data provided for the buttons');
      }
    }
    return $buttons;
  }

  /***
   * Response for sending an attachment
   * @param $type
   * @param $payload
   */
  private function attachment($type, $payload)
  {
    $type = strtolower($type);
    $validTypes = array('image', 'video', 'audio', 'file', 'template');

    if (in_array($type, $validTypes)) {
      $this->response[] = array(
        'attachment' => array(
          'type'    => $type,
          'payload' => $payload
        )
      );
    } else {
      $this->response[] = array('text' => 'Error: Invalid type!');
    }
  }

  /***
   * Function for checking a valid url
   * @param $url
   * @return mixed
   */
  private function isURL($url)
  {
    return filter_var($url, FILTER_VALIDATE_URL);
  }

  /***
   * Function for block redirection response
   * @param $block
   */
  public function redirectBlock($block) {
    try{
      if(is_array($block) || gettype($block) === 'string') {
        $this->response[] = array('redirect_to_blocks' => $block);
      } else {
        throw new ChatfuelException('Enter proper argument for blocks');
      }
    }catch(ChatfuelException $e) {
      throw new Exception($e->getMessage());
    }
  }
}