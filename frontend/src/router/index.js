import { createRouter, createWebHistory } from "vue-router";
import homeView from "@/views/homeView.vue";
import StudyView from "@/views/StudyView.vue";
import AddDeckView from "@/views/AddDeckView.vue";

const routes = [
  {
    path: "/",
    name: "Home",
    component: homeView,
  },
  {
    path: "/add-deck",
    name: "AddDeck",
    component: AddDeckView,
  },
  {
    path: "/study/:id",
    name: "Study",
    component: StudyView,
    props: true,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;
