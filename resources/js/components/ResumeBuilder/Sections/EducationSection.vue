<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import SectionPanel from './SectionPanel.vue'
import { ref } from 'vue'
import { GraduationCap, Plus } from 'lucide-vue-next'

interface EducationEntry {
  id: string
  school: string
  credential: string
  focus: string
  start: string
  end: string
  honors?: string
}

const education = ref<EducationEntry[]>([
  {
    id: crypto.randomUUID(),
    school: 'University of Cape Town',
    credential: 'Bachelor of Arts',
    focus: 'Interaction Design & Psychology',
    start: '2013',
    end: '2016',
    honors: 'Graduated with distinction · Dean’s List',
  },
  {
    id: crypto.randomUUID(),
    school: 'General Assembly',
    credential: 'Product Design Immersive',
    focus: 'UI/UX · Design Systems · DesignOps',
    start: '2017',
    end: '2017',
  },
])

function addEducation() {
  education.value = [
    {
      id: crypto.randomUUID(),
      school: '',
      credential: '',
      focus: '',
      start: '',
      end: '',
      honors: '',
    },
    ...education.value,
  ]
}
</script>

<template>
  <SectionPanel
    title="Education"
    description="Showcase academic foundations, specializations, and recognitions that reinforce your expertise."
  >
    <div class="grid gap-6">
      <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 text-sm font-medium text-muted-foreground">
          <GraduationCap class="size-4 text-primary" />
          Highlight your most relevant education first.
        </div>
        <Button variant="outline" size="sm" class="gap-2" @click="addEducation">
          <Plus class="size-4 text-primary" />
          Add education
        </Button>
      </div>

      <div class="grid gap-4">
        <Card
          v-for="entry in education"
          :key="entry.id"
          class="resume-card p-0 transition-colors duration-200 hover:border-border/70"
        >
          <CardHeader class="flex flex-col gap-3 border-b border-border/20 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <CardTitle class="text-base font-semibold text-foreground">{{ entry.school }}</CardTitle>
              <CardDescription>
                {{ entry.credential }} · {{ entry.focus }}
              </CardDescription>
            </div>
            <div class="flex items-center gap-2 text-xs text-muted-foreground">
              <span class="rounded-full bg-muted/20 px-2 py-1">
                {{ entry.start }} — {{ entry.end }}
              </span>
            </div>
          </CardHeader>
          <CardContent class="grid gap-4 px-5 py-4">
            <div class="grid gap-4 md:grid-cols-2">
              <div class="grid gap-2">
                <Label>Credential / Degree</Label>
                <Input v-model="entry.credential" />
              </div>
              <div class="grid gap-2">
                <Label>Field of study</Label>
                <Input v-model="entry.focus" />
              </div>
              <div class="grid gap-2">
                <Label>Start year</Label>
                <Input v-model="entry.start" />
              </div>
              <div class="grid gap-2">
                <Label>Completion year</Label>
                <Input v-model="entry.end" />
              </div>
            </div>

            <div class="grid gap-2">
              <Label>Honors & activities</Label>
              <textarea
                v-model="entry.honors"
                class="rounded-xl border border-border/35 bg-white px-3 py-2 text-sm text-muted-foreground transition focus:border-ring focus:ring-2 focus:ring-ring/30 dark:bg-zinc-900"
                rows="3"
                :placeholder="entry.honors ? '' : 'Add scholarships, leadership roles, societies, or awards.'"
              />
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </SectionPanel>
</template>

