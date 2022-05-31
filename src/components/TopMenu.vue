<script setup>
import { computed, inject } from "vue";

const useSetSystemMessage = inject("useSetSystemMessage");

let hashFromApp = defineProps(["hash"]);

function changehash(activeTab) {
  useSetSystemMessage();
  if (activeTab) window.dispatchEvent(new Event("hashchange")); //force click. used for active tabs to refresh
}

const activeFirstTab = computed(() => {
  //return true if the first tab is active
  const firstTabUrls = ["#/repeat", "#/", ""];
  return firstTabUrls.includes(hashFromApp.hash);
});
</script>

<template>
  <div class="top-menu">
    <a
      href="#/"
      :class="{
        active: activeFirstTab,
      }"
      @click="changehash(activeFirstTab)"
      >Repeat</a
    >
    <a
      href="#/study"
      :class="{ active: hashFromApp.hash == '#/study' }"
      @click="changehash(hashFromApp.hash == '#/study')"
    >
      Study
    </a>
    <a
      href="#/forget"
      :class="{ active: hashFromApp.hash == '#/forget' }"
      @click="changehash(hashFromApp.hash == '#/forget')"
      >Forget</a
    >
    <a
      href="#/play"
      :class="{ active: hashFromApp.hash == '#/play' }"
      @click="changehash(hashFromApp.hash == '#/play')"
      >Play</a
    >
  </div>
</template>

<style>
.top-menu {
  display: flex;
  justify-content: center;
  margin-bottom: 10px;
}
.top-menu a {
  flex-grow: 1;
  padding: 12px 0;
  background-color: #d2d2d2;
  font-weight: bold;
  text-align: center;
}
.top-menu a.active {
  background-color: #fff;
}
</style>
