<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as resumesIndex, edit as resumesEdit } from '@/routes/resumes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Download, Eye, Pencil, Trash2 } from 'lucide-vue-next';

interface ResumeSummary {
    id: string;
    title: string;
    slug: string;
    headline?: string | null;
    created_at?: string | null;
    updated_at?: string | null;
}

const props = defineProps<{
    resumes: ResumeSummary[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
    {
        title: 'Resumes',
        href: resumesIndex().url,
    },
];

const dateFormatter = new Intl.DateTimeFormat(undefined, {
    dateStyle: 'medium',
});

function formatDate(value?: string | null): string {
    if (!value) {
        return '—';
    }

    const parsedDate = new Date(value);

    if (Number.isNaN(parsedDate.getTime())) {
        return '—';
    }

    return dateFormatter.format(parsedDate);
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Resumes" />

        <section class="flex flex-1 flex-col gap-6 p-6">
            <header class="space-y-2">
                <h1 class="text-2xl font-semibold tracking-tight">Resumes</h1>
                <p class="text-sm text-muted-foreground">
                    Review and manage the resumes you&rsquo;ve crafted. Actions are
                    coming soon.
                </p>
            </header>

            <div
                v-if="props.resumes.length"
                class="grid gap-4 md:grid-cols-2 xl:grid-cols-3"
            >
                <Card v-for="resume in props.resumes" :key="resume.id">
                    <CardHeader class="space-y-1">
                        <CardTitle class="flex items-center justify-between gap-2">
                            <span class="line-clamp-1">{{ resume.title }}</span>
                            <span
                                class="rounded-full bg-muted px-2 py-0.5 text-xs text-muted-foreground"
                            >
                                {{ formatDate(resume.updated_at) }}
                            </span>
                        </CardTitle>
                        <CardDescription class="line-clamp-2">
                            {{ resume.headline ?? 'No headline provided' }}
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="space-y-2 text-sm text-muted-foreground">
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-foreground">Slug</span>
                            <span class="font-mono text-xs">{{ resume.slug }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-medium text-foreground">Created</span>
                            <span>{{ formatDate(resume.created_at) }}</span>
                        </div>
                    </CardContent>

                    <CardFooter class="flex flex-wrap items-center gap-2">
                        <Button size="sm" variant="outline" disabled>
                            <Eye class="mr-2 h-4 w-4" />
                            View
                        </Button>
                        <Button as-child size="sm" variant="outline">
                            <Link
                                :href="resumesEdit(resume).url"
                                class="inline-flex items-center gap-2"
                            >
                                <Pencil class="h-4 w-4" />
                                Edit
                            </Link>
                        </Button>
                        <Button size="sm" variant="outline" disabled>
                            <Download class="mr-2 h-4 w-4" />
                            Download
                        </Button>
                        <Button
                            size="sm"
                            variant="destructive"
                            class="disabled:opacity-60"
                            disabled
                        >
                            <Trash2 class="mr-2 h-4 w-4" />
                            Delete
                        </Button>
                    </CardFooter>
                </Card>
            </div>

            <div
                v-else
                class="flex flex-1 flex-col items-center justify-center rounded-xl border border-dashed border-muted-foreground/40 p-12 text-center"
            >
                <h2 class="text-lg font-medium text-foreground">No resumes yet</h2>
                <p class="mt-2 max-w-md text-sm text-muted-foreground">
                    Start crafting your story by creating your first resume. Once
                    saved, it will be displayed here.
                </p>
            </div>
        </section>
    </AppLayout>
</template>

