import { createRouter, createWebHistory } from "vue-router";
import NotFoundView from "../views/NotFoundView.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/login",
      name: "login",
      component: () => import("../views/LoginView.vue"),
    },
    {
      path: "/callback",
      name: "callback",
      component: () => import("../views/CallbackView.vue"),
    },
    {
      path: "/",
      name: "home",
      component: () => import("../views/HomeView.vue"),
      meta: { requiresAuth: true }, // add meta field to specify the route requires authentication
    },
    {
      path: "/test",
      name: "test",
      component: () => import("../views/TestView.vue"),
      meta: { requiresAuth: true }, // add meta field to specify the route requires authentication
    },
    {
      path: "/department",
      name: "department",
      component: () => import("../views/DepartmentView.vue"),
      meta: { requiresAuth: true }, // add meta field to specify the route requires authentication
    },
    {
      path: "/direction", 
      name: "direction",
      component: () => import("../views/DirectionView.vue"),
      meta: { requiresAuth: true }, // add meta field to specify the route
    },
    {
      path: "/pensumType", 
      name: "pensumType",
      component: () => import("../views/PensumTypeView.vue"),
      meta: { requiresAuth: true }, // add meta field to specify the route
    },
    {
      path: "/group", 
      name: "group",
      component: () => import("../views/GroupView.vue"),
      meta: { requiresAuth: true }, // add meta field to specify the route
    },
    {
      path: "/relative",
      name: "relative",
      component: () => import("../views/RelativeView.vue"), 
      meta: { requiresAuth: true }, // add meta field to specify the route
    }, 
    {
      path: "/subject",
      name: "subject",
      component: () => import("../views/SubjectView.vue"), 
      meta: { requiresAuth: true }, // add meta field to specify the route
    },
    {
      path: "/teacher",
      name: "teacher",
      component: () => import("../views/TeacherView.vue"),
      meta: {requiresAuth: true}, // add meta field to specify the route
    }, 
    {
      path: "/college", 
      name: "college", 
      component: () => import("../views/CollegeView.vue"), 
      meta: {requiresAuth: true}, // add meta field to specify the route  
    },
    {
      path: '/cycle',
      name: 'cycle', 
      component: () => import("../views/CycleView.vue"), 
      meta: {requiresAuth: true}, // add meta field to specify the route
    },
    {
      path: '/pensum',
      name: 'pensum', 
      component: () => import("../views/PensumView.vue"), 
      meta: {requiresAuth: true}, // add meta field to specify the route
    },
    {
      path: '/pensumSubjectDetail',
      name: 'pensumSubjectDetail', 
      component: () => import("../views/PensumSubjectDetailView.vue"), 
      meta: {requiresAuth: true}, // add meta field to specify the route
    }, 
    {
      path:'/prerequisite',
      name:'prerequisite',
      component: () => import("../views/PrerequisiteView.vue"),
      meta: {requiresAuth: true}, // add meta field to specify the route

    },
    {
      path:'/evaluation',
      name:'evaluation',
      component: () => import("../views/EvaluationView.vue"),
      meta: {requiresAuth: true}, // add meta field to specify the route
    },
    {
      path: '/municipality',
      name:'municipality',
      component: () => import("../views/MunicipalityView.vue"),
      meta: {requiresAuth: true}  // add meta field to specify the route
    },
    {
      path: "/:pathMatch(.*)*",
      name: "NotFound",
      component: NotFoundView,
    },
  ],
});

// add a global beforeEnter guard to check if the route requires authentication and if the user has an access token
router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !hasAccessToken()) {
    next("/login");
  } else {
    next();
  }
});

// helper function to check if the user has an access token
function hasAccessToken() {
  const token = localStorage.getItem("access_token");
  return token !== null && token !== undefined;
}

export default router;
