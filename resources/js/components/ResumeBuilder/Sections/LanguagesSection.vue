<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import SectionPanel from './SectionPanel.vue'
import { ref } from 'vue'
import { Globe2, Plus } from 'lucide-vue-next'

interface Language {
  id: string
  name: string
  fluency: 'Native' | 'Fluent' | 'Professional' | 'Conversational'
  certification?: string
}

const languages = ref<Language[]>([
  { id: crypto.randomUUID(), name: 'English', fluency: 'Native' },
  { id: crypto.randomUUID(), name: 'Afrikaans', fluency: 'Fluent' },
  { id: crypto.randomUUID(), name: 'French', fluency: 'Professional', certification: 'DELF B2 (2022)' },
])

const fluencyPalette: Record<Language['fluency'], string> = {
  Native: 'bg-emerald-500',
  Fluent: 'bg-sky-500',
  Professional: 'bg-indigo-500',
  Conversational: 'bg-amber-500',
}

function addLanguage() {
  languages.value.push({
    id: crypto.randomUUID(),
    name: '',
    fluency: 'Conversational',
  })
}
</script>

<template>
  <SectionPanel
    title="Languages"
    description="Showcase the languages you’re comfortable working in. Certifications help validate proficiency."
    :actionable="false"
  >
    <div class="grid gap-4">
      <Card class="resume-card p-0">
        <CardContent class="divide-y divide-border/15 p-0">
          <div
            v-for="language in languages"
            :key="language.id"
            class="grid gap-3 px-5 py-4 sm:grid-cols-[minmax(0,1fr),220px]"
          >
            <div class="grid gap-3">
              <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                  <Globe2 class="size-4 text-brand" />
                  <Input v-model="language.name" placeholder="Language" class="h-9 w-auto bg-card" />
                </div>
                <Badge class="rounded-full px-3 py-1 text-[11px] uppercase tracking-wide">
                  {{ language.fluency }}
                </Badge>
              </div>

              <div class="h-2 w-full overflow-hidden rounded-full bg-background/70">
                <div
                  class="h-full rounded-full transition-all"
                  :class="fluencyPalette[language.fluency]"
                  :style="{
                    width:
                      language.fluency === 'Native'
                        ? '100%'
                        : language.fluency === 'Fluent'
                          ? '85%'
                          : language.fluency === 'Professional'
                            ? '70%'
                            : '50%',
                  }"
                />
              </div>
            </div>

            <div class="grid gap-2">
              <Label>Certification or notes</Label>
              <Input v-model="language.certification" placeholder="e.g. TOEFL 118/120 · 2023" />
            </div>
          </div>
        </CardContent>
      </Card>

      <Button variant="outline" size="sm" class="w-fit gap-2" @click="addLanguage">
        <Plus class="size-4" />
        Add language
      </Button>
    </div>
  </SectionPanel>
</template>

