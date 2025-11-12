<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import SectionPanel from './SectionPanel.vue'
import { computed, reactive, ref } from 'vue'
import { Equal, Plus } from 'lucide-vue-next'

interface SkillItem {
  id: string
  name: string
  level: 'advanced' | 'intermediate' | 'familiar'
  category: 'Design' | 'Research' | 'Leadership' | 'Technical'
}

const skills = reactive<SkillItem[]>([
  { id: crypto.randomUUID(), name: 'Design systems', level: 'advanced', category: 'Design' },
  { id: crypto.randomUUID(), name: 'Prototyping', level: 'advanced', category: 'Design' },
  { id: crypto.randomUUID(), name: 'UX research', level: 'intermediate', category: 'Research' },
  { id: crypto.randomUUID(), name: 'Workshop facilitation', level: 'advanced', category: 'Leadership' },
  { id: crypto.randomUUID(), name: 'Stakeholder alignment', level: 'advanced', category: 'Leadership' },
  { id: crypto.randomUUID(), name: 'SQL', level: 'familiar', category: 'Technical' },
  { id: crypto.randomUUID(), name: 'Storybook', level: 'intermediate', category: 'Technical' },
])

const newSkill = ref({
  name: '',
  category: 'Design' as SkillItem['category'],
  level: 'intermediate' as SkillItem['level'],
})

const categories = computed(() => {
  const grouped: Record<string, SkillItem[]> = {}
  for (const skill of skills) {
    grouped[skill.category] ??= []
    grouped[skill.category].push(skill)
  }

  return grouped
})

function addSkill() {
  if (!newSkill.value.name.trim()) {
    return
  }

  skills.push({
    id: crypto.randomUUID(),
    name: newSkill.value.name.trim(),
    category: newSkill.value.category,
    level: newSkill.value.level,
  })

  newSkill.value = {
    name: '',
    category: 'Design',
    level: 'intermediate',
  }
}
</script>

<template>
  <SectionPanel
    title="Skill matrix"
    description="Group your skills into meaningful categories. Showing depth and breadth together helps employers understand your range."
  >
    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr),320px]">
      <div class="grid gap-6">
        <Card class="resume-card p-4">
          <div class="flex flex-wrap items-center gap-2">
            <Badge variant="outline" class="border-dashed uppercase">
              Framework
            </Badge>
            <p class="text-sm text-muted-foreground">
              Use 3–4 categories and 4–6 skills per category for a balanced matrix.
            </p>
          </div>
        </Card>

        <div class="grid gap-4">
          <Card
            v-for="(items, category) in categories"
            :key="category"
            class="resume-card p-0"
          >
            <CardHeader class="flex flex-col gap-2 border-b border-border/20 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
              <CardTitle class="text-base font-semibold text-foreground">{{ category }}</CardTitle>
              <div class="flex items-center gap-2 text-xs text-muted-foreground">
                <Equal class="size-4 text-primary" />
                Balance between advanced and emerging skills.
              </div>
            </CardHeader>
            <CardContent class="p-0">
              <ul class="divide-y divide-border/20">
                <li
                  v-for="skill in items"
                  :key="skill.id"
                  class="flex flex-wrap items-center justify-between gap-3 px-5 py-4"
                >
                  <p class="text-sm font-medium">{{ skill.name }}</p>
                  <Badge
                    :variant="skill.level === 'advanced' ? 'secondary' : skill.level === 'intermediate' ? 'outline' : 'ghost'"
                    class="rounded-full px-3 py-1 text-[11px] uppercase tracking-wide"
                  >
                    {{ skill.level }}
                  </Badge>
                </li>
              </ul>
            </CardContent>
          </Card>
        </div>
      </div>

      <aside class="grid gap-4">
        <Card class="resume-card p-4">
          <p class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">
            Add a new skill
          </p>
          <div class="grid gap-3 rounded-xl border border-border/20 bg-card p-4">
            <div class="grid gap-2">
              <Label for="skill-name">Skill name</Label>
              <Input
                id="skill-name"
                v-model="newSkill.name"
                placeholder="Service design, Figma, Remote discovery..."
              />
            </div>

            <div class="grid gap-2">
              <Label>Category</Label>
              <div class="grid grid-cols-2 gap-2">
                <Button
                  v-for="category in ['Design', 'Research', 'Leadership', 'Technical']"
                  :key="category"
                  variant="ghost"
                  class="rounded-xl border border-transparent px-3 py-3 text-sm transition hover:border-border/80 hover:bg-muted/20 focus-visible:border-ring"
                  :data-state="newSkill.category === category ? 'active' : 'inactive'"
                  @click="newSkill.category = category as SkillItem['category']"
                >
                  {{ category }}
                </Button>
              </div>
            </div>

            <div class="grid gap-2">
              <Label>Proficiency</Label>
              <div class="flex flex-wrap gap-2">
                <Badge
                  v-for="level in ['advanced', 'intermediate', 'familiar']"
                  :key="level"
                  :variant="newSkill.level === level ? 'secondary' : 'outline'"
                  class="cursor-pointer rounded-full px-3 py-1 text-[11px] uppercase tracking-wide"
                  @click="newSkill.level = level as SkillItem['level']"
                >
                  {{ level }}
                </Badge>
              </div>
            </div>

            <Button class="gap-2" @click="addSkill">
              <Plus class="size-4 text-primary" />
              Add skill
            </Button>
          </div>
        </Card>

        <Card class="resume-card p-4 text-xs text-muted-foreground">
          <p class="mb-2 font-medium text-foreground">Tip</p>
          Group complementary skills together (e.g. “Design systems” + “Storybook”) to tell a cohesive story.
        </Card>
      </aside>
    </div>
  </SectionPanel>
</template>

