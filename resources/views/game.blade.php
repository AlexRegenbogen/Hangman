<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Hangman API - VueJS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='//fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>
    @vite(['resources/js/hangman.js'])
    @inertiaHead
</head>
<body>
    @inertia('hangman')
    <div id="hangman"></div>
</body>
</html>
