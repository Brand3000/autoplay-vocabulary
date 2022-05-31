<script>
export default {
  name: "Study", //name of the component. It's used for the KeepAlive method
};
</script>

<script setup>
import { useGetWord } from "./inc/useGetWord.js";
import { useButtonsOperations } from "./inc/useButtonsOperations.js";
import { ref, watch, onMounted, inject } from "vue";

const usePlayWord = inject("usePlayWord");
const useSetSystemMessage = inject("useSetSystemMessage");
const dictionary = inject("dictionary");
const ajaxWaitFetchRequest = inject("ajaxWaitFetchRequest");

const emits = defineEmits(["componentEmit"]);

let dataFromApp = defineProps(["appMode", "reload"]);

watch(dataFromApp, () => {
  getWord();
});

let word = ref("...");
let wordId = ref(0);
let wordType = ref("");
let isEnWord = ref(false);
let translates = ref([]);
let countPhrasalVerbs = ref(0);
let countIdioms = ref(0);
let countsetPhrases = ref(0);
let extraInfoAboutWord = ref("");
let showTranslates = ref(false); //switcher of translates

onMounted(() => {
  getWord();
});

function getWord() {
  useGetWord(
    countPhrasalVerbs,
    countIdioms,
    countsetPhrases,
    showTranslates,
    word,
    extraInfoAboutWord,
    isEnWord,
    translates,
    dataFromApp.appMode,
    1,
    wordId,
    wordType
  );
}

function buttonsOperations(operation) {
  word.value = "...";
  useButtonsOperations(operation, dataFromApp.appMode, wordId.value).then((result) => {
    if (result.emit !== null) emits("componentEmit", result.emit);
    getWord();
  });
}

function moveToRegular(idToConnectWith = 0) {
  useSetSystemMessage(dictionary["wait_message"]);
  ajaxWaitFetchRequest("/api/movetoregular.php", {
    appMode: dataFromApp.appMode,
    wordId: wordId.value,
    idToConnectWith: idToConnectWith,
  }).then(
    (dataResponse) => {
      useSetSystemMessage();
      if (dataResponse["status"] == "success") {
        emits("componentEmit", "decCntWordsToStudy");
        emits("componentEmit", "minusOneWordToRepeat");
        getWord();
      } else if (dataResponse["existingWord"]) {
        let msgToConfirm =
          '"' +
          dataResponse["existingWord"]["word"] +
          '"' +
          " is in existance with id " +
          dataResponse["existingWord"]["id"];
        for (let i = 1; i <= 5; i++) {
          if (dataResponse["existingWord"]["v" + i]) {
            msgToConfirm += "\n" + dataResponse["existingWord"]["v" + i];
          }
        }
        msgToConfirm += "\nDo you want to combine?";
        if (confirm(msgToConfirm)) {
          moveToRegular(dataResponse["existingWord"]["id"]);
        }
      }
    },
    (error) => {
      console.error(error);
      useSetSystemMessage(error.message);
    }
  );
}
</script>

<template>
  <div class="words-container words-container__flex">
    <div>
      <span @click="showTranslates = 1"
        >{{ word }} <span v-if="word !== '...'">{{ extraInfoAboutWord }}</span></span
      >
      <span class="play" v-if="isEnWord && word !== '...'" @click="usePlayWord(word)"
        >►</span
      >
      <button v-if="word === '...'" @click="getWord" style="margin-left: 10px">
        reload
      </button>
      <div class="translate-qty" v-if="translates.length > 1 && word !== '...'">
        {{ translates.length }} translates
      </div>
    </div>
    <div
      class="edit-close-button"
      v-if="word !== '...'"
      @click="
        $emit('componentEmit', 'fillTranslates', {
          wordId: wordId,
          word: word,
          wordType: wordType,
          translates: translates,
        })
      "
    >
      Edit
    </div>
  </div>
  <div
    class="translate"
    v-show="word !== '...' && translates.length > 0 && showTranslates"
  >
    <div class="other-translates" v-for="translate in translates" :key="translate.word">
      {{ translate.word }}
      <span class="play" v-if="translate.isEnWord" @click="usePlayWord(translate.word)"
        >►</span
      >
      <div class="translate-details" v-if="translate.description">
        <span v-html="'- ' + translate.description"></span>
      </div>
    </div>
  </div>
  <div class="buttons" v-show="word !== '...' && translates.length > 0 && showTranslates">
    <button class="action-button studyiknow" @click="buttonsOperations('studyiknow')">
      I know
    </button>
    <button class="action-button learn" @click="buttonsOperations('studylearn')">
      Learn
    </button>
    <button
      class="action-button toregular"
      v-if="dataFromApp.appMode == 1"
      @click="moveToRegular(0)"
    >
      Move to regular
    </button>
  </div>
</template>

<style>
.studyiknow {
  background-color: lightgreen;
  width: 48%;
  margin-left: 1%;
  margin-right: 1%;
}
.learn {
  background-color: tomato;
  width: 48%;
  margin-left: 1%;
  margin-right: 1%;
}
.toregular {
  background-color: lightskyblue;
  color: #fff;
  width: 75%;
  margin: 24px auto 0 auto;
}
</style>
