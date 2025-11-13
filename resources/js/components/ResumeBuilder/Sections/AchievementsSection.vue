<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Separator } from '@/components/ui/separator'
import SectionPanel from './SectionPanel.vue'
import { computed, ref, watch } from 'vue'
import { Award, ChevronDown, Plus, Trophy, Trash2 } from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'
import { sync as syncAchievements } from '@/routes/resumes/achievements'
import type { ResumeAchievement } from '@/types/resume'
import { useDebounceFn } from '@vueuse/core'

interface Props {
  resumeId: string
  achievements?: ResumeAchievement[]
}

const props = withDefaults(defineProps<Props>(), {
  achievements: () => [],
})

const achievements = ref<ResumeAchievement[]>(
  props.achievements && props.achievements.length > 0
    ? [...props.achievements]
    : [],
)

const isSaving = ref(false)
const saveError = ref<string | null>(null)
const lastSaved = ref<Date | null>(null)

const achievementsList = computed(() =>
  achievements.value.map((entry, index) => ({
    ...entry,
    isLast: index === achievements.value.length - 1,
  })),
)

function addAchievement() {
  achievements.value = [
    {
      id: crypto.randomUUID(),
      resume_id: props.resumeId,
      title: '',
      issuer: null,
      achieved_on: null,
      category: null,
      url: null,
      description: null,
      sort_order: achievements.value.length,
      metadata: null,
    },
    ...achievements.value,
  ]
}

function removeAchievement(id: string) {
  const index = achievements.value.findIndex((a) => a.id === id)
  if (index !== -1) {
    achievements.value.splice(index, 1)
    save()
  }
}

function formatDate(date: string | null): string {
  if (!date) {
    return ''
  }

  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    year: 'numeric',
  })
}

function prepareDataForSync(): Array<Partial<ResumeAchievement>> {
  return achievements.value.map((entry, index) => {
    const data: Partial<ResumeAchievement> = {
      title: entry.title?.trim() || '',
      issuer: entry.issuer?.trim() || null,
      achieved_on: entry.achieved_on || null,
      category: entry.category?.trim() || null,
      url: entry.url?.trim() || null,
      description: entry.description?.trim() || null,
      sort_order: index,
      metadata: entry.metadata || null,
    }

    // Only include id if it exists and is not empty (for updates)
    if (entry.id && entry.id !== '' && entry.id !== props.resumeId) {
      data.id = entry.id
    }

    return data
  })
}

function save() {
  const dataToSave = prepareDataForSync().filter(
    (entry) => entry.title && entry.title.trim() !== '',
  )

  // Only save if we have at least one entry with content
  // Don't save empty arrays (which would delete everything)
  if (dataToSave.length === 0) {
    return
  }

  // Debug: Log what we're sending
  console.log('Saving achievements:', JSON.stringify(dataToSave, null, 2))

  isSaving.value = true
  isSavingRef.value = true
  saveError.value = null

  router.post(
    syncAchievements.url({ resume: props.resumeId }),
    {
      achievements: dataToSave,
    },
    {
      preserveScroll: true,
      preserveState: true,
      only: ['resume'],
      onSuccess: () => {
        lastSaved.value = new Date()
        saveError.value = null
        console.log('Save successful')
        // The redirect will reload the resume data with updated achievements
      },
      onError: (errors) => {
        console.error('Save error:', errors)
        saveError.value =
          errors.achievements || errors.message || 'Failed to save achievements.'
      },
      onFinish: () => {
        isSaving.value = false
        // Delay resetting isSavingRef to prevent immediate watcher trigger
        setTimeout(() => {
          isSavingRef.value = false
        }, 100)
      },
    },
  )
}

const debouncedSave = useDebounceFn(save, 1000)

// Track if we're currently saving to prevent watcher loops
const isSavingRef = ref(false)

// Watch for changes in achievements data and auto-save
watch(
  () => JSON.stringify(achievements.value.map((a) => ({
    title: a.title,
    issuer: a.issuer,
    achieved_on: a.achieved_on,
    category: a.category,
    url: a.url,
    description: a.description,
  }))),
  () => {
    // Don't save if we're already saving or if props are syncing
    if (isSavingRef.value) {
      return
    }

    // Only save if we have at least one entry with content
    const hasContent = achievements.value.some(
      (entry) => entry.title && entry.title.trim() !== '',
    )
    if (hasContent) {
      debouncedSave()
    }
  },
  { deep: false },
)

// Sync with props when they change (after server save)
watch(
  () => props.achievements,
  (newAchievements) => {
    // Don't sync if we're currently saving (to prevent loops)
    if (isSavingRef.value) {
      return
    }

    // Always sync with props, even if empty
    const currentIds = achievements.value.map((a) => a.id).sort().join(',')
    const newIds = (newAchievements || []).map((a) => a.id).sort().join(',')
    if (currentIds !== newIds) {
      isSavingRef.value = true // Prevent watcher from triggering during sync
      achievements.value = newAchievements && newAchievements.length > 0
        ? [...newAchievements]
        : []
      setTimeout(() => {
        isSavingRef.value = false
      }, 100)
    }
  },
  { deep: true, immediate: true },
)
</script>

<template>
  <SectionPanel title="Achievements" description="Highlight metrics and accolades that set you apart."
    hint="Include awards, recognitions, competitions won, or significant milestones.">
    <div class="grid gap-6">
      <div class="flex justify-between gap-3">
        <div class="flex items-center gap-2 text-sm font-medium text-muted-foreground">
          <Trophy class="size-4 text-primary" />
          <span>Showcase your accomplishments.</span>
          <span v-if="isSaving" class="text-xs text-muted-foreground">(Saving...)</span>
          <span v-else-if="lastSaved" class="text-xs text-muted-foreground">
            (Saved {{ lastSaved.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }})
          </span>
        </div>
        <Button variant="outline" size="sm" class="gap-2" @click="addAchievement">
          <Plus class="size-4 text-primary" />
          Add achievement
        </Button>
      </div>

      <div v-if="saveError"
        class="rounded-lg border border-destructive/50 bg-destructive/10 p-3 text-sm text-destructive">
        {{ saveError }}
      </div>

      <div v-if="achievementsList.length === 0"
        class="rounded-xl border border-dashed border-border/40 bg-muted/20 p-8 text-center">
        <Award class="mx-auto mb-3 size-8 text-muted-foreground" />
        <p class="text-sm font-medium text-foreground">No achievements yet</p>
        <p class="mt-1 text-xs text-muted-foreground">
          Click "Add achievement" to get started.
        </p>
      </div>

      <div v-else class="grid gap-6">
        <Collapsible v-for="achievement in achievementsList" :key="achievement.id"
          :default-open="achievement.title === ''">
          <Card class="resume-card p-0 transition-colors duration-200 hover:border-border/80">
            <div class="px-5 py-4">
              <CollapsibleTrigger as-child>
                <button type="button" class="group/collapsible flex w-full items-start justify-between gap-4 text-left">
                  <div class="flex items-start gap-3">
                    <Award class="mt-1 size-5 shrink-0 text-primary" />
                    <div>
                      <p class="text-lg font-semibold text-foreground">
                        {{ achievement.title || 'Achievement title' }}
                      </p>
                      <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-muted-foreground">
                        <span v-if="achievement.issuer" class="rounded-full bg-background/80 px-2 py-1">
                          {{ achievement.issuer }}
                        </span>
                        <span v-if="achievement.achieved_on" class="rounded-full bg-background/80 px-2 py-1">
                          {{ formatDate(achievement.achieved_on) }}
                        </span>
                        <span v-if="achievement.category" class="rounded-full bg-background/80 px-2 py-1">
                          {{ achievement.category }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <ChevronDown
                    class="mt-1 size-5 shrink-0 text-muted-foreground transition group-data-[state=open]/collapsible:rotate-180" />
                </button>
              </CollapsibleTrigger>
            </div>

            <CollapsibleContent>
              <Separator />
              <CardContent class="grid gap-6 px-5 py-5 lg:grid-cols-[minmax(0,1fr),320px]">
                <div class="grid gap-4">
                  <div class="grid gap-3">
                    <Label class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                      Description
                    </Label>
                    <textarea v-model="achievement.description" rows="4"
                      class="rounded-xl border border-border/40 bg-muted/20 px-3 py-2 text-sm text-muted-foreground transition focus:border-ring focus:ring-2 focus:ring-ring/30"
                      placeholder="Describe the achievement, its significance, and impact..." />
                  </div>
                </div>

                <Card class="resume-card p-4">
                  <div class="mb-4 flex items-center justify-between">
                    <div>
                      <p class="text-sm font-medium text-foreground">Details</p>
                      <p class="mt-1 text-xs text-muted-foreground">
                        These fields appear on your resume.
                      </p>
                    </div>
                    <Button variant="ghost" size="sm" class="text-destructive hover:text-destructive"
                      @click="removeAchievement(achievement.id)">
                      <Trash2 class="size-4" />
                    </Button>
                  </div>
                  <div class="grid gap-3">
                    <div class="grid gap-2">
                      <Label>Title</Label>
                      <Input v-model="achievement.title" placeholder="Achievement title" />
                    </div>
                    <div class="grid gap-2">
                      <Label>Issuer</Label>
                      <Input :model-value="achievement.issuer || ''" placeholder="Organization or institution"
                        @update:model-value="achievement.issuer = ($event as string) || null" />
                    </div>
                    <div class="grid gap-2">
                      <Label>Date achieved</Label>
                      <Input :model-value="achievement.achieved_on || ''" type="date" placeholder="Date achieved"
                        @update:model-value="achievement.achieved_on = ($event as string) || null" />
                    </div>
                    <div class="grid gap-2">
                      <Label>Category</Label>
                      <Input :model-value="achievement.category || ''"
                        placeholder="e.g., Award, Competition, Recognition"
                        @update:model-value="achievement.category = ($event as string) || null" />
                    </div>
                    <div class="grid gap-2">
                      <Label>URL</Label>
                      <Input :model-value="achievement.url || ''" type="url"
                        placeholder="Link to achievement (optional)"
                        @update:model-value="achievement.url = ($event as string) || null" />
                    </div>
                  </div>
                </Card>
              </CardContent>
            </CollapsibleContent>
          </Card>
        </Collapsible>
      </div>
    </div>
  </SectionPanel>
</template>
