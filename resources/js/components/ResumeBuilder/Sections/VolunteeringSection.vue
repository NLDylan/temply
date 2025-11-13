<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Separator } from '@/components/ui/separator'
import SectionPanel from './SectionPanel.vue'
import { computed, ref } from 'vue'
import { CalendarRange, ChevronDown, Heart, Plus } from 'lucide-vue-next'
import type { ResumeVolunteering } from '@/types/resume'

interface Props {
  volunteering?: ResumeVolunteering[]
}

const props = withDefaults(defineProps<Props>(), {
  volunteering: () => [],
})

const volunteering = ref<ResumeVolunteering[]>(
  props.volunteering.length > 0
    ? props.volunteering
    : [
        {
          id: crypto.randomUUID(),
          resume_id: '',
          organization: '',
          role: null,
          location: null,
          started_on: null,
          ended_on: null,
          is_current: false,
          description: null,
          sort_order: 0,
          metadata: null,
        },
      ],
)

const timeline = computed(() =>
  volunteering.value.map((entry, index) => ({
    ...entry,
    isLast: index === volunteering.value.length - 1,
  })),
)

function addVolunteering() {
  volunteering.value = [
    {
      id: crypto.randomUUID(),
      resume_id: '',
      organization: '',
      role: null,
      location: null,
      started_on: null,
      ended_on: null,
      is_current: false,
      description: null,
      sort_order: volunteering.value.length,
      metadata: null,
    },
    ...volunteering.value,
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
    title="Volunteering"
    description="Demonstrate community involvement and leadership through your volunteer work."
    hint="Highlight roles, impact, and skills gained through volunteering."
  >
    <div class="grid gap-6">
      <div class="flex justify-between gap-3">
        <div class="flex items-center gap-2 text-sm font-medium text-muted-foreground">
          <Heart class="size-4 text-primary" />
          Showcase your community contributions.
        </div>
        <Button variant="outline" size="sm" class="gap-2" @click="addVolunteering">
          <Plus class="size-4 text-primary" />
          Add volunteering
        </Button>
      </div>

      <div class="grid gap-6">
        <Collapsible
          v-for="entry in timeline"
          :key="entry.id"
          :default-open="entry.organization === ''"
        >
          <Card class="resume-card p-0 transition-colors duration-200 hover:border-border/80">
            <div class="px-5 py-4">
              <CollapsibleTrigger as-child>
                <button
                  type="button"
                  class="group/collapsible flex w-full items-start justify-between gap-4 text-left"
                >
                  <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">
                      {{ entry.organization || 'Organization' }}
                    </p>
                    <p class="text-lg font-semibold text-foreground">
                      {{ entry.role || 'Role' }}
                    </p>
                    <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-muted-foreground">
                      <span
                        v-if="entry.started_on || entry.ended_on || entry.is_current"
                        class="inline-flex items-center gap-1 rounded-full bg-background/80 px-2 py-1"
                      >
                        <CalendarRange class="size-3.5" />
                        <template v-if="entry.is_current">
                          {{ formatDate(entry.started_on) }} — Present
                        </template>
                        <template v-else>
                          {{ formatDate(entry.started_on) }} — {{ formatDate(entry.ended_on) }}
                        </template>
                      </span>
                      <span v-if="entry.location" class="rounded-full bg-background/80 px-2 py-1">
                        {{ entry.location }}
                      </span>
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
                      v-model="entry.description"
                      rows="4"
                      class="rounded-xl border border-border/40 bg-muted/20 px-3 py-2 text-sm text-muted-foreground transition focus:border-ring focus:ring-2 focus:ring-ring/30"
                      placeholder="Describe your volunteer work, responsibilities, and impact..."
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
                      <Label>Organization</Label>
                      <Input v-model="entry.organization" placeholder="Organization name" />
                    </div>
                    <div class="grid gap-2">
                      <Label>Role</Label>
                      <Input v-model="entry.role" placeholder="Volunteer role or position" />
                    </div>
                    <div class="grid gap-2">
                      <Label>Location</Label>
                      <Input v-model="entry.location" placeholder="City, Country" />
                    </div>
                    <div class="grid gap-2">
                      <Label>Start date</Label>
                      <Input
                        v-model="entry.started_on"
                        type="date"
                        placeholder="Start date"
                      />
                    </div>
                    <div class="grid gap-2">
                      <Label>End date</Label>
                      <Input
                        v-model="entry.ended_on"
                        type="date"
                        :disabled="entry.is_current"
                        placeholder="End date"
                      />
                    </div>
                    <div class="flex items-center gap-2">
                      <input
                        id="is-current"
                        v-model="entry.is_current"
                        type="checkbox"
                        class="rounded border-border"
                      />
                      <Label for="is-current" class="text-sm">Currently volunteering</Label>
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

