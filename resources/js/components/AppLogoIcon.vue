<script setup lang="ts">
import type { HTMLAttributes } from 'vue'

defineOptions({
  inheritAttrs: false,
})

interface Props {
  className?: HTMLAttributes['class']
}

const props = defineProps<Props>()
</script>

<template>
  <span class="inline-flex items-center justify-center" v-bind="$attrs">
    <img
      src="/logo-light.png"
      alt="Temply icon"
      :class="[props.className, 'dark:hidden']"
    />
    <img
      src="/logo-dark.png"
      alt=""
      :class="[props.className, 'hidden dark:block']"
    />
  </span>
</template>
