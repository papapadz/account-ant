export type ThemeMode = 'dark' | 'light'

export const useTheme = () => {
  const theme = useState<ThemeMode>('app_theme', () => 'dark')

  const applyTheme = (mode: ThemeMode) => {
    theme.value = mode
    if (import.meta.client) {
      localStorage.setItem('accountant_theme', mode)
      if (mode === 'dark') {
        document.documentElement.classList.add('dark')
        document.documentElement.classList.remove('light')
      } else {
        document.documentElement.classList.remove('dark')
        document.documentElement.classList.add('light')
      }
    }
  }

  const toggleTheme = () => {
    const nextMode = theme.value === 'dark' ? 'light' : 'dark'
    applyTheme(nextMode)
  }

  const initTheme = () => {
    if (import.meta.client) {
      const saved = localStorage.getItem('accountant_theme') as ThemeMode | null
      if (saved === 'dark' || saved === 'light') {
        applyTheme(saved)
      } else {
        applyTheme('dark')
      }
    }
  }

  return {
    theme,
    toggleTheme,
    initTheme,
    isDark: computed(() => theme.value === 'dark'),
  }
}
