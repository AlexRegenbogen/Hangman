<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Hangman API - VueJS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='//fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="assets/hangman.css">
</head>
<body>
	<div id="hangman">

		<h1>VueJS/Canvas Hangman via API</h1>

		<div id="game">

			<canvas id="canvas" width="300" height="280"></canvas>

			<div class="testWord">{{ game.word }}</div>

			<div v-show="game.id" class="keyboard">
				<?php foreach (range('a', 'z') as $k => $char) { ?>
				<button v-on:click="guess"><?php echo $char; ?></button>
				<?php }?>
			</div>
		</div>

		<button v-show="!game.id" class="startNew" @click="startNewGame">Start New Game</button>

		<div v-if="game.status == 'fail'" @click="startNewGame" class="gameOver">
			<h2>{{ game.error }}</h2>
			<h3>Click this box to try again.</h3>
		</div>

		<div v-if="game.status == 'success'" @click="startNewGame" class="youWon">
			<h2>{{ game.error }}</h2>
			<h3>You are doing great! Click this box to play again.</h3>
		</div>

	</div>

	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/vue/1.0.12/vue.min.js"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/vue-resource/0.5.1/vue-resource.js"></script>
	<script type="text/javascript" src="assets/hangman.js"></script>
</body>
</html>