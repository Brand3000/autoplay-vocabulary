import { dictionary } from "./dictionary.js";
import { useSetSystemMessage } from "./useSetSystemMessage.js";
import { ajaxWaitFetchRequest } from "./ajaxWaitFetchRequest.js";

let audio = new Audio();
let previousWord = "";
let previousAudioFile = "";
export function usePlayWord(word) {
  if (word != "-" && previousWord != word) {
    useSetSystemMessage(dictionary["wait_message"]);
    ajaxWaitFetchRequest("/api/playword.php", { word: word }).then(
      (data) => {
        previousWord = word;
        previousAudioFile = data["audioFile"];
        if (data["audioFile"] != "") {
          audio.src = data["audioFile"];
          let playStatus = audio.play();
          if (playStatus !== undefined) {
            playStatus
              .then(() => {
                audio.onended = function () {
                  useSetSystemMessage();
                }
              })
              .catch(() => {
                useSetSystemMessage(dictionary["press_the_play_message"]);
              });
          }
        } else {
          useSetSystemMessage("an emty audio file");
        }
      },
      (error) => {
        console.error(error);
      }
    );
  } else if (word != "-" && previousWord == word) {
    //works if clicked twice on the same word
    useSetSystemMessage(dictionary["wait_message"]);
    audio.src = previousAudioFile;
    audio.play().then(() => {
      useSetSystemMessage();
    });
  }
}