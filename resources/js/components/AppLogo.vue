<template>
    <div class="flex items-center gap-2">
        <img
            src="/logo-light.png"
            alt="Temply logo"
            class="h-10 shrink-0 dark:hidden group-data-[collapsible=icon]:hidden"
        />
        <img
            src="/logo-dark.png"
            alt=""
            class="hidden h-10 shrink-0 dark:block group-data-[collapsible=icon]:hidden"
        />

        <img
            src="/favicon.png"
            alt="Temply icon"
            class="hidden h-9 w-9 shrink-0 rounded-lg group-data-[collapsible=icon]:block dark:hidden"
        />
        <img
            src="/favicon.png"
            alt=""
            class="hidden h-9 w-9 shrink-0 rounded-lg group-data-[collapsible=icon]:block dark:block dark:opacity-90"
        />
    </div>
</template>

