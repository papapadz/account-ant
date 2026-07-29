<template>
  <div></div>
</template>

<script setup lang="ts">
const route = useRoute()
const router = useRouter()

onMounted(() => {
  router.replace(`/project/${route.params.id}`)
})
</script>
