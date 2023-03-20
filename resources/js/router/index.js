import { setupLayouts } from 'virtual:generated-layouts'
import { createRouter, createWebHistory } from 'vue-router'
import { isUserLoggedIn } from './utils'
import routes from '~pages'
import { canNavigate } from '@layouts/plugins/casl'
import {ability} from "@/plugins/casl/ability";
import {createAbility} from "@/plugins/casl/ability";
import {useAbility} from "@casl/vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // ℹ️ We are redirecting to different pages based on role.
    // NOTE: Role is just for UI purposes. ACL is based on abilities.
    {
      path: '/',
      redirect: to => {
        const userData = localStorage.getItem('userData');
        const parsedUserData = userData ? JSON.parse(userData) : {};
        const userRole = parsedUserData.role ? parsedUserData.role : null;
        if (userRole === 'admin')
          return { name: 'dashboards-analytics' }
        if (userRole === 'client')
          return { name: 'access-control' }

        return { name: 'login', query: to.query }
      },
    },
    {
      path: '/pages/user-profile',
      redirect: () => ({ name: 'pages-user-profile-tab', params: { tab: 'profile' } }),
    },

    {
      path: '/pages/account-settings',
      redirect: () => ({ name: 'pages-account-settings-tab', params: { tab: 'account' } }),
    },
    ...setupLayouts(routes),
  ],
})


// Docs: https://router.vuejs.org/guide/advanced/navigation-guards.html#global-before-guards
router.beforeEach((to, from, next) => {
  const isLoggedIn = isUserLoggedIn()

  if (canNavigate(to)) {
    if (to.meta.redirectIfLoggedIn && isLoggedIn) {
      next('/')
    } else {
      next()
    }
  } else {
    if (isLoggedIn) {
      next({ name: 'not-authorized' })
    } else {
      next({ name: 'login', query: { to: to.name !== 'index' ? to.fullPath : undefined } })
    }
  }
})

export default router




