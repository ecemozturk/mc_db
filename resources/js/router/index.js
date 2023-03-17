import { setupLayouts } from 'virtual:generated-layouts'
import { createRouter, createWebHistory } from 'vue-router'
import { isUserLoggedIn } from './utils'
import routes from '~pages'
import { canNavigate } from '@layouts/plugins/casl'
import EventEdit from "@/pages/EventEdit.vue";
import CeteleList from "@/pages/Cetele/CeteleList.vue";
import CeteleCreate from "@/pages/Cetele/CeteleCreate.vue";
import CeteleView from "@/pages/Cetele/CeteleView.vue";
import CeteleUpdate from "@/pages/Cetele/CeteleUpdate.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // ℹ️ We are redirecting to different pages based on role.
    // NOTE: Role is just for UI purposes. ACL is based on abilities.
    {
      path: '/',
      redirect: to => {
        const userData = JSON.parse(localStorage.getItem('userData') || '{}')
        const userRole = userData && userData.role ? userData.role : null
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
      path: '/cetele/cetelelist',
      name: 'cetelelist',
      component: CeteleList,
      meta: { requiresAuth: false },
    },
    {
      name: 'cetele-create',
      component: CeteleCreate,
    },
    {
      path: '/cetele/cetele/:id',
      name: 'cetele-details',
      component: CeteleView,
      props: true,
    },
    {
      path: '/cetele/cetele/update/:id',
      name: 'cetele-update',
      component: CeteleUpdate,
      props: true,
    },
    {
      path: '/events/:id/edit',
      name: 'event-edit',
      component: EventEdit,
      props: true
    },
    { path: '/events/:id/update',name: 'event-update', meta: { requiresAuth: true } },

    {
      path: '/pages/account-settings',
      redirect: () => ({ name: 'pages-account-settings-tab', params: { tab: 'account' } }),
    },
    ...setupLayouts(routes),
  ],
})


// Docs: https://router.vuejs.org/guide/advanced/navigation-guards.html#global-before-guards
router.beforeEach(to => {
  const isLoggedIn = isUserLoggedIn()

  /*

    ℹ️ Commented code is legacy code

    if (!canNavigate(to)) {
      // Redirect to login if not logged in
      // ℹ️ Only add `to` query param if `to` route is not index route
      if (!isLoggedIn)
        return next({ name: 'login', query: { to: to.name !== 'index' ? to.fullPath : undefined } })

      // If logged in => not authorized
      return next({ name: 'not-authorized' })
    }

    // Redirect if logged in
    if (to.meta.redirectIfLoggedIn && isLoggedIn)
      next('/')

    return next()

    */
  if (canNavigate(to)) {
    if (to.meta.redirectIfLoggedIn && isLoggedIn)
      return '/'
  }
  else {
    if (isLoggedIn)
      return { name: 'not-authorized' }
    else
      return { name: 'login', query: { to: to.name !== 'index' ? to.fullPath : undefined } }
  }
})
export default router





