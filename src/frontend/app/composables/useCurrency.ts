export type CurrencySign = 'Php' | '$' | '€' | '¥'

export const useCurrency = () => {
    const currency = useState<CurrencySign>('app_currency', () => 'Php')

    const formatCurrency = (val: number | string) => {
        const numval = Number(val)
        const num = typeof numval === 'number' && !isNaN(numval) ? numval : 0
        const formatted = currency.value + Math.abs(num).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')
        if (num < 0) {
            return `(${formatted})`
        }
        return formatted
    }

    const applyCurrency = (mode: CurrencySign) => {
        currency.value = mode
        if (import.meta.client) {
            localStorage.setItem('accountant_currency', mode)
        }
    }

    const toggleCurrency = () => {
        const nextMode = currency.value === 'Php' ? '$' : 'Php'
        applyCurrency(nextMode)
    }

    const initCurrency = () => {
        if (import.meta.client) {
            const saved = localStorage.getItem('accountant_currency') as CurrencySign | null
            if (saved === 'Php' || saved === '$') {
                applyCurrency(saved)
            } else {
                applyCurrency('Php')
            }
        }
    }

    return {
        formatCurrency,
        currency,
        toggleCurrency,
        initCurrency,
        isPhp: computed(() => currency.value === 'Php'),
    }
}
