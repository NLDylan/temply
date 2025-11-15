<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { index as resumesIndex, edit as resumesEdit } from '@/routes/resumes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Copy, Download, Eye, Pencil, Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

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

const createDialogOpen = ref(false);
const createForm = useForm({
    title: '',
});

function submitCreateForm(): void {
    createForm.post('/resumes', {
        preserveScroll: true,
        onSuccess: () => {
            createDialogOpen.value = false;
            createForm.reset();
        },
    });
}

const deleteDialogOpen = ref<Record<string, boolean>>({});

function openDeleteDialog(resumeId: string): void {
    deleteDialogOpen.value[resumeId] = true;
}

function closeDeleteDialog(resumeId: string): void {
    deleteDialogOpen.value[resumeId] = false;
}

function deleteResume(resumeId: string): void {
    router.delete(`/resumes/${resumeId}`, {
        preserveScroll: true,
        onSuccess: () => {
            closeDeleteDialog(resumeId);
        },
    });
}

function duplicateResume(resumeId: string): void {
    router.post(`/resumes/${resumeId}/duplicate`, {}, {
        preserveScroll: true,
    });
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Resumes" />

        <section class="flex flex-1 flex-col gap-6 p-6">
            <header class="flex items-center justify-between">
                <div class="space-y-2">
                    <h1 class="text-2xl font-semibold tracking-tight">Resumes</h1>
                    <p class="text-sm text-muted-foreground">
                        Review and manage the resumes you&rsquo;ve crafted.
                    </p>
                </div>
                <Dialog v-model:open="createDialogOpen">
                    <DialogTrigger as-child>
                        <Button>
                            <Plus class="mr-2 h-4 w-4" />
                            Create Resume
                        </Button>
                    </DialogTrigger>
                    <DialogContent>
                        <form @submit.prevent="submitCreateForm">
                            <DialogHeader>
                                <DialogTitle>Create New Resume</DialogTitle>
                                <DialogDescription>
                                    Enter a title for your new resume. You can edit
                                    this later.
                                </DialogDescription>
                            </DialogHeader>
                            <div class="grid gap-4 py-4">
                                <div class="grid gap-2">
                                    <Label for="title">Title</Label>
                                    <Input
                                        id="title"
                                        v-model="createForm.title"
                                        placeholder="e.g., Software Engineer Resume"
                                        required
                                    />
                                    <p
                                        v-if="createForm.errors.title"
                                        class="text-sm text-destructive"
                                    >
                                        {{ createForm.errors.title }}
                                    </p>
                                </div>
                            </div>
                            <DialogFooter>
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="createDialogOpen = false"
                                >
                                    Cancel
                                </Button>
                                <Button
                                    type="submit"
                                    :disabled="createForm.processing"
                                >
                                    Create
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>
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
                            variant="outline"
                            @click="duplicateResume(resume.id)"
                        >
                            <Copy class="mr-2 h-4 w-4" />
                            Duplicate
                        </Button>
                        <Dialog
                            v-model:open="deleteDialogOpen[resume.id]"
                        >
                            <DialogTrigger as-child>
                                <Button
                                    size="sm"
                                    variant="destructive"
                                    @click="openDeleteDialog(resume.id)"
                                >
                                    <Trash2 class="mr-2 h-4 w-4" />
                                    Delete
                                </Button>
                            </DialogTrigger>
                            <DialogContent>
                                <DialogHeader>
                                    <DialogTitle>Delete Resume</DialogTitle>
                                    <DialogDescription>
                                        Are you sure you want to delete
                                        &ldquo;{{ resume.title }}&rdquo;? This action
                                        cannot be undone.
                                    </DialogDescription>
                                </DialogHeader>
                                <DialogFooter>
                                    <Button
                                        type="button"
                                        variant="outline"
                                        @click="closeDeleteDialog(resume.id)"
                                    >
                                        Cancel
                                    </Button>
                                    <Button
                                        type="button"
                                        variant="destructive"
                                        @click="deleteResume(resume.id)"
                                    >
                                        Delete
                                    </Button>
                                </DialogFooter>
                            </DialogContent>
                        </Dialog>
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

