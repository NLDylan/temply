<script setup lang="ts">
import SectionPanel from './SectionPanel.vue'
import { ref } from 'vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Label } from '@/components/ui/label'
import { Input } from '@/components/ui/input'

const summary = ref('')
const highlightOne = ref('')
const highlightTwo = ref('')
const highlightThree = ref('')
</script>

<template>
  <SectionPanel
    title="Professional summary"
    description="Position yourself as the solution to an employer’s problem. Focus on outcomes, differentiators, and the value you bring."
    :status="summary ? 'draft' : 'attention'"
  >
    <div class="grid gap-6">
      <div class="grid gap-4">
        <div class="grid gap-2">
          <Label for="summary">Elevator pitch</Label>
          <textarea
            id="summary"
            v-model="summary"
            rows="6"
            class="rounded-2xl border border-border/35 bg-white px-4 py-3 text-sm leading-relaxed outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/30 dark:bg-zinc-900"
            placeholder="Senior product designer with 8+ years shaping data-rich experiences for SaaS platforms..."
          />
        </div>

        <Card class="resume-card p-0">
          <CardHeader class="px-4 py-4">
            <p class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">
              Key proof points
            </p>
          </CardHeader>
          <CardContent class="grid gap-3 px-4 pb-4 md:grid-cols-3">
            <div class="grid gap-2">
              <Label for="highlight-one">Strength</Label>
              <Input
                id="highlight-one"
                v-model="highlightOne"
                placeholder="Design systems that scale to 5+ squads"
              />
            </div>
            <div class="grid gap-2">
              <Label for="highlight-two">Impact</Label>
              <Input
                id="highlight-two"
                v-model="highlightTwo"
                placeholder="Increased engagement by 30% across key journeys"
              />
            </div>
            <div class="grid gap-2">
              <Label for="highlight-three">Audience</Label>
              <Input
                id="highlight-three"
                v-model="highlightThree"
                placeholder="Trusted partner to product, data, and research leads"
              />
            </div>
          </CardContent>
        </Card>
      </div>

      <Card class="resume-card p-0 text-xs text-muted-foreground">
        <CardHeader class="px-4 py-4">
          <CardTitle class="text-sm font-medium text-foreground">Guidelines</CardTitle>
        </CardHeader>
        <CardContent class="px-4 pb-4">
          <ul class="space-y-1 list-disc pl-4">
          <li>Lead with your title, experience, and specialty.</li>
          <li>Mention 2–3 strengths supported by measurable outcomes.</li>
          <li>Highlight a signature achievement that shows the scale you operate at.</li>
          <li>Finish with the type of roles or impact you’re targeting next.</li>
          </ul>
        </CardContent>
      </Card>
    </div>
  </SectionPanel>
</template>

