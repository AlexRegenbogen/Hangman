<script setup>
import {computed, onMounted, ref, watch} from "vue";
import axios from "axios";
import {useHangmanCanvas} from "../Composables/useHangmanCanvas";
import {useToast} from "vue-toast-notification";

import flagDutch from '../../images/nl.svg';
import flagEnglish from '../../images/gb.svg';

import { useI18n } from 'vue-i18n';
const { t, locale: i18nLocale } = useI18n();

const {canvasRef, initContext, clear, drawStand, updateVisuals} = useHangmanCanvas();

const props = defineProps({
  initialGame: {
    type: Object,
    default: () => ({status: 'idle', word: '', tries_left: 6})
  }
});

const alphabet = [[...'qwertyuiop'], [...'asdfghjkl'], [...'zxcvbnm']];
const game = ref(props.initialGame);
const guessedLetters = ref([]);
const locale = ref('en');

const isBusy = computed(() => game.value.status === 'busy');

const $toast = useToast();

watch(() => props.initialGame, (newGame) => {
  if (newGame) {
    game.value = newGame;
  }
}, {immediate: true});

onMounted(() => {
  initContext();
  drawStand();

  // If we resumed a game, draw the current state of the hangman
  if (game.value.status === 'busy' && game.value.tries_left < 6) {
    for (let i = 6; i > game.value.tries_left; i--) {
      updateVisuals(i - 1);
    }
  }

  window.addEventListener('keydown', event => {
    if (event.key.length === 1 && /[a-z]/i.test(event.key)) {
      guess(event.key.toLowerCase());
    }
  });
});

const startNewGame = async () => {
  const {data} = await axios.post('/api/games', {locale: locale.value})
      .catch((error) => {
        const message = error.response?.data?.message || $t("Error starting new game!");
        $toast.error(message);
      });
  game.value = data.data || data;
  guessedLetters.value = [];

  window.history.pushState({}, '', `/${game.value.id}`);

  clear();
  drawStand();
};

const toggleLocale = () => {
  locale.value = (locale.value === 'en') ? 'nl' : 'en';
  i18nLocale.value = locale.value;
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
    const message = error.response?.data?.message || $t("Error submitting guess!");
    $toast.error(message);
  }
};
</script>

<template>
  <div class="text-2xl dark:text-white text-center">{{ $t('game.title') }}</div>

  <div id="game">
    <canvas ref="canvasRef" width="300" height="300"></canvas>

    <div class="testWord">{{ game.word }}</div>

    <div v-if="isBusy" class="keyboard">
      <div>
        <button
            v-for="letter in alphabet[0]"
            :key="letter"
            :class="{ used: guessedLetters.includes(letter) }"
            :disabled="guessedLetters.includes(letter)"
            @click="guess(letter)"
        >
          {{ letter }}
        </button>
      </div>
      <div>
        <button
            v-for="letter in alphabet[1]"
            :key="letter"
            :class="{ used: guessedLetters.includes(letter) }"
            :disabled="guessedLetters.includes(letter)"
            @click="guess(letter)"
        >
          {{ letter }}
        </button>
      </div>
      <div>
        <button
            v-for="letter in alphabet[2]"
            :key="letter"
            :class="{ used: guessedLetters.includes(letter) }"
            :disabled="guessedLetters.includes(letter)"
            @click="guess(letter)"
        >
          {{ letter }}
        </button>
      </div>
    </div>
  </div>

  <div class="buttons">
    <button v-if="!isBusy" class="toggleLocale" @click="toggleLocale">
      <img
          :src="locale === 'nl' ? flagDutch : flagEnglish"
          :alt="locale"
          class="w-9 h-6 inline-block shadow-sm"
      />
    </button>
    <div class="text-center dark:text-white" v-else>
      {{ $t("game.current_locale") }}
      <img
          :src="locale === 'nl' ? flagDutch : flagEnglish"
          :alt="locale"
          class="w-6 h-4 inline-block shadow-sm"
      />
    </div>


    <button v-if="!isBusy" class="startNew" @click="startNewGame">
      {{ game.status === 'idle' ? 'Start Game' : 'Play Again' }}
    </button>
  </div>

  <div v-if="game.status === 'fail'" class="gameOver" @click="startNewGame">
    <div class="youLost">
      <h2>{{ game.error || $t('game.gameOver') }}</h2>
      <h3>Click to try again.</h3>
    </div>
  </div>

  <div v-if="game.status === 'success'" class="gameOver" @click="startNewGame">
    <div id="fireworks">
      <div class="firework"></div>
      <div class="firework"></div>
      <div class="firework"></div>
    </div>

    <div class="youWon">
      <h2>{{ $t('game.victory') }}</h2>
      <h3>{{ $t('game.click_to_play_again')}}</h3>
    </div>
  </div>
</template>
