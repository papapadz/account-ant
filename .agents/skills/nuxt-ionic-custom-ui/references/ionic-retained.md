# What to keep from Ionic, and why

## Keep: page shell and navigation

`IonApp`, `IonRouterOutlet`, `IonPage`, `IonHeader`/`IonFooter` (as structural landmarks, styled with Tailwind), `IonContent`. These handle things that are genuinely hard to reproduce well: correct scroll containment, momentum scrolling on iOS, pull-to-refresh physics (`IonRefresher`), and page transition animations that match platform conventions when navigating between `IonPage`s. Style them with Tailwind classes on their content — don't fight their layout role.

## Keep: overlay controllers, but own the content

`IonModal`, `IonActionSheet`, `IonAlert`, `IonPopover`, `IonToast` solve real problems: portal rendering above the rest of the app, focus trapping, backdrop handling, gesture-to-dismiss, and correct z-index/safe-area-aware positioning. Reimplementing this well is a lot of work for little visual payoff, since the user never sees the controller itself — only its content.

Pattern: use the controller, but pass it a **custom Vue component** as content, styled entirely with Tailwind, so the chrome is Ionic but everything inside is yours.

```vue
<!-- composables/useConfirmModal.ts -->
<script setup lang="ts">
import { modalController } from '@ionic/vue'
import ConfirmModalContent from '~/components/ui/ConfirmModalContent.vue'

async function openConfirm(props: { title: string; message: string }) {
  const modal = await modalController.create({
    component: ConfirmModalContent,
    componentProps: props,
    cssClass: 'custom-modal', // hook for stripping Ionic's default modal chrome
    breakpoints: [0, 0.5],
    initialBreakpoint: 0.5,
  })
  await modal.present()
  const { data, role } = await modal.onWillDismiss()
  return { data, confirmed: role === 'confirm' }
}
</script>
```

```css
/* Strip Ionic's default modal sheet styling so only your content's
   Tailwind classes are visible — do this once, globally. */
.custom-modal::part(content) {
  border-radius: 1.25rem 1.25rem 0 0;
  background: transparent;
  box-shadow: none;
}
```

`ConfirmModalContent.vue` itself is a fully custom Tailwind component — it just happens to be rendered inside Ionic's animated sheet.

## Keep: platform + native integration APIs

`isPlatform()`, `useBackButton()`, `useIonRouter()`, and Capacitor plugins (`Haptics`, `StatusBar`, `Keyboard`, `App`) aren't visual at all — there's no reason to reimplement them. Use them directly inside custom components when a component needs platform-aware behavior (e.g. a custom `<Button>` triggering `Haptics.impact()` on press).

## Rebuild: every form control and content-display component

`IonButton`, `IonInput`, `IonTextarea`, `IonSelect`, `IonCheckbox`, `IonRadio`, `IonToggle`, `IonRange`, `IonItem`, `IonList`, `IonCard`, `IonBadge`, `IonChip`, `IonAvatar`, `IonProgressBar`, `IonSkeletonText`, `IonFab`, `IonSpinner`, `IonSegment`, `IonTabBar`/`IonTabButton`. These are purely presentational wrappers around ordinary HTML — a button, an input, a list — and Ionic's styling on them is the thing being replaced. Rebuilding them as native elements with Tailwind classes is straightforward and is where nearly all of the custom design work actually lives. See `components.md` for working examples of the most common ones.

One caveat: `IonSpinner`'s SVG-based platform-specific spinner animations (ios/crescent/dots/etc.) are mildly fiddly to reproduce pixel-for-pixel. It's fine to keep `IonSpinner` short-term for a loading indicator and swap in a custom Tailwind spin animation (`animate-spin` on an SVG/border-based spinner) once the design system has a defined spinner style — flag this as a reasonable place to defer if the user is optimizing for speed.
