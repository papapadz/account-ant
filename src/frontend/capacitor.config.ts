import type { CapacitorConfig } from '@capacitor/cli'

const config: CapacitorConfig = {
  appId: 'com.accountant.app',
  appName: 'AccountAnt',
  // nuxt generate (ssr:false) outputs static files here
  webDir: '.output/public',
  server: {
    // Use https scheme on Android to match secure-context requirements
    androidScheme: 'https',
  },
  plugins: {
    SplashScreen: {
      launchShowDuration: 2000,
      backgroundColor: '#020617', // --bg-app dark token
      androidSplashResourceName: 'splash',
      showSpinner: false,
    },
  },
}

export default config
