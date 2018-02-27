<?php 
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload
use ChatFuel\Chatfuel;

/**
 * Initialising chatfuel
 */
 $chatfuelText = new Chatfuel();
/**
 * Sending text as array
 */
 $chatfuelText->text(['hi','how are you','need some time']);
/**
 * Sending text as string
 */
 $chatfuelText->text('Hello!');
/**
 *  Sending text with quickreplies
 *  Quickreplies format must be same as defined below (Multiple Quickreplies)
 */
 $chatfuelText->text(['hi','how are you','need some time'],[['1','chat',"https://rockets.chatfuel.com/img/welcome.png"],['2','chat','asfa']]);
/**
 *  Sending text with quickreplies
 *  Quickreplies format must be same as defined below (Single Quickreplies)
 */
 $chatfuelText->text(['hi','how are you','need some time'],[['1','chat',"https://rockets.chatfuel.com/img/welcome.png"]]);
/**
 * Sending image url
 */
 $chatfuelImage = new Chatfuel();
/**
 * Multiple URLs
 */
 $chatfuelImage->image([
    "https://rockets.chatfuel.com/img/welcome.png",
    "https://rockets.chatfuel.com/img/welcome.png",
    "https://rockets.chatfuel.com/img/welcome.png"
 ]);
/**
 * Single URLs
 */
 $chatfuelImage->image("https://rockets.chatfuel.com/img/welcome.png");
/**
 * Multiple URLs with quick replies
 */
 $chatfuelImage->image([
    "https://rockets.chatfuel.com/img/welcome.png",
    "https://rockets.chatfuel.com/img/welcome.png",
    "https://rockets.chatfuel.com/img/welcome.png"
 ],[['1','chat',"https://rockets.chatfuel.com/img/welcome.png"]]);
/**
 * Single URLs
 */
 $chatfuelImage->image("https://rockets.chatfuel.com/img/welcome.png",[['1','chat',"https://rockets.chatfuel.com/img/welcome.png"]]);
/**
 * Sending Videos
 */
 $chatfuelVideo =  new Chatfuel();
/**
 * Multiple urls
 */
 $chatfuelVideo->video([
    "https://rockets.chatfuel.com/img/welcome.png",
    "https://rockets.chatfuel.com/img/welcome.png",
    "https://rockets.chatfuel.com/img/welcome.png"
 ]);
/**
 * Single url
 */
 $chatfuelVideo->video("https://rockets.chatfuel.com/img/welcome.png");
/**
 * Sending audios
 */
 $chatfuelAudio = new Chatfuel();
/**
 * Multiple urls
 */
 $chatfuelAudio->audio([
    "https://rockets.chatfuel.com/",
    "https://rockets.chatfuel.com/",
    "https://rockets.chatfuel.com/"
  ]);
/**
 * Single url
 */
 $chatfuelAudio->audio("https://rockets.chatfuel.com/");
/**
 * Defining the gallery structure (Note that the structure should be exactly as defined). Elements Array should contain at least two fields. Optional fields can be replaced with null if not needed
 * Button types 1: url, 2: block, 3: postback, 4: call, 5: share. Properties will change on the basis of what type the user chooses.Quick replies can be added as the second parameter.
 */
 $chatfuel = new Chatfuel();
 $galleryElements = [
    //'gallery1' =>
    [
        //'element1' =>
        [//title (required)=>
            'title1',
         // image_url (optional) =>
            "https://rockets.chatfuel.com/img/welcome.png",
         //subtitle (optional)   
            "subtitle",
         //default_actions (optional)
            null,
         //buttons (optional) =>
            [
                [   //type(required)
                    '1',
                    //title(required)
                    'chat',
                    //url(required)
                    "https://rockets.chatfuel.com/img/welcome.png"
                ],
                [
                    '2',
                    'chat',
                    'asfa'
                ]
            ]
        ],
        //'element2' =>
        ['title2',"https://rockets.chatfuel.com/img/welcome.png",null,null,[['1','chat',"https://rockets.chatfuel.com/img/welcome.png"],['2','chat','asfa']]]
    ],
    //'gallery2' =>
    [
        //'element1' =>
        ['title3',"https://rockets.chatfuel.com/img/welcome.png",null,null,[['1','chat',"https://rockets.chatfuel.com/img/welcome.png"],['2','chat','asfa']]],
        //'element2' =>
        ['title4',"https://rockets.chatfuel.com/img/welcome.png",null,null,[['1','chat',"https://rockets.chatfuel.com/img/welcome.png"],['2','chat','asfa']]]
    ]
];

//$chatfuel->gallery($galleryElements);

/**
 * Defining the list structure (Note that the structure should be exactly as defined). Elements should contain at least two fields. Optional fields can be replaced with null
 * Button types 1: url, 2: block, 3: postback, 4: call, 5: share. Properties will change on the basis of what type the user chooses.Can be found from the api documentation  http://docs.chatfuel.com/plugins/plugin-documentation/json-api .Quick replies can be added as the second parameter.
 */

//$chatfuel = new Chatfuel();
$listElements = [
//'list1' =>
    [
        //top_element_style(required) =>
        'large',
        [
            [//title (required)=>
            'title1',
            // image_url (optional) =>
            "https://rockets.chatfuel.com/img/welcome.png",
            //subtitle (optional)
            "subtitle",
            //default_actions (optional)
            null,
            //buttons (optional) =>
                [
                    [   //type(required)
                        '1',
                        //title(required)
                        'chat',
                        //url(required)
                        "https://rockets.chatfuel.com/img/welcome.png"
                    ],
                    [
                        '2',
                        'chat',
                        'asfa'
                    ]
                ]
            ],
            //'element2' =>
            ['title2',"https://rockets.chatfuel.com/img/welcome.png",null,[['1','chat',"https://rockets.chatfuel.com/img/welcome.png"],['2','chat','asfa']]]
        ]
    ],
//'list2' =>
    [
        'compact',
        [//'element1' =>
            ['title3',"https://rockets.chatfuel.com/img/welcome.png",null,[['1','chat',"https://rockets.chatfuel.com/img/welcome.png"],['2','chat','asfa']]],
            //'element2' =>
            ['title4',"https://rockets.chatfuel.com/img/welcome.png",null,[['1','chat',"https://rockets.chatfuel.com/img/welcome.png"],['2','chat','asfa']]]
        ]
    ]
];

//$chatfuel->lists($listElements);

/**
 * Creating buttons and special buttons.
 * Button types 1: url, 2: block, 3: postback. Properties will change on the basis of what type the user chooses.Properties can be found from the api documentation http://docs.chatfuel.com/plugins/plugin-documentation/json-api
 */
//$chatfuel = new Chatfuel();
$buttons =
    [   //button1
        [
            //text(required) =>
            'text',
            [
                //buttons
                [   //type
                    '1',
                    //title
                    'chat',
                    //url
                    "https://rockets.chatfuel.com/img/welcome.png"
                ],['2','chat','asfa']
            ]
        ]
    ];


/**
 * Defining the button structure (Note that the structure should be exactly as defined). Elements should contain at least two fields. Optional fields can be replaced with null
 * Button types 4: call, 5: share. Properties will change on the basis of what type the user chooses.Properties can be found from the api documentation http://docs.chatfuel.com/plugins/plugin-documentation/json-api
 */
$spbuttonElements = [
//'gallery1' =>
    [//'element1' =>
        ['title1',"https://rockets.chatfuel.com/img/welcome.png",null,null,[['4','564536325',"abc"],['5']]],
        //'element2' =>
        ['title2',"https://rockets.chatfuel.com/img/welcome.png",null,null,[['4','564536325',"abc"],['5']]]
    ],
//'gallery2' =>
    [//'element1' =>
        ['title3',"https://rockets.chatfuel.com/img/welcome.png",null,null,[['4','564536325',"abc"],['5']]],
        //'element2' =>
        ['title4',"https://rockets.chatfuel.com/img/welcome.png",null,null,[['4','564536325',"abc"],['5']]]
    ]
];

$chatfuel->lists($listElements);
$chatfuel->buttons($buttons);
$chatfuel->spbuttons($spbuttonElements);

/**Redirect block response*/
$chatfuel->redirectBlock(['abc','def']);

/**Saving the json response*/
$response = $chatfuel->save();
echo $response;
