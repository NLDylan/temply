<template>
    <div
        class="flex items-center gap-2 group-data-[collapsible=icon]:justify-center group-data-[collapsible=icon]:gap-0"
    >
        <span class="relative flex h-9 w-9 shrink-0 items-center justify-center">
            <img
                src="/logo-light.png"
                alt="Temply logo"
                class="h-8 w-8 dark:hidden"
            />
            <img
                src="/logo-dark.png"
                alt=""
                class="hidden h-8 w-8 dark:block"
            />
        </span>

        <div
            class="grid flex-1 text-left text-sm group-data-[collapsible=icon]:hidden"
        >
            <span class="font-semibold leading-tight text-sidebar-foreground">
                Temply
            </span>
            <span class="text-xs text-sidebar-foreground/70">
                CV Builder
            </span>
        </div>
    </div>
</template>

