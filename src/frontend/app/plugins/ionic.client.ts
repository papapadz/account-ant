import { defineNuxtPlugin } from '#app'
import { defineCustomElements } from '@ionic/core/loader'

export default defineNuxtPlugin(() => {
  if (import.meta.client && typeof window !== 'undefined') {
    try {
      defineCustomElements(window).catch(() => {})
    } catch {
      // Ignore hydration error
    }
  }
})
