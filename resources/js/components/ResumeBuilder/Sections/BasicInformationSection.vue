<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import SectionPanel from './SectionPanel.vue'
import { ref } from 'vue'
import { Link2, Plus, Trash2, UploadCloud } from 'lucide-vue-next'

const contactLinks = ref([
  { id: crypto.randomUUID(), label: 'Portfolio', value: 'https://dylanjonker.com' },
  { id: crypto.randomUUID(), label: 'LinkedIn', value: 'linkedin.com/in/dylanjonker' },
])

function addContactLink() {
  contactLinks.value.push({
    id: crypto.randomUUID(),
    label: '',
    value: '',
  })
}

function removeContactLink(id: string) {
  contactLinks.value = contactLinks.value.filter((link) => link.id !== id)
}
</script>

<template>
  <SectionPanel
    title="Basic information"
    description="Introduce yourself with a strong first impression. This information will anchor the rest of your resume."
    hint="Keep your headline aligned with the role you're targeting. Your contact details should be easy to scan at a glance."
  >
    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr),280px]">
      <div class="grid gap-6">
        <div class="grid gap-4 md:grid-cols-2">
          <div class="grid gap-2">
            <Label for="full-name">Full name</Label>
            <Input id="full-name" placeholder="Jordan Walker" autocomplete="name" />
          </div>
          <div class="grid gap-2">
            <Label for="headline">Headline / Role</Label>
            <Input id="headline" placeholder="Senior Product Designer" />
          </div>
          <div class="grid gap-2">
            <Label for="email">Email</Label>
            <Input id="email" type="email" placeholder="hello@yourdomain.com" autocomplete="email" />
          </div>
          <div class="grid gap-2">
            <Label for="phone">Phone</Label>
            <Input id="phone" type="tel" placeholder="+1 555 000 1234" autocomplete="tel" />
          </div>
          <div class="grid gap-2">
            <Label for="location">Location</Label>
            <Input id="location" placeholder="Remote · Cape Town, ZA" autocomplete="address-level2" />
          </div>
          <div class="grid gap-2">
            <Label for="working-rights">Working rights</Label>
            <Input id="working-rights" placeholder="Eligible to work in South Africa & EU" />
          </div>
        </div>

        <Card class="resume-card p-0">
          <CardHeader class="flex flex-row items-center justify-between gap-2 px-4 py-4">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">
              Contact links
            </h3>
            <Button variant="ghost" size="sm" class="h-8 gap-1 px-3 text-[11px] uppercase tracking-wide" @click="addContactLink">
              <Plus class="size-3.5" />
              Add
            </Button>
          </CardHeader>

          <CardContent class="divide-y divide-border/20 p-0">
            <div
              v-for="link in contactLinks"
              :key="link.id"
              class="grid gap-4 px-4 py-4 md:grid-cols-[160px,1fr,auto] md:items-end"
            >
              <div class="grid gap-2">
                <Label>Label</Label>
                <Input v-model="link.label" placeholder="LinkedIn" />
              </div>
              <div class="grid gap-2">
                <Label>URL</Label>
                <div class="relative">
                  <Input v-model="link.value" placeholder="https://linkedin.com/in/username" class="pl-9" />
                  <Link2 class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-muted-foreground/70" />
                </div>
              </div>
              <Button
                variant="ghost"
                size="icon"
                class="h-9 w-9 justify-self-start text-muted-foreground md:justify-self-end"
                @click="removeContactLink(link.id)"
              >
                <Trash2 class="size-4" />
                <span class="sr-only">Remove link</span>
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>

      <Card class="resume-card p-0">
        <CardHeader class="flex flex-col gap-2 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
          <p class="text-sm font-medium">Profile photo</p>
          <Button variant="ghost" size="sm" class="gap-2">
            <UploadCloud class="size-4" />
            Upload
          </Button>
        </CardHeader>
        <CardContent class="grid gap-4 px-4 pb-4">
          <p class="text-xs text-muted-foreground">
            A friendly, professional headshot increases response rates. Use a neutral background and natural lighting.
          </p>

          <div class="flex flex-col items-center gap-3 rounded-xl border border-border/40 bg-card px-4 py-6">
            <Avatar class="h-20 w-20 rounded-2xl">
              <AvatarImage src="https://i.pravatar.cc/200?img=32" alt="Profile preview" />
              <AvatarFallback class="rounded-2xl text-sm font-semibold uppercase">JW</AvatarFallback>
            </Avatar>
            <Button variant="outline" size="sm" class="gap-2">
              <UploadCloud class="size-4" />
              Replace photo
            </Button>
            <p class="text-[11px] text-muted-foreground">
              Minimum 400x400px · PNG or JPG
            </p>
          </div>
        </CardContent>
      </Card>
    </div>
  </SectionPanel>
</template>

