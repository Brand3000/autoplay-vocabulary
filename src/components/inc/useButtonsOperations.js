import { useSetSystemMessage } from "./useSetSystemMessage.js";
import { ajaxWaitPutRequest } from "./ajaxWaitPutRequest.js";

export const useButtonsOperations = async (operation, appMode, wordId) => {
  return ajaxWaitPutRequest("/api/" + operation + ".php", {
    appMode: appMode,
    wordId: wordId,
  }).then(
    () => {
      if (operation === "iknow") return { 'emit': "changeRepeatedCnt"};
      else if (operation === "learn") return { 'emit': "incCntWordsToStudy"};
      else if (operation === "studyiknow") return { 'emit': "decCntWordsToStudy"};
      else return { 'emit': null};
    },
    (error) => {
      console.error(error);
      useSetSystemMessage(error.message);
    }
  );  
}