<script>
export default {
  name: "Forget", //name of the component. It's used for the KeepAlive method
};
</script>

<script setup>
import { ref, watch, onMounted, inject } from "vue";

const usePlayWord = inject("usePlayWord");
const useSetSystemMessage = inject("useSetSystemMessage");
const dictionary = inject("dictionary");
const ajaxWaitFetchRequest = inject("ajaxWaitFetchRequest");

const emits = defineEmits(["componentEmit"]);

let dataFromApp = defineProps(["appMode", "reload"]);

watch(dataFromApp, () => {
  getForgottenWords();
});

onMounted(() => {
  getForgottenWords();
});

let forgottenWords = ref([]);
function getForgottenWords() {
  useSetSystemMessage(dictionary["wait_message"]);
  ajaxWaitFetchRequest("/api/getforgottenwords.php", {
    appMode: dataFromApp.appMode,
  }).then(
    (data) => {
      useSetSystemMessage();
      forgottenWords.value = data["words"];
      forgottenWords.value.forEach((item, index) => {
        let tmp = item["word"].charCodeAt(0);
        if ((tmp >= 65 && tmp <= 90) || (tmp >= 97 && tmp <= 122)) {
          forgottenWords.value[index]["isEnWord"] = true;
        }
        for (let i = 1; i <= 5; i++) {
          if (item["v" + i]) {
            tmp = item["v" + i].charCodeAt(0);
            if ((tmp >= 65 && tmp <= 90) || (tmp >= 97 && tmp <= 122))
              forgottenWords.value[index]["v" + i + "isEnWord"] = 1;
            if (item["v" + i + "d"])
              forgottenWords.value[index]["v" + i + "d"] = item["v" + i + "d"].replace(
                /\n/g,
                "<br />"
              );
          }
        }
      });
    },
    (error) => {
      console.error(error);
      useSetSystemMessage(error.message);
    }
  );
}

function toggleScreen(id) {
  const el = document.getElementById("translates-" + id);
  if (el.style.display !== "block") el.style.display = "block";
  else el.style.display = "none";
}
</script>

<template>
  <div v-for="word in forgottenWords" :key="word.id">
    <div class="words-container" style="padding: 5px 15px">
      <span @click="toggleScreen(word.id)">{{ word.word }}</span>
      <span class="play" v-if="word.isEnWord" @click="usePlayWord(word.word)">►</span>
      <div class="translates" :id="'translates-' + word.id">
        <div class="translate-details" v-for="i in 5" :key="i.index">
          <div v-if="word['v' + i]">
            {{ word["v" + i] }}
            <span
              class="play"
              v-if="word['v' + i + 'isEnWord']"
              @click="usePlayWord(word['v' + i])"
              >►</span
            >
            <div class="other-translates" v-if="word['v' + i + 'd']">
              <div class="translate-details">
                <span v-html="'- ' + word['v' + i + 'd']"></span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.translates {
  display: none;
  font-weight: normal;
}

.translates .translate-details {
  margin-top: 5px;
}
</style>
