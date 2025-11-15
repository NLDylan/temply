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
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { FileText, Plus, TrendingUp } from 'lucide-vue-next';

interface RecentResume {
    id: string;
    title: string;
    slug: string;
    headline?: string | null;
    updated_at?: string | null;
}

const props = defineProps<{
    recentResumes: RecentResume[];
    totalResumes: number;
    recentlyUpdated: number;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
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
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-6">
            <header class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">Dashboard</h1>
                    <p class="text-sm text-muted-foreground">
                        Overview of your resume activity
                    </p>
                </div>
                <Button as-child>
                    <Link :href="resumesIndex().url" class="inline-flex items-center gap-2">
                        <Plus class="h-4 w-4" />
                        Create Resume
                    </Link>
                </Button>
            </header>

            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Total Resumes
                        </CardTitle>
                        <FileText class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ props.totalResumes }}</div>
                        <p class="text-xs text-muted-foreground">
                            All resumes you&rsquo;ve created
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Recently Updated
                        </CardTitle>
                        <TrendingUp class="h-4 w-4 text-muted-foreground" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ props.recentlyUpdated }}</div>
                        <p class="text-xs text-muted-foreground">
                            Updated in the last 7 days
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">
                            Quick Actions
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <Button
                                as-child
                                variant="outline"
                                class="w-full justify-start"
                            >
                                <Link :href="resumesIndex().url">
                                    View All Resumes
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Recent Resumes</CardTitle>
                    <CardDescription>
                        Your most recently updated resumes
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="props.recentResumes.length" class="space-y-4">
                        <div
                            v-for="resume in props.recentResumes"
                            :key="resume.id"
                            class="flex items-center justify-between rounded-lg border p-4"
                        >
                            <div class="space-y-1">
                                <Link
                                    :href="resumesEdit(resume).url"
                                    class="font-medium hover:underline"
                                >
                                    {{ resume.title }}
                                </Link>
                                <p
                                    v-if="resume.headline"
                                    class="text-sm text-muted-foreground"
                                >
                                    {{ resume.headline }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    Updated {{ formatDate(resume.updated_at) }}
                                </p>
                            </div>
                            <Button as-child size="sm" variant="outline">
                                <Link :href="resumesEdit(resume).url">
                                    Edit
                                </Link>
                            </Button>
                        </div>
                    </div>
                    <div
                        v-else
                        class="flex flex-col items-center justify-center rounded-lg border border-dashed p-12 text-center"
                    >
                        <FileText class="mb-4 h-12 w-12 text-muted-foreground" />
                        <h3 class="text-lg font-medium">No resumes yet</h3>
                        <p class="mt-2 text-sm text-muted-foreground">
                            Get started by creating your first resume.
                        </p>
                        <Button as-child class="mt-4">
                            <Link :href="resumesIndex().url">
                                <Plus class="mr-2 h-4 w-4" />
                                Create Resume
                            </Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
