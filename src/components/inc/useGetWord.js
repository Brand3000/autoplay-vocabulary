import { dictionary } from "./dictionary.js";
import { useSetSystemMessage } from "./useSetSystemMessage.js";
import { usePlayWord } from "./usePlayWord.js";
import { ajaxWaitFetchRequest } from "./ajaxWaitFetchRequest.js";
import { resolveComponent } from "vue";

export const useGetWord = async (countPhrasalVerbs, countIdioms, countsetPhrases, showTranslates, word, extraInfoAboutWord, isEnWord, translates, appMode, study, wordId, wordType) => {
  countPhrasalVerbs.value = countIdioms.value = countsetPhrases.value = 0; //reset quantity of special translates
  showTranslates.value = false; //hide the container with translates
  word.value = "...";
  extraInfoAboutWord.value = "";
  isEnWord.value = false;
  translates.value = [];
  useSetSystemMessage(dictionary["wait_message"]);  
  return await ajaxWaitFetchRequest("/api/getword.php", {
    appMode: appMode,
    study: study,
  }).then(
    (data) => {
      useSetSystemMessage();
      if (data["mess"]) {
        useSetSystemMessage(data["mess"]);
        return "updateStatistics";
      }
      if (data["status"] == "continue") {
        word.value = data["word"];
        wordId.value = data["id"];
        wordType.value = data["wordtype"];
        let tmp = word.value.charCodeAt(0);
        if ((tmp >= 65 && tmp <= 90) || (tmp >= 97 && tmp <= 122)) {
          isEnWord.value = true;
        }
        translates.value = [];
        for (let i = 1; i <= 5; i++) {
          if (data["v" + i]) {
            let newTranslate = [];
            newTranslate["word"] = data["v" + i];
            tmp = data["v" + i].charCodeAt(0);
            if ((tmp >= 65 && tmp <= 90) || (tmp >= 97 && tmp <= 122))
              newTranslate["isEnWord"] = 1;
            if (data["v" + i + "d"])
              newTranslate["description"] = data["v" + i + "d"].replace(/\n/g, "<br />");
            newTranslate["wordtype"] = data["wordtype" + i];
            translates.value.push(newTranslate);
            switch (data["wordtype" + i]) {
              case "phrasalverb":
                countPhrasalVerbs.value++;
                break;
              case "setphrase":
                countsetPhrases.value++;
                break;
              case "idiom":
                countIdioms.value++;
                break;
            }
          }
        }
        extraInfoAboutWord.value +=
          countPhrasalVerbs.value > 0 ? countPhrasalVerbs.value + "pv" : "";
        extraInfoAboutWord.value +=
          extraInfoAboutWord.value && countIdioms.value > 0 ? "," : "";
        extraInfoAboutWord.value += countIdioms.value > 0 ? countIdioms.value + "i" : "";
        extraInfoAboutWord.value +=
          extraInfoAboutWord.value && countsetPhrases.value > 0 ? "," : "";
        extraInfoAboutWord.value +=
          countsetPhrases.value > 0 ? countsetPhrases.value + "sp" : "";
        if (extraInfoAboutWord.value !== "")
          extraInfoAboutWord.value = " (" + extraInfoAboutWord.value + ")";
      }
    },
    (error) => {
      console.error(error);
      useSetSystemMessage(error.message);
    }
  ).then((msg) => {
    return msg;
  });
}
