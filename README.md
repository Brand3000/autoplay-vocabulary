# Autoplay vocabulary

This application will help you repeat and remember words of the language you learn.<br>
Being a learner of English, I am convinced that the first words you need to learn are the words that you couldn't remember in your last talk.
90% that you will need those words again shortly in your next conversations. Having those words learned, you will be more confident.<br>
In my app, you should add new words yourself. I recommend using the Collins dictionary or the Cambridge dictionary in order to find explanations and ways of using the words.
https://www.collinsdictionary.com/ and https://dictionary.cambridge.org/ respectively.<br>
Whenever you're adding a new word, its mirror translation will be added automatically.<br>
After you have added words, you have the next features:
1. <b>The tab Repeat.</b><br>
This tab consists of the words that you are supposed to have remembered the best. You can use this tab just for repeating words.<br>
By looking at a word, you have to recall its translation in your mind. Then you can check yourself by pressing on the original word.<br>
If you can't remember a word, you can move it to the Study tab by pressing the button "Learn".<br>
If you don't remember a word, but don't want to move it to the Study tab, you can put it in the queue for repeating in the future by pressing the button "Repeat".
2. <b>The tab Study.</b><br>
This tab consists of the words that you are studying. After adding a word, it'll appear on this tab first.<br>
By looking at a word, you have to recall its translation in your mind. Then you can check yourself by pressing on the original word.<br>
When you think that you have remembered a word strongly, you can move this word to the Repeat tab by pressing the button "I know".<br>
If you don't remember a word, press the button "Learn".
3. <b>The tab Forget.</b><br>
This tab comprises all the words from the tab Study, but the words are shown as one list. You can use this tab for quick looks at the list if you need.
4. <b>The tab Play.</b><br>
This is the most incredible tab of the app. It plays your words automatically in circles. It means that you can relax, run, walk and repeat your words along the way. All the words will be pronounced by the app. After pronouncing a word, you have 5 seconds to recall its translation in your mind. After 5 seconds the app will pronounce the translation.<br>
This tab has a big button "Learn". You can press this button for moving a word to the tab "Study".

Each tab will fetch new words one by one until they are in the queue for drilling. Then the app will shuffle the words and start fetching them again.<br>
Look at several screenshots from the app:
![Screenshots](/screenshots.jpg)

## Quick start if you are a learner

If you are just a learner, you shouldn't dive deeply into the source. All that you need is the dist folder and a PHP+MySQL hosting.
You have to complete the next steps:
1. Copy all the files from the dist folder to your hosting server.
2. Create a database and run dump.sql.
3. Put the credentials from your database into the api/_init.php file.
4. Define login and password in order to get access to the app.
5. To get your words pronounced, you have to obtain the X-Goog-Api-Key from the Text-to-Speach feature by Google. Look at the screenshot below:
![Text to speach credentials](/text-to-speach.jpg)

That's it. You don't need to read the rest information on this page if you are not a developer and don't want to contribute to the project.

## Project Setup

If you want to improve the app, do the next:
```sh
npm install
```

## Recommended IDE Setup

[VSCode](https://code.visualstudio.com/) + [Volar](https://marketplace.visualstudio.com/items?itemName=johnsoncodehk.volar) (and disable Vetur) + [TypeScript Vue Plugin (Volar)](https://marketplace.visualstudio.com/items?itemName=johnsoncodehk.vscode-typescript-vue-plugin).
