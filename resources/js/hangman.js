import "./bootstrap";

import "../css/hangman.css";
import { createApp } from 'vue'
import Hangman from './Components/Hangman.vue'

createApp(Hangman).mount('#hangman')
