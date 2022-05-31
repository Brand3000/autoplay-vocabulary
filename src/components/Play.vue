<script>
export default {
  name: "Play", //name of the component. It's used for the KeepAlive method
};
</script>

<script setup>
import { ajaxWaitPutRequest } from "./inc/ajaxWaitPutRequest.js";
import { ref, watch, onUnmounted, onMounted, inject } from "vue";

const useSetSystemMessage = inject("useSetSystemMessage");
const dictionary = inject("dictionary");
const ajaxWaitFetchRequest = inject("ajaxWaitFetchRequest");

const emits = defineEmits(["componentEmit"]);

let timeOutHandler = null;
function clearAllTimeOuts() {
  clearTimeout(timeOutHandler);
}

let componentStatus = "unmounted";
onUnmounted(() => {
  componentStatus = "unmounted";
  clearAllTimeOuts();
});

onMounted(() => {
  componentStatus = "mounted";
  getPlayedWordsStatistic();
  playNextWord();
});

let dataFromApp = defineProps(["appMode", "reload"]);

let wordId = 0;
watch(dataFromApp, () => {
  wordId = 0;
  getPlayedWordsStatistic();
  playNextWord();
});

let word = ref("...");
let translates = ref([]);
let otherWords = ref("");
function playNextWord() {
  document.querySelector(".learn-from-play").style.display = "table";
  document.querySelector(".word-translates").style.display = "none";
  clearAllTimeOuts();
  otherWords.value = "";
  word.value = "...";
  translates.value = [];
  useSetSystemMessage(dictionary["wait_message"]);
  ajaxWaitFetchRequest("/api/playnextword.php", {
    appMode: dataFromApp.appMode,
    wordId: wordId,
  }).then(
    (data) => {
      useSetSystemMessage();
      if (data["mess"]) {
        useSetSystemMessage(data["mess"]);
        document.querySelector(".learn-from-play").style.display = "none";
      } else {
        word.value = data["word"];
        wordId = data["id"];
        translates.value = [];
        for (let i = 1; i <= 5; i++) {
          if (data["v" + i]) {
            if (otherWords.value) otherWords.value += ",";
            otherWords.value += data["v" + i];
            let newTranslate = [];
            newTranslate["word"] = data["v" + i];
            if (data["v" + i + "d"])
              newTranslate["description"] = data["v" + i + "d"].replace(/\n/g, "<br />");
            translates.value.push(newTranslate);
          }
        }
        playCurrentWord(word.value, otherWords.value, data["mess"]);
      }
    },
    (error) => {
      console.error(error);
      useSetSystemMessage(error.message);
    }
  );
}

let audio = new Audio();
let previousWord = "";
let previousAudioFile = "";
function playCurrentWord(word, otherWords = null, message = null) {
  clearAllTimeOuts();
  if (word != "-" && previousWord != word) {
    useSetSystemMessage(dictionary["wait_message"]);
    ajaxWaitFetchRequest("/api/playword.php", { word: word }).then(
      (data) => {
        if (data["audioFile"] != "") {
          previousWord = word;
          previousAudioFile = data["audioFile"];
          pronounceCurrentWord(data["audioFile"], otherWords);
        } else {
          useSetSystemMessage("an emty audio file");
        }
      },
      (error) => {
        console.error(error);
        useSetSystemMessage(error.message);
      }
    );
  } else if (word != "-" && previousWord == word) {
    //works if clicked twice on the same word
    useSetSystemMessage(dictionary["wait_message"]);
    pronounceCurrentWord(null, otherWords);
  }
}

function pronounceCurrentWord(audioFile = null, otherWords) {
  if (audioFile !== null) audio.src = audioFile;
  let playStatus = audio.play();
  if (playStatus !== undefined) {
    playStatus
      .then(() => {
        audio.onended = function () {
          useSetSystemMessage();
          if (componentStatus === "mounted") {
            timeOutHandler = setTimeout(() => {
              if (otherWords != null) {
                playCurrentWord(otherWords);
                document.querySelector(".word-translates").style.display = "block";
              } else {
                cntWordsToPlay.value--;
                playNextWord();
              }
            }, 3000); //5 sec to remember
          }
        };
      })
      .catch(() => {
        useSetSystemMessage(dictionary["press_the_play_message"]);
      });
  }
}

function learn() {
  document.querySelector(".learn-from-play").style.display = "none";
  ajaxWaitPutRequest("/api/learn.php", {
    appMode: dataFromApp.appMode,
    wordId: wordId,
  }).then(
    () => {
      emits("componentEmit", "incCntWordsToStudy");
    },
    (error) => {
      console.error(error);
      useSetSystemMessage(error.message);
    }
  );
}

let cntAllWords = ref(0);
let cntWordsToPlay = ref(0);
function getPlayedWordsStatistic() {
  ajaxWaitFetchRequest("/api/getplayedwordsstatistic.php", {
    appMode: dataFromApp.appMode,
  }).then(
    (data) => {
      cntAllWords.value = data["cntAllWords"];
      cntWordsToPlay.value = data["cntWordsToPlay"];
    },
    (error) => {
      console.error(error);
      useSetSystemMessage(error.message);
    }
  );
}
</script>

<template>
  <div class="words-container">
    {{ word }}
    <span class="play" v-if="word !== '...'" @click="playCurrentWord(word, otherWords)"
      >►</span
    >
    <button v-if="word === '...'" @click="playNextWord" style="margin-left: 10px">
      reload
    </button>
    <div class="translate-qty" v-if="translates.length > 1">
      {{ translates.length }} translates
    </div>
  </div>
  <div class="translate word-translates">
    <div class="other-translates" v-for="translate in translates" :key="translate.word">
      {{ translate.word }}
      <div class="translate-details" v-if="translate.description">
        <span v-html="'- ' + translate.description"></span>
      </div>
    </div>
  </div>
  <div class="autoPayedStatistic">
    Total: {{ cntAllWords }} / Left: {{ cntWordsToPlay }}
  </div>
  <button class="action-button learn learn-from-play" @click="learn">Learn</button>
</template>

<style>
.learn-from-play {
  width: 200px;
  height: 200px;
  margin: 20px auto 0 auto;
  display: table;
  font-size: 2rem;
}
.autoPayedStatistic {
  margin: 10px 0;
  text-align: center;
  font-weight: bold;
}
</style>
