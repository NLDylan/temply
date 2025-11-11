<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Separator } from '@/components/ui/separator'
import SectionPanel from './SectionPanel.vue'
import { computed, reactive } from 'vue'
import { CalendarRange, ChevronDown, ListChecks, Plus } from 'lucide-vue-next'

interface ExperienceEntry {
  id: string
  company: string
  role: string
  start: string
  end: string
  location: string
  achievements: string[]
}

const experiences = reactive<ExperienceEntry[]>([
  {
    id: crypto.randomUUID(),
    company: 'Nimbus Analytics',
    role: 'Lead Product Designer',
    start: 'Aug 2021',
    end: 'Present',
    location: 'Remote · South Africa',
    achievements: [
      'Spearheaded design for a multi-tenant analytics platform, lifting user engagement by 36%.',
      'Partnered with product and research to launch a design system adopted across 5 squads.',
      'Mentored a team of 4 designers, introducing outcome-based workflows and critique rituals.',
    ],
  },
  {
    id: crypto.randomUUID(),
    company: 'ScaleOps',
    role: 'Product Designer',
    start: 'Jan 2018',
    end: 'Jul 2021',
    location: 'Cape Town, South Africa',
    achievements: [
      'Redesigned onboarding for enterprise customers, reducing time-to-value by 42%.',
      'Introduced accessibility standards, achieving AA compliance across flagship flows.',
      'Collaborated with data science to visualize predictive insights used by 1.2k customers.',
    ],
  },
])

const timeline = computed(() =>
  experiences.map((experience, index) => ({
    ...experience,
    isLast: index === experiences.length - 1,
  })),
)
</script>

<template>
  <SectionPanel
    title="Work experience"
    description="Translate your responsibilities into measurable impact. Focus on achievements, outcomes, and the story they tell together."
    hint="Each entry should highlight a problem, the actions you took, and the outcome. Quantify whenever possible."
  >
    <div class="grid gap-6">
      <div class="flex justify-between gap-3">
        <div class="flex items-center gap-2 text-sm font-medium text-muted-foreground">
          <ListChecks class="size-4 text-brand" />
          Outline up to 10 years of relevant experience.
        </div>
        <Button variant="outline" size="sm" class="gap-2">
          <Plus class="size-4" />
          Add experience
        </Button>
      </div>

      <div class="grid gap-6">
        <Collapsible
          v-for="experience in timeline"
          :key="experience.id"
          open
          class="rounded-2xl border border-border/60 bg-muted/20 p-4 transition hover:border-brand/40"
        >
          <CollapsibleTrigger as-child>
            <button
              type="button"
              class="group/collapsible flex w-full items-start justify-between gap-4 text-left"
            >
              <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">
                  {{ experience.company }}
                </p>
                <p class="text-lg font-semibold text-foreground">
                  {{ experience.role }}
                </p>
                <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-muted-foreground">
                  <span class="inline-flex items-center gap-1 rounded-full bg-background/80 px-2 py-1">
                    <CalendarRange class="size-3.5" />
                    {{ experience.start }} — {{ experience.end }}
                  </span>
                  <span class="rounded-full bg-background/80 px-2 py-1">
                    {{ experience.location }}
                  </span>
                </div>
              </div>
              <ChevronDown
                class="mt-1 size-5 shrink-0 text-muted-foreground transition group-data-[state=open]/collapsible:rotate-180"
              />
            </button>
          </CollapsibleTrigger>

          <CollapsibleContent>
            <Separator class="my-4" />
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr),320px]">
              <div class="grid gap-4">
                <div class="grid gap-3">
                  <Label class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                    Achievements
                  </Label>
                  <Card v-for="achievement in experience.achievements" :key="achievement" class="border-border/60 bg-background/90">
                    <CardContent class="flex items-start gap-3 p-4">
                      <span class="mt-1.5 h-1.5 w-1.5 rounded-full bg-brand" />
                      <p class="text-sm leading-relaxed text-muted-foreground">
                        {{ achievement }}
                      </p>
                    </CardContent>
                  </Card>
                </div>
                <Button variant="ghost" size="sm" class="w-fit gap-2">
                  <Plus class="size-4" />
                  Add achievement
                </Button>
              </div>

              <Card class="h-fit border-border/60 bg-background/95">
                <CardHeader>
                  <CardTitle class="text-sm font-medium">Role details</CardTitle>
                  <CardDescription>
                    These fields appear alongside the timeline on your resume layout.
                  </CardDescription>
                </CardHeader>
                <CardContent class="grid gap-3">
                  <div class="grid gap-2">
                    <Label>Team scope</Label>
                    <Input placeholder="Platform · 4 designers · 10 engineers" />
                  </div>
                  <div class="grid gap-2">
                    <Label>Key tools</Label>
                    <Input placeholder="Figma · FigJam · Looker · Amplitude" />
                  </div>
                  <div class="grid gap-2">
                    <Label>Primary focus</Label>
                    <textarea
                      rows="3"
                      class="rounded-xl border border-border/60 bg-muted/30 px-3 py-2 text-xs text-muted-foreground transition focus:border-brand focus:ring-2 focus:ring-brand/30"
                      placeholder="Led analytics workflows, improved cross-team collaboration, defined product rituals."
                    />
                  </div>
                </CardContent>
              </Card>
            </div>
          </CollapsibleContent>
        </Collapsible>
      </div>
    </div>
  </SectionPanel>
</template>

