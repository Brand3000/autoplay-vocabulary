import { usePlayWord } from "./components/inc/usePlayWord.js";
import { useSetSystemMessage } from "./components/inc/useSetSystemMessage.js";
import { dictionary } from "./components/inc/dictionary.js";
import { ajaxWaitFetchRequest } from "./components/inc/ajaxWaitFetchRequest.js";

import { createApp } from "vue";
import App from "./App.vue";

const app = createApp(App);

app.provide("usePlayWord", usePlayWord);
app.provide("useSetSystemMessage", useSetSystemMessage);
app.provide("dictionary", dictionary);
app.provide("ajaxWaitFetchRequest", ajaxWaitFetchRequest);

app.mount("#app");