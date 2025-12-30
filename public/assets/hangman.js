/**
 * CSS and Canvas segment originally by: Lalwani Vikas https://twitter.com/LalwaniVikas
 */

var mainContainer = document.getElementById('hangman')[0],
    canvas = document.getElementById('canvas'),
    testWord = document.getElementsByClassName('testWord')[0],
    keyboard = document.querySelectorAll('.keyboard div div'),
    gameOver = document.querySelector('.gameOver'),
    youWon = document.querySelector('.youWon');

var context = canvas.getContext('2d'),
    randomNumber,
    misses = 0,
    bodyParts = [drawHead, drawSpine, drawLeftHand, drawRightHand, drawLeftLeg, drawRightLeg];


// canvas dimension: w:300px, h:300px
// function to draw hanging stand using Canvas
function drawStand() {
    context.beginPath();
    context.moveTo(60, 300);
    context.lineTo(60, 2);
    context.lineTo(170, 2);
    context.lineTo(170, 25);
    context.lineWidth = 4;
    context.strokeStyle = '#607D8B';
    context.stroke();
}

drawStand();


// function to draw head using Canvas
function drawHead() {
    context.beginPath();
    context.arc(170, 60, 35, 0, 2 * Math.PI);
    context.moveTo(155, 50);
    context.arc(155, 50, 2, 0, 2 * Math.PI);
    context.moveTo(185, 50);
    context.arc(185, 50, 2, 0, 2 * Math.PI);
    context.moveTo(160, 75);
    context.lineTo(180, 75);
    context.lineWidth = 3;
    context.strokeStyle = '#C51109';
    context.stroke();
}


// function to draw spine using Canvas
function drawSpine() {
    context.beginPath();
    context.moveTo(170, 95);
    context.lineTo(170, 200);
    context.lineWidth = 3;
    context.strokeStyle = '#C51109';
    context.stroke();
}


// function to draw left hand using Canvas
function drawLeftHand() {
    context.beginPath();
    context.moveTo(170, 135);
    context.lineTo(120, 105);
    context.lineWidth = 3;
    context.strokeStyle = '#C51109';
    context.stroke();
}


// function to draw right hand using Canvas
function drawRightHand() {
    context.beginPath();
    context.moveTo(170, 135);
    context.lineTo(220, 105);
    context.lineWidth = 3;
    context.strokeStyle = '#C51109';
    context.stroke();
}


// function to draw left leg using Canvas
function drawLeftLeg() {
    context.beginPath();
    context.moveTo(170, 200);
    context.lineTo(220, 230);
    context.lineWidth = 3;
    context.strokeStyle = '#C51109';
    context.stroke();
}


// function to draw right leg using Canvas
function drawRightLeg() {
    context.beginPath();
    context.moveTo(170, 200);
    context.lineTo(120, 230);
    context.lineWidth = 3;
    context.strokeStyle = '#C51109';
    context.stroke();

}

var Hangman = new Vue({
    el: '#hangman',
    data: {
        game: []
    },
    methods: {
        startNewGame: function (event) {
            // Reset visuals
            context.clearRect(0, 0, 300, 300);
            drawStand();

            // Reset any chosen characters from the previous game
            container = document.getElementsByClassName('keyboard')[0]
            btns = container.getElementsByTagName("button");
            for (var i = 0; i < btns.length; i++) {
                btns[i].className = '';
                btns[i].disabled = false;
            }

            // Request new game from API
            Vue.http.post('/api/games', function (game) {
                Hangman.game = game;
            });
        },
        guess: function (event) {
            // Add class to button, and disable it
            // Prevents reselecting the same letter.
            event.target.className = event.target.className + ' used';
            event.target.disabled = true;

            // Submit the chosen letter to the API
            Vue.http.put('/api/games/' + this.$data.game.id, {'character': event.target.innerHTML}, function (game) {
                Hangman.game = game;
                if (game.tries_left === 5) drawHead();
                if (game.tries_left === 4) drawSpine();
                if (game.tries_left === 3) drawLeftHand();
                if (game.tries_left === 2) drawRightHand();
                if (game.tries_left === 1) drawLeftLeg();
                if (game.tries_left === 0) drawRightLeg();
            });
        }
    }
});
