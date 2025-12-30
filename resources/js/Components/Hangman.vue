<script setup>
import {computed, onMounted, ref} from "vue";
import axios from "axios";
import { useHangmanCanvas } from "../Composables/useHangmanCanvas";

const {canvasRef, initContext, clear, drawStand, updateVisuals} = useHangmanCanvas();

const alphabet = [...'abcdefghijklmnopqrstuvwxyz'];
const game = ref({status: 'idle', word: '', tries_left: 6});
const guessedLetters = ref([]);
const locale = ref('en');

const isBusy = computed(() => game.value.status === 'busy');

onMounted(() => {
  initContext();
  drawStand();
});

const startNewGame = async () => {
  try {
    const {data} = await axios.post('/api/games', {locale: locale.value});
    game.value = data.data || data;
    guessedLetters.value = [];
    clear();
    drawStand();
  } catch (error) {
    console.error("Failed to start game:", error);
  }
};

const toggleLocale = () => {
  locale.value = (locale.value === 'en') ? 'nl' : 'en';
};

const guess = async (letter) => {
  if (!isBusy.value || guessedLetters.value.includes(letter)) return;

  guessedLetters.value.push(letter);
  try {
    const prevTries = game.value.tries_left;
    const {data} = await axios.put(`/api/games/${game.value.id}`, {character: letter});
    game.value = data.data || data;

    if (game.value.tries_left < prevTries) {
      updateVisuals(game.value.tries_left);
    }
  } catch (error) {
    console.error("Error submitting guess:", error);
  }
};
</script>

<template>
  <h1>Hangman in Vue v3 (Laravel API backend)</h1>

  <div id="game">
    <canvas ref="canvasRef" width="300" height="300"></canvas>

    <div class="testWord">{{ game.word }}</div>

    <div v-if="isBusy" class="keyboard">
      <button
          v-for="letter in alphabet"
          :key="letter"
          :class="{ used: guessedLetters.includes(letter) }"
          :disabled="guessedLetters.includes(letter)"
          @click="guess(letter)"
      >
        {{ letter }}
      </button>
    </div>
  </div>

  <div class="buttons">
    <button v-if="!isBusy" class="toggleLocale" @click="toggleLocale">
      {{ locale === 'nl' ? 'Nederlands' : 'English' }}
    </button>
    <h2 class="text-center bg-yellow" v-else>
      Current game in: {{ locale === 'nl' ? 'Nederlands' : 'English' }}
    </h2>


    <button v-if="!isBusy" class="startNew" @click="startNewGame">
      {{ game.status === 'idle' ? 'Start Game' : 'Play Again' }}
    </button>
  </div>

  <div v-if="game.status === 'fail'" class="gameOver" @click="startNewGame">
    <h2>{{ game.error || 'GAME OVER' }}</h2>
    <h3>Click to try again.</h3>
  </div>

  <div v-if="game.status === 'success'" class="youWon" @click="startNewGame">
    <h2>Victory!</h2>
    <h3>Great job! Click to play again.</h3>
  </div>
</template>
