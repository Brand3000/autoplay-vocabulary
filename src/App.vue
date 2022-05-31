<script setup>
import TopMenu from "./components/TopMenu.vue";
import Repeat from "./components/Repeat.vue";
import Study from "./components/Study.vue";
import Forget from "./components/Forget.vue";
import Play from "./components/Play.vue";
import NotFound from "./components/NotFound.vue";
import { ref, computed, inject } from "vue";

const useSetSystemMessage = inject("useSetSystemMessage");
const dictionary = inject("dictionary");
const ajaxWaitFetchRequest = inject("ajaxWaitFetchRequest");

let appMode = ref(0); //0 - regular mode; 1 - advanced mode
let reload = ref(0); //use for reloading content of a tab

const routes = {
  "/": Repeat,
  "/study": Study,
  "/forget": Forget,
  "/play": Play,
};

const currentPath = ref(window.location.hash);

window.addEventListener("hashchange", () => {
  useSetSystemMessage(); //clear the message box
  if (currentPath.value == window.location.hash) {
    reload.value++; //reload if was clicked on an active tab
  }
  currentPath.value = window.location.hash;
});

const currentView = computed(() => {
  return routes[currentPath.value.slice(1) || "/"] || NotFound;
});

let cntAllWords = ref(0); //quantity of all the words
let cntEnWords = ref(0); //quantity of en - words
let cntRuWords = ref(0); //quantity of ru - words
let cntRepeatedWords = ref(0); //quantity of repeated words
let cntWordsToStudy = ref(0); //quantity of words to study
let cntLeftWords = ref(0); //quantity of words left to repat

window.addEventListener("load", () => {
  getStatistics();
});

function getStatistics() {
  if (!isUser.value) document.querySelector(".login-message").innerHTML = "";
  //get words statistic
  ajaxWaitFetchRequest("/api/getstatistics.php", {
    appMode: appMode.value,
  }).then(
    (data) => {
      if (data.error !== undefined) {
        document.querySelector(".login-message").innerHTML = data.error.message;
      } else {
        if (!isUser.value) {
          document.querySelector(".please-wait").style.display = "none";
        }
        cntAllWords.value = data["cntAllWords"];
        cntEnWords.value = data["cntEnWords"];
        cntRuWords.value = cntAllWords.value - cntEnWords.value;
        cntRepeatedWords.value = data["cntRepeatedWords"];
        cntLeftWords.value = cntAllWords.value - cntRepeatedWords.value;
        cntWordsToStudy.value = data["cntWordsToStudy"];
        isUser.value = data["isUser"];
        if (!isUser.value) {
          document.querySelector(".login-form").style.display = "table";
        }
      }
    },
    (error) => {
      console.error(error);
    }
  );
}

function toggleAddWordForm() {
  const el = document.querySelector(".add-word");
  const closeButton = document.querySelector(".close-button");
  document.querySelector(".delete-word").style.display = "none";
  if (el.style.display !== "table") {
    el.style.display = "table";
    closeButton.style.display = "block";
    document.querySelector("[type=submit]").style.display = "block";
    scrollTo(document.getElementById("add-word"));
  } else {
    el.style.display = closeButton.style.display = "none";
    document.querySelector(".add-word").reset();
    document.querySelector("[name=wordId]").value = 0;
    document.querySelector("[type=submit]").value = "Add";
  }
}

function addEditWord(idToConnectWith = 0) {
  useSetSystemMessage(dictionary["wait_message"]);
  let dataForm = new FormData(document.querySelector(".add-word"));
  let data = {};
  for (let entry of dataForm) {
    if (entry[1]) data[entry[0]] = entry[1];
  }
  data["appMode"] = appMode.value;
  data["idToConnectWith"] = idToConnectWith;
  ajaxWaitFetchRequest("/api/addeditword.php", data).then(
    (dataResponse) => {
      if (dataResponse["status"] == "success") {
        if (dataResponse["new"]) {
          cntWordsToStudy.value =
            parseInt(cntWordsToStudy.value) + parseInt(dataResponse["cntNewWords"]);
          cntAllWords.value =
            parseInt(cntAllWords.value) + parseInt(dataResponse["cntNewWords"]);
          cntLeftWords.value =
            parseInt(cntLeftWords.value) + parseInt(dataResponse["cntNewWords"]);
        }
        useSetSystemMessage(dictionary["saved"]);
        document.querySelector(".add-word").style.display = "none";
        document.querySelector(".add-word").reset();
        document.querySelector(".close-button").style.display = "none";
        document.querySelector("[name=wordId]").value = 0;
        document.querySelector("[type=submit]").value = "Add";
        document.querySelector(".delete-word").style.display = "none";
        if (dataResponse["newWordWithDuplicate"]["id"]) {
          alert(
            "You've got a duplicate with the word \"" +
              dataResponse["newWordWithDuplicate"]["word"] +
              '", id = ' +
              dataResponse["newWordWithDuplicate"]["id"] +
              ". You have to fix that yourself in the DB."
          );
        }
        if (!dataResponse["new"]) reload.value++;
      } else if (dataResponse["existingWord"]) {
        useSetSystemMessage();
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
          addEditWord(dataResponse["existingWord"]["id"]);
        }
      }
    },
    (error) => {
      console.error(error);
    }
  );
}

function fillTranslates(wordId, word, wordType, translates) {
  document.querySelector("[name=wordId]").value = wordId;
  document.querySelector("[name=word]").value = word;

  translates.forEach((item, index) => {
    if (item.word)
      document.querySelector("[name=translate" + (index + 1) + "]").value = item.word;
    if (item.description)
      document.querySelector(
        "[name=description" + (index + 1) + "]"
      ).value = item.description.replace(/<br \/>/g, "\n");
    if (item.wordtype)
      document.querySelector("[name=wordtype" + (index + 1) + "]").value = item.wordtype;
  });

  const formSubmit = document.querySelector("[type=submit]");
  if (wordId) {
    formSubmit.value = "Save";
    document.querySelector(".delete-word").style.display = "table";
  } else formSubmit.value = "Add";
  formSubmit.style.display = "block";
  document.querySelector(".add-word").style.display = "table";
  document.querySelector(".close-button").style.display = "block";
  scrollTo(document.getElementById("add-word"));
}

const scrollTo = (element) => {
  window.scroll({
    behavior: "smooth",
    left: 0,
    top: element.offsetTop,
  });
  console;
};

function deleteWord() {
  if (confirm("Are you sure")) {
    document.querySelector("[type=submit]").style.display = "none";
    document.querySelector(".delete-word").style.display = "none";
    ajaxWaitFetchRequest("/api/deleteword.php", {
      appMode: appMode.value,
      wordId: document.querySelector("[name=wordId]").value,
    }).then(
      () => {
        cntAllWords.value--;
        cntLeftWords.value--;
        if (currentView.value["name"] == "Study") cntWordsToStudy.value--;
        toggleAddWordForm();
        reload.value++;
      },
      (error) => {
        console.error(error);
      }
    );
  }
}

let isUser = ref(false);
isUser.value = false;
function login() {
  document.querySelector(".login-message__login").innerHTML = "";
  ajaxWaitFetchRequest("/api/login.php", {
    login: document.querySelector("[name=login]").value,
    pwd: document.querySelector("[name=pwd]").value,
  }).then(
    (data) => {
      if (data["error"]) {
        document.querySelector(".login-message__login").innerHTML =
          data["error"]["message"];
      } else if (!data.isUser) {
        document.querySelector(".login-message__login").innerHTML =
          "Login or password is incorrect";
      }
      isUser.value = data.isUser;
      getStatistics();
    },
    (error) => {
      console.error(error);
    }
  );
}

function componentEmit(emitName, variables) {
  switch (emitName) {
    case "updateStatistics":
      getStatistics();
      break;
    case "fillTranslates":
      fillTranslates(
        variables["wordId"],
        variables["word"],
        variables["wordType"],
        variables["translates"]
      );
      break;
    case "decCntWordsToStudy":
      cntWordsToStudy.value--;
      break;
    case "minusOneWordToRepeat":
      cntLeftWords.value--;
      cntAllWords.value--;
      break;
    case "incCntWordsToStudy":
      cntWordsToStudy.value++;
      break;
    case "changeRepeatedCnt":
      cntRepeatedWords.value++;
      cntLeftWords.value--;
      break;
  }
}
</script>

<template>
  <div v-if="isUser" style="padding-bottom: 100px">
    <TopMenu :hash="currentPath" />
    <KeepAlive exclude="Forget,Play">
      <component
        :is="currentView"
        :appMode="appMode"
        :reload="reload"
        @component-emit="componentEmit"
      />
    </KeepAlive>

    <br /><br />
    <div class="statistic-info container">
      Всего слов: {{ cntAllWords }} (en: {{ cntEnWords }}, ru: {{ cntRuWords }})
      <br />
      Повторено: {{ cntRepeatedWords }}, осталось: {{ cntLeftWords - cntWordsToStudy }}
      <br />
      На изучении: {{ cntWordsToStudy }}
    </div>
    <div class="bottomFixed">
      <div class="message" id="message"></div>
      <button
        class="mode-switcher"
        :class="{ 'advanced-mode': appMode }"
        @click="
          appMode = !appMode;
          getStatistics();
        "
      >
        Switch to {{ appMode == 0 ? "advanced" : "regular" }}
      </button>
    </div>
    <div class="words-container" id="add-word">
      <div class="words-container__flex">
        <div @click="toggleAddWordForm">Add/edit a word</div>
        <div class="edit-close-button close-button" @click="toggleAddWordForm">Close</div>
      </div>
      <form class="add-word" @submit.prevent="addEditWord(0)">
        <input type="hidden" name="wordId" value="0" />
        <input type="text" name="word" placeholder="word" required />
        <div v-for="i in 5" :key="i.index" style="margin-top: 15px">
          <input
            type="text"
            :name="'translate' + i"
            :placeholder="'translate ' + i"
            v-if="i == 1"
            required
            style="width: 69%; float: left"
          /><input
            type="text"
            :name="'translate' + i"
            :placeholder="'translate ' + i"
            v-if="i > 1"
            style="width: 69%; float: left"
          />
          <select :name="'wordtype' + i" style="width: 29%; float: right">
            <option value="0" selected>extra information if it's needed</option>
            <option value="phrasalverb">Phrasal verb</option>
            <option value="setphrase">set phrase</option>
            <option value="idiom">idiom</option>
          </select>
          <div style="clear: both; padding-top: 1px">
            <textarea
              :name="'description' + i"
              :placeholder="'description ' + i"
            ></textarea>
          </div>
        </div>
        <input type="submit" class="action-button" value="Add" />
      </form>
      <button class="delete-word" @click="deleteWord">Delete word</button>
    </div>
  </div>
  <div v-else>
    <div class="login-message__login"></div>
    <form method-="post" class="login-form" @submit.prevent="login">
      <input type="text" name="login" placeholder="login" autocomplete="username" /><br />
      <input
        type="password"
        name="pwd"
        placeholder="password"
        autocomplete="current-password"
      /><br />
      <input
        type="submit"
        value="Log in"
        style="background-color: lightgreen; color: black"
      />
    </form>
    <div class="please-wait">
      <div class="login-message"></div>
      {{ dictionary["wait_message"] }}<br />
      <button @click="getStatistics()">Reload</button>
    </div>
  </div>
</template>

<style>
* {
  box-sizing: border-box;
  outline: none;
}

body {
  font-family: Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  margin: 0;
  font-size: 16px;
  color: #2c3e50;
}
a {
  color: #2c3e50;
  text-decoration: none;
}
input,
select,
textarea {
  font-family: Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
::-webkit-input-placeholder {
  /* Chrome/Opera/Safari */
  font-family: Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
::-moz-placeholder {
  /* Firefox 19+ */
  font-family: Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
:-ms-input-placeholder {
  /* IE 10+ */
  font-family: Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
:-moz-placeholder {
  /* Firefox 18- */
  font-family: Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
.container {
  padding: 0 15px;
}
.statistic-info {
  font-weight: bold;
  margin-top: 20px;
}
.mode-switcher {
  width: 70%;
  height: 40px;
  border: 0;
  background-color: tomato;
  color: white;
  margin-left: 15%;
  margin-right: 15%;
  border-radius: 5px;
  margin-top: 6px;
}
.advanced-mode {
  background-color: lightskyblue;
}
.message,
.login-message,
.login-message__login {
  color: red;
  text-align: center;
}
.bottomFixed {
  position: fixed;
  bottom: 0;
  width: 100%;
  background-color: #fff;
  padding-top: 8px;
}
.edit-close-button {
  padding-left: 10px;
  color: cornflowerblue;
  cursor: pointer;
  font-size: 0.9rem;
}
.close-button {
  display: none;
}
.add-word {
  display: none;
  margin: 0 auto;
  width: 100%;
}
input[type="text"],
input[type="password"],
input[type="submit"],
select,
textarea {
  width: 100%;
  height: 30px;
  margin-top: 5px;
  border-radius: 5px;
  border: 1px solid grey;
  padding: 0 10px;
  display: block;
  color: #2c3e50;
  background-color: #fff;
  font-size: 0.95rem;
}
input[type="submit"] {
  background-color: lightgreen;
  color: #2c3e50;
  width: 50%;
  margin: 10px auto;
}
.add-word textarea {
  height: 70px;
  padding: 5px 10px;
}
@media (min-width: 640px) {
  .add-word {
    max-width: 640px;
  }
}
.delete-word {
  display: none;
  margin: 10px auto 0 auto;
}
.login-form {
  display: none;
  margin: 0 auto;
  text-align: center;
  margin-top: 10px;
}
.login-message,
.login-message__login {
  margin-top: 100px;
}
.please-wait {
  display: table;
  margin: 50px auto 0 auto;
  text-align: center;
}
</style>
