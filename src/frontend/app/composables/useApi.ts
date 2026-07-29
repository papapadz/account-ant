import type { FetchOptions } from 'ofetch'

export const useApi = () => {
  const config = useRuntimeConfig()
  const apiBase = config.public.apiBase || 'http://localhost:8000/api'
  const tokenCookie = useCookie<string | null>('auth_token', { default: () => null })

  const request = async <T>(endpoint: string, options: FetchOptions = {}): Promise<T> => {
    const headers: Record<string, string> = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      ...(options.headers as Record<string, string> || {}),
    }

    if (tokenCookie.value) {
      headers['Authorization'] = `Bearer ${tokenCookie.value}`
    }

    try {
      const response = await $fetch<T>(`${apiBase}${endpoint}`, {
        ...options,
        headers,
      })
      return response
    } catch (error: any) {
      console.warn(`[API Call Warning] ${endpoint}:`, error?.message || error)
      throw error
    }
  }

  return {
    apiBase,
    token: tokenCookie,
    request,
  }
}
