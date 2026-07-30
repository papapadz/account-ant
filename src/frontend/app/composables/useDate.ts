export type DateInput = Date | string | number | null | undefined

export const useDate = () => {
  const locale = useState<string>('app_locale', () => 'en-US')

  // Cache formatter instances to prevent repeated instantiation overhead
  const formatterCache = new Map<string, Intl.DateTimeFormat>()
  const relativeCache = new Map<string, Intl.RelativeTimeFormat>()

  const getCachedFormatter = (key: string, options?: Intl.DateTimeFormatOptions): Intl.DateTimeFormat => {
    const cacheKey = `${locale.value}_${key}_${JSON.stringify(options || {})}`
    if (!formatterCache.has(cacheKey)) {
      formatterCache.set(cacheKey, new Intl.DateTimeFormat(locale.value, options))
    }
    return formatterCache.get(cacheKey)!
  }

  const getCachedRelativeFormatter = (options?: Intl.RelativeTimeFormatOptions): Intl.RelativeTimeFormat => {
    const cacheKey = `${locale.value}_${JSON.stringify(options || {})}`
    if (!relativeCache.has(cacheKey)) {
      relativeCache.set(cacheKey, new Intl.RelativeTimeFormat(locale.value, options))
    }
    return relativeCache.get(cacheKey)!
  }

  const parseDate = (val: DateInput): Date | null => {
    if (val === null || val === undefined || val === '') return null
    if (val instanceof Date) return isNaN(val.getTime()) ? null : val
    const parsed = new Date(val)
    return isNaN(parsed.getTime()) ? null : parsed
  }

  const formatDate = (val: DateInput, options?: Intl.DateTimeFormatOptions): string => {
    const d = parseDate(val)
    if (!d) return 'N/A'
    const defaultOptions: Intl.DateTimeFormatOptions = {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      ...options,
    }
    return getCachedFormatter('date', defaultOptions).format(d)
  }

  const formatTime = (val: DateInput, options?: Intl.DateTimeFormatOptions): string => {
    const d = parseDate(val)
    if (!d) return 'N/A'
    const defaultOptions: Intl.DateTimeFormatOptions = {
      hour: '2-digit',
      minute: '2-digit',
      hour12: true,
      ...options,
    }
    return getCachedFormatter('time', defaultOptions).format(d)
  }

  const formatDateTime = (val: DateInput, options?: Intl.DateTimeFormatOptions): string => {
    const d = parseDate(val)
    if (!d) return 'N/A'
    const defaultOptions: Intl.DateTimeFormatOptions = {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      hour12: true,
      ...options,
    }
    return getCachedFormatter('datetime', defaultOptions).format(d)
  }

  const formatRelative = (val: DateInput): string => {
    const d = parseDate(val)
    if (!d) return 'N/A'

    const now = new Date()
    const diffInSeconds = Math.round((d.getTime() - now.getTime()) / 1000)

    const rtf = getCachedRelativeFormatter({ numeric: 'auto' })

    const absSec = Math.abs(diffInSeconds)
    if (absSec < 60) {
      return rtf.format(diffInSeconds, 'second')
    }
    const diffInMinutes = Math.round(diffInSeconds / 60)
    if (Math.abs(diffInMinutes) < 60) {
      return rtf.format(diffInMinutes, 'minute')
    }
    const diffInHours = Math.round(diffInMinutes / 60)
    if (Math.abs(diffInHours) < 24) {
      return rtf.format(diffInHours, 'hour')
    }
    const diffInDays = Math.round(diffInHours / 24)
    if (Math.abs(diffInDays) < 30) {
      return rtf.format(diffInDays, 'day')
    }
    const diffInMonths = Math.round(diffInDays / 30)
    if (Math.abs(diffInMonths) < 12) {
      return rtf.format(diffInMonths, 'month')
    }
    const diffInYears = Math.round(diffInDays / 365)
    return rtf.format(diffInYears, 'year')
  }

  const formatISODate = (val: DateInput, fallback = 'N/A'): string => {
    const d = parseDate(val)
    if (!d) return fallback
    const year = d.getFullYear()
    const month = String(d.getMonth() + 1).padStart(2, '0')
    const day = String(d.getDate()).padStart(2, '0')
    return `${year}-${month}-${day}`
  }

  const setLocale = (newLocale: string) => {
    locale.value = newLocale
    formatterCache.clear()
    relativeCache.clear()
  }

  return {
    locale,
    parseDate,
    formatDate,
    formatISODate,
    formatTime,
    formatDateTime,
    formatRelative,
    setLocale,
  }
}
