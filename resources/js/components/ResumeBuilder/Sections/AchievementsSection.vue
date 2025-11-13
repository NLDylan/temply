<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Separator } from '@/components/ui/separator'
import SectionPanel from './SectionPanel.vue'
import { computed, ref } from 'vue'
import { Award, ChevronDown, Plus, Trophy } from 'lucide-vue-next'
import type { ResumeAchievement } from '@/types/resume'

interface Props {
  achievements?: ResumeAchievement[]
}

const props = withDefaults(defineProps<Props>(), {
  achievements: () => [],
})

const achievements = ref<ResumeAchievement[]>(
  props.achievements.length > 0
    ? props.achievements
    : [
        {
          id: crypto.randomUUID(),
          resume_id: '',
          title: '',
          issuer: null,
          achieved_on: null,
          category: null,
          url: null,
          description: null,
          sort_order: 0,
          metadata: null,
        },
      ],
)

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
      resume_id: '',
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

function formatDate(date: string | null): string {
  if (!date) {
    return ''
  }

  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    year: 'numeric',
  })
}
</script>

<template>
  <SectionPanel
    title="Achievements"
    description="Highlight metrics and accolades that set you apart."
    hint="Include awards, recognitions, competitions won, or significant milestones."
  >
    <div class="grid gap-6">
      <div class="flex justify-between gap-3">
        <div class="flex items-center gap-2 text-sm font-medium text-muted-foreground">
          <Trophy class="size-4 text-primary" />
          Showcase your accomplishments.
        </div>
        <Button variant="outline" size="sm" class="gap-2" @click="addAchievement">
          <Plus class="size-4 text-primary" />
          Add achievement
        </Button>
      </div>

      <div class="grid gap-6">
        <Collapsible
          v-for="achievement in achievementsList"
          :key="achievement.id"
          :default-open="achievement.title === ''"
        >
          <Card class="resume-card p-0 transition-colors duration-200 hover:border-border/80">
            <div class="px-5 py-4">
              <CollapsibleTrigger as-child>
                <button
                  type="button"
                  class="group/collapsible flex w-full items-start justify-between gap-4 text-left"
                >
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
                        <span
                          v-if="achievement.achieved_on"
                          class="rounded-full bg-background/80 px-2 py-1"
                        >
                          {{ formatDate(achievement.achieved_on) }}
                        </span>
                        <span
                          v-if="achievement.category"
                          class="rounded-full bg-background/80 px-2 py-1"
                        >
                          {{ achievement.category }}
                        </span>
                      </div>
                    </div>
                  </div>
                  <ChevronDown
                    class="mt-1 size-5 shrink-0 text-muted-foreground transition group-data-[state=open]/collapsible:rotate-180"
                  />
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
                    <textarea
                      v-model="achievement.description"
                      rows="4"
                      class="rounded-xl border border-border/40 bg-muted/20 px-3 py-2 text-sm text-muted-foreground transition focus:border-ring focus:ring-2 focus:ring-ring/30"
                      placeholder="Describe the achievement, its significance, and impact..."
                    />
                  </div>
                </div>

                <Card class="resume-card p-4">
                  <p class="text-sm font-medium text-foreground">Details</p>
                  <p class="mt-1 text-xs text-muted-foreground">
                    These fields appear on your resume.
                  </p>
                  <div class="mt-4 grid gap-3">
                    <div class="grid gap-2">
                      <Label>Title</Label>
                      <Input v-model="achievement.title" placeholder="Achievement title" />
                    </div>
                    <div class="grid gap-2">
                      <Label>Issuer</Label>
                      <Input
                        v-model="achievement.issuer"
                        placeholder="Organization or institution"
                      />
                    </div>
                    <div class="grid gap-2">
                      <Label>Date achieved</Label>
                      <Input
                        v-model="achievement.achieved_on"
                        type="date"
                        placeholder="Date achieved"
                      />
                    </div>
                    <div class="grid gap-2">
                      <Label>Category</Label>
                      <Input
                        v-model="achievement.category"
                        placeholder="e.g., Award, Competition, Recognition"
                      />
                    </div>
                    <div class="grid gap-2">
                      <Label>URL</Label>
                      <Input
                        v-model="achievement.url"
                        type="url"
                        placeholder="Link to achievement (optional)"
                      />
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

