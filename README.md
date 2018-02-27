# Chatfuel-Response-Package
This is a composer package for generating chatfuel responses as provided in the chatfuel documentation.

# Reference:

http://docs.chatfuel.com/plugins/plugin-documentation/json-api

# Getting started:

This package is in active development and not discoverable by Composer via Packagist. But if you wish to test this package now you have to follow these procedures

Clone this repository inside your PHP project. As this project is intended to release as a composer package so you should better have composer initialized into your project and already have a /vendor directory inside your project root.

### via HTTPS
```
$ git clone https://github.com/tier5/Chatfuel-Response-Package.git
```
### via SSH
```
$ git clone git@github.com:tier5/Chatfuel-Response-Package.git
```

Now just run composer install
```
$ composer install
```

Now you should be able to access Chatfuel using proper namespace

```
use ChatFuel\Chatfuel;

$chatfuel = new Chatfuel();
```

# Syntax:

### For Text:

```
$chatfuel->text($text);
```

### For Audio:

```
$chatfuel->audio($audioUrl);
```

### For Video:

```
$chatfuel->video($videoUrl);
```

### For Image:

```
$chatfuel->image($imageUrl);
```

### For Gallery:

```
$chatfuel->gallery($gallery);
```
### For List:

```
$chatfuel->lists($list);
```

### For Buttons:

```
$chatfuel->buttons($buttons);
```

### For Special Buttons:

```
$chatfuel->spbuttons($spbuttons);
```
### For Redirect Blocks:

```
$chatfuel->redirectBlock($redirectBlock = ['abc','def']);
```

### For storing the response in a variable:

```
$response = $chatfuel->save();
```
# Examples:

The examples of how to use the functions in an efficient way is provided inside the example/example.php
