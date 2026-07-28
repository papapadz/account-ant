import { defineNuxtRouteMiddleware, navigateTo } from '#app'

export default defineNuxtRouteMiddleware(async (to) => {
  if (import.meta.server) return
  if (to.path === '/login' || to.path === '/register') return

  const auth = useAuth()
  const api = useApi()

  if (!api.token.value) {
    return navigateTo('/login')
  }

  if (!auth.currentUser.value) {
    try {
      await auth.fetchUser()
    } catch {
      auth.logout()
      return navigateTo('/login')
    }
  }
})
