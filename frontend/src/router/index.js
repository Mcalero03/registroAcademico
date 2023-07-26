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
      path: "/school",
      name: "school",
      component: () => import("../views/SchoolView.vue"),
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
      path: "/subject",
      name: "subject",
      component: () => import("../views/SubjectView.vue"),
      meta: { requiresAuth: true }, // add meta field to specify the route
    },
    {
      path: "/teacher",
      name: "teacher",
      component: () => import("../views/TeacherView.vue"),
      meta: { requiresAuth: true }, // add meta field to specify the route
    },
    {
      path: "/subSchool",
      name: "subSchool",
      component: () => import("../views/SubSchoolView.vue"),
      meta: { requiresAuth: true }, // add meta field to specify the route  
    },
    {
      path: '/cycle',
      name: 'cycle',
      component: () => import("../views/CycleView.vue"),
      meta: { requiresAuth: true }, // add meta field to specify the route
    },
    {
      path: '/pensum',
      name: 'pensum',
      component: () => import("../views/PensumView.vue"),
      meta: { requiresAuth: true }, // add meta field to specify the route
    },
    {
      path: '/pensumSubjectDetail',
      name: 'pensumSubjectDetail',
      component: () => import("../views/PensumSubjectDetailView.vue"),
      meta: { requiresAuth: true }, // add meta field to specify the route
    },
    {
      path: '/prerequisite',
      name: 'prerequisite',
      component: () => import("../views/PrerequisiteView.vue"),
      meta: { requiresAuth: true }, // add meta field to specify the route

    },
    {
      path: '/evaluation',
      name: 'evaluation',
      component: () => import("../views/EvaluationView.vue"),
      meta: { requiresAuth: true }, // add meta field to specify the route
    },
    {
      path: '/municipality',
      name: 'municipality',
      component: () => import("../views/MunicipalityView.vue"),
      meta: { requiresAuth: true }  // add meta field to specify the route
    },
    {
      path: '/schedule',
      name: 'schedule',
      component: () => import("../views/ScheduleView.vue"),
      meta: { requiresAuth: true } // add meta field to specify the route
    },
    {
      path: '/student',
      name: 'student',
      component: () => import("../views/StudentView.vue"),
      meta: { requiresAuth: true } // add meta field to specify the route
    },
    {
      path: '/inscription',
      name: 'inscription',
      component: () => import("../views/InscriptionView.vue"),
      meta: { requiresAuth: true } // add meta field to specify the route
    }, {
      path: '/attendance',
      name: 'attendance',
      component: () => import("../views/AttendanceView.vue"),
      meta: { requiresAuth: true } // add meta field to specify the route
    },
    {
      path: '/kinship',
      name: 'kinship',
      component: () => import("../views/KinshipView.vue"),
      meta: { requiresAuth: true } // add meta field to specify the route
    },
    {
      path: '/classroom',
      name: 'classroom',
      component: () => import("../views/ClassroomView.vue"),
      meta: { requiresAuth: true } // add meta field to specify the route
    },
    {
      path: '/grades',
      name: 'grades',
      component: () => import("../views/GradesCheckView.vue"),
      meta: { requiresAuth: true } // add meta field to specify the route
    },
    {
      path: '/inscriptionRecord',
      name: 'inscriptionRecord',
      component: () => import("../views/InscriptionRecordView.vue"),
      meta: { requiresAuth: true } // add meta field to specify the route
    },
    {
      path: '/teacherSchedule',
      name: 'teacherSchedule',
      component: () => import("../views/TeacherScheduleView.vue"),
      meta: { requiresAuth: true } // add meta field to specify the route
    },
    {
      path: '/studentSchedule',
      name: 'studentSchedule',
      component: () => import("../views/StudentScheduleView.vue"),
      meta: { requiresAuth: true } // add meta field to specify the route
    },
    {
      path: '/teacherSubject',
      name: 'teacherSubject',
      component: () => import("../views/TeacherSubjectView.vue"),
      meta: { requiresAuth: true } // add meta field to specify the route
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
